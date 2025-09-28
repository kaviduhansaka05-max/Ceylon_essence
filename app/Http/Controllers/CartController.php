<?php

namespace App\Http\Controllers;

use App\Models\MongoCart;
use App\Models\MongoCartItem;
use App\Models\MongoProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\ObjectId;

class CartController extends Controller
{
    /* ======================
     * CORE
     * ====================== */
    protected function getOpenCartForUser(string $userId): MongoCart
    {
        $cart = MongoCart::where('user_id', $userId)->where('status', 'open')->first();

        if (!$cart) {
            $cart = new MongoCart([
                'user_id'     => $userId,
                'status'      => 'open',
                'items'       => [],
                'total'       => 0,
                'quantity'    => 0,
                'discount'    => 0,
                'grand_total' => 0,
            ]);
            $cart->save();
        }
        return $cart;
    }

    protected function incrementSoldPieces(string $productId, int $qty): void
    {
        try {
            $col = DB::connection('mongodb')->getMongoDB()->selectCollection('products');
            $filter = preg_match('/^[a-f0-9]{24}$/i', $productId)
                ? ['_id' => new ObjectId($productId)]
                : ['_id' => $productId];
            $col->updateOne($filter, ['$inc' => ['sold_pieces' => $qty]]);
        } catch (\Throwable $e) {}
    }

    protected function recomputeTotals(MongoCart $cart): void
    {
        $qty = 0;
        $sum = 0.0;

        foreach ($cart->items as $it) {
            $it->quantity = (int) ($it->quantity ?? 1);
            $it->price    = (float) ($it->price ?? 0);
            $it->total    = round($it->price * $it->quantity, 2);

            $qty += $it->quantity;
            $sum += $it->total;

            $cart->items()->save($it);
        }

        $cart->quantity    = $qty;
        $cart->total       = round($sum, 2);
        $cart->grand_total = $cart->total;
        $cart->save();
    }

    /* ======================
     * REGULAR CART
     * ====================== */
    public function show()
    {
        $cart = $this->getOpenCartForUser((string) Auth::id());
        $this->recomputeTotals($cart);
        return view('cart', ['cart' => $cart]);
    }
public function webApplyPromo(Request $request)
{
    $request->validate([
        'code' => 'required|string|max:50',
    ]);

    $cart = $this->getOpenCartForUser((string) Auth::id());
    $code = trim($request->code);

    // Look in MySQL promos table
    $promo = DB::table('promos')
        ->where('code', $code)
        ->where('active', true)
        ->first();

    if (!$promo) {
        return back()->with('error', 'Invalid promo code');
    }

    // Expiry
    if (!empty($promo->expires_at) && strtotime($promo->expires_at) < time()) {
        return back()->with('error', 'This promo has expired');
    }

    // Minimum order
    $min = (float)($promo->min ?? 0);
    if ($cart->total < $min) {
        return back()->with('error', "Minimum order amount is $" . number_format($min, 2));
    }

    // Discount
    $discount = 0;
    if ($promo->type === 'percent') {
        $discount = round($cart->total * ((float)$promo->amount / 100), 2);
    } elseif ($promo->type === 'flat') {
        $discount = min((float)$promo->amount, $cart->total);
    }

    $cart->promo_code  = $promo->code;
    $cart->discount    = $discount;
    $cart->grand_total = $cart->total - $discount;
    $cart->save();

    return back()->with('success', 'Promo code applied!');
}

public function webRemovePromo()
{
    $cart = $this->getOpenCartForUser((string) Auth::id());

    $cart->promo_code = null;
    $cart->discount = 0;
    $cart->grand_total = $cart->total;
    $cart->save();

    return back()->with('success', 'Promo code removed');
}

    public function webAdd(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $userId = (string) Auth::id();
        $cart   = $this->getOpenCartForUser($userId);

        $product = MongoProduct::find($request->product_id);
        if (!$product) {
            return back()->with('error', 'Product not found');
        }

        // add or increase existing
        $existing = $cart->items->firstWhere('product_id', (string) $product->_id);
        if ($existing) {
            $existing->quantity += $request->quantity ?? 1;
            $existing->total = $existing->price * $existing->quantity;
            $cart->items()->save($existing);
        } else {
            $cart->items()->save(new MongoCartItem([
                'product_id' => (string) $product->_id,
                'name'       => $product->name,
                'price'      => (float) $product->price,
                'quantity'   => $request->quantity ?? 1,
                'image'      => $product->image,
                'status'     => $product->status,
                'total'      => (float) $product->price * ($request->quantity ?? 1),
            ]));
        }

        $this->recomputeTotals($cart);
        return redirect()->route('cart.show')->with('success', 'Added to cart');
    }

    public function webUpdateQty(Request $request)
{
    $request->validate([
        'product_id' => 'required|string',
        'quantity'   => 'nullable|integer|min:1',
        'op'         => 'nullable|string|in:inc,dec', // optional
    ]);

    $cart = $this->getOpenCartForUser((string) Auth::id());

    // Find item
    $item = $cart->items->firstWhere('product_id', $request->product_id);

    if ($item) {
        $qty = (int) ($item->quantity ?? 1);

        // If "op" is set (clicked +/− button)
        if ($request->op === 'inc') {
            $qty++;
        } elseif ($request->op === 'dec') {
            $qty = max(1, $qty - 1); // don’t go below 1
        } elseif ($request->filled('quantity')) {
            $qty = max(1, (int) $request->quantity); // direct input box
        }

        $item->quantity = $qty;
        $item->total    = $item->price * $qty;
        $cart->items()->save($item);

        $this->recomputeTotals($cart);

        return back()->with('success', 'Cart updated');
    }

    return back()->with('error', 'Item not found in cart');
}

    public function webRemove(Request $request)
    {
        $cart = $this->getOpenCartForUser((string) Auth::id());
        $cart->items = $cart->items->reject(fn($i) => $i->product_id === $request->product_id)->values()->all();
        $this->recomputeTotals($cart);
        return back()->with('success', 'Removed from cart');
    }

    
    public function webClear()
    {
        $cart = $this->getOpenCartForUser((string) Auth::id());
        $cart->items = [];
        $this->recomputeTotals($cart);
        return back()->with('success', 'Cart cleared');
    }

    /* ======================
     * BUY NOW (special flow)
     * ====================== */
    public function buyNow(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|string',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = MongoProduct::find($data['product_id']);
        if (!$product) {
            return back()->with('error', 'Product not found.');
        }

        $userId = (string) Auth::id();
        $cart = $this->getOpenCartForUser($userId);

        // clear cart first
        $cart->items()->delete();
        $cart->items = [];

        // add the one product
        $cart->items()->save(new MongoCartItem([
            'product_id' => (string) $product->_id,
            'name'       => $product->name,
            'price'      => (float) $product->price,
            'quantity'   => $data['quantity'],
            'image'      => $product->image,
            'status'     => $product->status,
            'total'      => (float) $product->price * $data['quantity'],
        ]));

        $this->recomputeTotals($cart);

        // straight to checkout
        return redirect()->route('checkout.show');
    }
}
