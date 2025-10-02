<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\MongoCart;
use App\Models\MongoOrder;
use App\Models\MongoOrderItem;
use MongoDB\BSON\ObjectId;

class PaymentController extends Controller
{
    /* ---------- helpers: read-only and robust ---------- */

    /** Do NOT auto-create here; for POST we only want the real open cart. */
    protected function findOpenCartForUser(string $userId): ?MongoCart
    {
        return MongoCart::where('user_id', $userId)
            ->where('status', 'open')
            ->first();
    }

    /** Always return a Collection of line items without mutating the cart. */
    protected function items(MongoCart $cart): Collection
    {
        $raw = $cart->getAttribute('items');

        if ($raw instanceof Collection) return $raw;
        if (is_array($raw))             return collect($raw);
        if (is_object($raw))            return collect((array) $raw)->values();

        return collect(); // nothing/unknown
    }

    /** Atomic increment of sold_pieces in Mongo products. */
    protected function incrementSoldPieces(string $productId, int $qty): void
    {
        try {
            $qty = max(0, (int) $qty);
            if ($qty <= 0) return;

            $col = DB::connection('mongodb')->getMongoDB()->selectCollection('products');

            $filter = preg_match('/^[a-f0-9]{24}$/i', $productId)
                ? ['_id' => new ObjectId($productId)]
                : ['_id' => $productId];

            $col->updateOne($filter, ['$inc' => ['sold_pieces' => $qty]]);
        } catch (\Throwable $e) {
            // Optional log
        }
    }

    /** Atomic decrement of inventory in Mongo products. */
    protected function decrementInventory(string $productId, int $qty): void
    {
        try {
            $qty = max(0, (int) $qty);
            if ($qty <= 0) return;

            $col = DB::connection('mongodb')->getMongoDB()->selectCollection('products');

            $filter = preg_match('/^[a-f0-9]{24}$/i', $productId)
                ? ['_id' => new ObjectId($productId)]
                : ['_id' => $productId];

            // Don’t allow negatives; only decrement if enough stock
            $col->updateOne(
                array_merge($filter, ['inventory' => ['$gte' => $qty]]),
                ['$inc' => ['inventory' => -$qty]]
            );

            // If now <=0, mark as Out of Stock
            $col->updateOne(
                array_merge($filter, ['inventory' => ['$lte' => 0]]),
                ['$set' => ['inventory' => 0, 'status' => 'Out of Stock']]
            );
        } catch (\Throwable $e) {
            // Optional log
        }
    }

    /** Recompute totals from the normalized items view (no mutation of items). */
    protected function recomputeTotals(MongoCart $cart): void
    {
        $qty = 0;
        $sum = 0.0;

        foreach ($this->items($cart) as $it) {
            $qty += (int) data_get($it, 'quantity', 0);
            $sum += (float) data_get($it, 'total', 0);
        }

        $cart->quantity = $qty;
        $cart->total    = round($sum, 2);
        $cart->save();
    }

    /* ---------- pages ---------- */

    public function show(Request $request, $orderId = null)
    {
        if ($orderId) {
            // Direct Buy Now order
            $order = MongoOrder::with('items')->find($orderId);
            if (!$order) {
                return redirect()->route('products')->with('error', 'Order not found.');
            }

            return view('checkout', [
                'order' => $order,
                'mode'  => 'buy-now',
            ]);
        }

        // Default: checkout using cart
        $userId = (string) Auth::id();
        $cart   = $this->findOpenCartForUser($userId);

        return view('checkout', [
            'cart' => $cart, // can be null; the Blade handles it
            'mode' => 'cart',
        ]);
    }

    public function process(Request $request)
    {
        // Basic validation for card + address
        $request->validate([
            'card_name'   => 'required|string|max:255',
            'card_number' => 'required|string|min:12|max:19',
            'exp_month'   => 'required|integer|min:1|max:12',
            'exp_year'    => 'required|integer|min:' . date('Y') . '|max:' . (date('Y') + 15),
            'cvc'         => 'required|string|min:3|max:4',
            'address'     => 'nullable|string|max:500',
            'mode'        => 'nullable|in:cart,buy-now',
            'orderId'     => 'nullable|string',
        ]);

        $userId = (string) Auth::id();
        $mode   = $request->input('mode', 'cart'); // cart OR buy-now

        if ($mode === 'buy-now' && $request->filled('orderId')) {
            // ✅ Buy Now: order already created in CartController::buyNow
            $order = MongoOrder::find($request->orderId);
            if (!$order) {
                return redirect()->route('products')->with('error', 'Order not found.');
            }

            // Mark order as completed (or paid) and save address
            $order->status   = 'paid';
            $order->location = (string) $request->input('address', '');
            $order->save();

            return redirect()->route('order.thanks', (string) $order->_id)
                             ->with('success', 'Payment successful for Buy Now order!');
        }

        // ✅ Default cart checkout
        $cart = $this->findOpenCartForUser($userId);

        if (! $cart) {
            return redirect()->route('cart.show')
                ->with('error', 'Your cart session expired. Please review your cart and try again.');
        }

        $this->recomputeTotals($cart);
        $items = $this->items($cart);

        if ($items->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Your cart is empty.');
        }

        // Create order from cart
            $order = new MongoOrder([
            'user_id'  => $cart->user_id,
            'status'   => 'pending',   // 👈 default new order state
            'location' => (string) $request->input('address', ''),
            'total'    => $cart->total,
            'quantity' => $cart->quantity,
        ]);

        $order->save();

        foreach ($items as $ci) {
            $order->items()->save(new MongoOrderItem([
                'product_id' => (string) data_get($ci, 'product_id'),
                'name'       => (string) data_get($ci, 'name', '—'),
                'price'      => (float)  data_get($ci, 'price', 0),
                'quantity'   => (int)    data_get($ci, 'quantity', 0),
                'image'      =>          data_get($ci, 'image'),
                'total'      => (float)  data_get($ci, 'total', 0),
            ]));

            // Update product stats
            $this->incrementSoldPieces(
                (string) data_get($ci, 'product_id'),
                (int)    data_get($ci, 'quantity', 0)
            );

            $this->decrementInventory(
                (string) data_get($ci, 'product_id'),
                (int)    data_get($ci, 'quantity', 0)
            );
        }

        // Close the cart
        $cart->status = 'closed';
        $cart->items  = [];
        $cart->save();

        return redirect()->route('order.thanks', (string) $order->_id)
                         ->with('success', 'Payment successful for cart checkout!');
    }
}
