<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Models\MongoCart;
use App\Models\MongoOrder;
use App\Models\MongoOrderItem;

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

        if ($raw instanceof Collection)      return $raw;
        if (is_array($raw))                  return collect($raw);
        if (is_object($raw))                 return collect((array) $raw)->values();

        return collect(); // nothing/unknown
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

    public function show(Request $request)
    {
        $userId = (string) Auth::id();
        $cart   = $this->findOpenCartForUser($userId);

        if (! $cart) {
            return redirect()->route('cart.show')->with('error', 'Your cart is empty.');
        }

        $this->recomputeTotals($cart);

        if ($this->items($cart)->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Your cart is empty.');
        }

        return view('checkout', ['cart' => $cart]);
    }

    public function process(Request $request)
    {
        // demo validation; replace with your gateway’s requirements
        $request->validate([
            'card_name'   => 'required|string|max:255',
            'card_number' => 'required|string|min:12|max:19',
            'exp_month'   => 'required|integer|min:1|max:12',
            'exp_year'    => 'required|integer|min:' . date('Y') . '|max:' . (date('Y') + 15),
            'cvc'         => 'required|string|min:3|max:4',
            'address'     => 'nullable|string|max:500',
        ]);

        $userId = (string) Auth::id();
        $cart   = $this->findOpenCartForUser($userId);

        if (! $cart) {
            return redirect()->route('cart.show')->with('error', 'Your cart session expired. Please review your cart and try again.');
        }

        // ensure totals are up to date (but do not mutate items)
        $this->recomputeTotals($cart);

        $items = $this->items($cart);
        if ($items->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Your cart is empty.');
        }

        // create order
        $order = new MongoOrder([
            'user_id'  => $cart->user_id,
            'status'   => 'pending',
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
        }

        // close cart and clear its items to avoid re-using it
        $cart->status = 'closed';
        $cart->items  = []; // explicitly empty now that we placed an order
        $cart->save();

        return redirect()->route('order.thanks', (string) $order->_id);
    }
}
