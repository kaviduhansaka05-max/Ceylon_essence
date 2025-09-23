<?php

namespace App\Http\Controllers;

use App\Models\MongoCart;
use App\Models\MongoCartItem;
use App\Models\MongoOrder;
use App\Models\MongoOrderItem;
use App\Models\MongoProduct;
use App\Models\Promo; // << MySQL promos
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /* ======================
     * CORE (business logic)
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
                // promo-related (Mongo accepts dynamic fields)
                'promo_code'  => null,
                'discount'    => 0,
                'grand_total' => 0,
            ]);
            $cart->save();
        }

        return $cart;
    }

    /**
     * Recompute line totals, subtotal, discount and grand total.
     */
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

            // persist each normalized line
            $cart->items()->save($it);
        }

        $cart->quantity = $qty;
        $cart->total    = round($sum, 2);

        // ---- Promo application (if any) ----
        $discount = 0.0;

        if (!empty($cart->promo_code)) {
            $promo = Promo::where('code', strtoupper($cart->promo_code))->first();

            if ($promo && $promo->active) {
                $validMin   = $promo->min <= $cart->total;
                $validExp   = $promo->expires_at ? !$promo->expires_at->isPast() : true;

                if ($validMin && $validExp) {
                    if ($promo->type === 'percent') {
                        $discount = round($cart->total * ($promo->amount / 100), 2);
                    } else { // flat
                        $discount = round(min($promo->amount, $cart->total), 2);
                    }
                } else {
                    // auto-remove invalid/expired promos
                    $cart->promo_code = null;
                }
            } else {
                // auto-remove missing/inactive promos
                $cart->promo_code = null;
            }
        }

        $cart->discount    = round(max(0, $discount), 2);
        $cart->grand_total = round(max(0, $cart->total - $cart->discount), 2);

        $cart->save();
    }

    private function coreAdd(string $userId, string $productId, int $qty): ?MongoCart
    {
        $cart = $this->getOpenCartForUser($userId);
        $prod = MongoProduct::where('_id', $productId)->first();
        if (!$prod) return null;

        $existing = $cart->items->firstWhere('product_id', $productId);
        if ($existing) {
            $existing->quantity = $existing->quantity + $qty;
        } else {
            $cart->items()->save(new MongoCartItem([
                'product_id' => (string) $prod->_id,
                'name'       => $prod->name ?? '—',
                'price'      => (float) ($prod->price ?? 0),
                'quantity'   => $qty,
                'image'      => $prod->image ?? null,
                'status'     => $prod->status ?? 'Instock',
            ]));
        }

        $this->recomputeTotals($cart);
        return $cart;
    }

    private function coreUpdateQty(string $userId, string $productId, int $qty): bool
    {
        $cart = $this->getOpenCartForUser($userId);

        $item = $cart->items->firstWhere('product_id', $productId);
        if (! $item) return false;

        $qty = max(1, (int) $qty);
        $item->quantity = $qty;
        $item->total    = round(((float) $item->price) * $qty, 2);

        $cart->items()->save($item);

        $this->recomputeTotals($cart);
        return true;
    }

    private function coreRemove(string $userId, string $productId): bool
    {
        $cart = $this->getOpenCartForUser($userId);

        $cart->items = $cart->items
            ->reject(fn($i) => $i->product_id === $productId)
            ->values()
            ->all();

        $this->recomputeTotals($cart);
        return true;
    }

    private function coreClear(string $userId): void
    {
        $cart = $this->getOpenCartForUser($userId);
        $cart->items      = [];
        $cart->promo_code = null;
        $cart->discount   = 0;
        $cart->grand_total= 0;

        $this->recomputeTotals($cart);
    }

    private function coreCheckout(string $userId, string $location = ''): ?MongoOrder
    {
        $cart = $this->getOpenCartForUser($userId);
        if ($cart->items->isEmpty()) return null;

        $this->recomputeTotals($cart);

        $order = new MongoOrder([
            'user_id'     => $userId,
            'status'      => 'pending',
            'location'    => $location,
            'total'       => $cart->grand_total ?? $cart->total,
            'quantity'    => $cart->quantity,
            'promo_code'  => $cart->promo_code ?? null,
            'discount'    => $cart->discount ?? 0,
            'subtotal'    => $cart->total ?? 0,
        ]);
        $order->save();

        foreach ($cart->items as $ci) {
            $order->items()->save(new MongoOrderItem([
                'product_id' => $ci->product_id,
                'name'       => $ci->name,
                'price'      => $ci->price,
                'quantity'   => $ci->quantity,
                'image'      => $ci->image,
                'total'      => $ci->total,
            ]));
        }

        $cart->status = 'closed';
        $cart->save();
        $this->getOpenCartForUser($userId); // spawn a fresh cart

        return $order;
    }

    /* ======================
     * WEB (redirect/Blade)
     * ====================== */

    public function show()
    {
        $userId = (string) Auth::id();
        $cart = $this->getOpenCartForUser($userId);
        $this->recomputeTotals($cart);
        return view('cart', ['cart' => $cart]);
    }

    public function webAdd(Request $request)
    {
        $request->validate(['product_id' => 'required|string|size:24','quantity'=>'nullable|integer|min:1']);
        $cart = $this->coreAdd((string)Auth::id(), $request->product_id, (int)($request->quantity ?? 1));
        return redirect()->route('cart.show')->with($cart ? 'success' : 'error', $cart ? 'Added to cart' : 'Product not found');
    }

    public function webUpdateQty(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string|size:24',
            'quantity'   => 'nullable|integer|min:1',
            'op'         => 'nullable|in:inc,dec',
        ]);

        $userId = (string) Auth::id();
        $cart   = $this->getOpenCartForUser($userId);

        $item = $cart->items->firstWhere('product_id', $request->product_id);
        if (! $item) {
            return back()->with('error', 'Item not found');
        }

        $qty = (int) ($request->quantity ?? $item->quantity ?? 1);
        if ($request->op === 'inc') $qty = ((int) $item->quantity) + 1;
        if ($request->op === 'dec') $qty = max(1, ((int) $item->quantity) - 1);

        $ok = $this->coreUpdateQty($userId, $request->product_id, $qty);

        return back()->with($ok ? 'success' : 'error', $ok ? 'Cart updated' : 'Item not found');
    }

    public function webRemove(Request $request)
    {
        $this->coreRemove((string)Auth::id(), $request->product_id);
        return back()->with('success', 'Removed from cart');
    }

    public function webClear()
    {
        $this->coreClear((string)Auth::id());
        return back()->with('success', 'Cart cleared');
    }

    public function webCheckout(Request $request)
    {
        $order = $this->coreCheckout((string)Auth::id(), $request->location ?? '');
        return redirect()->route('cart.show')->with($order ? 'success' : 'error', $order ? 'Order placed!' : 'Cart empty');
    }

    /* ======================
     * Promo (Apply/Remove)
     * ====================== */

    public function webApplyPromo(Request $request)
    {
        $request->validate(['code' => 'required|string|max:64']);
        $code = strtoupper(trim($request->code));

        $promo = Promo::where('code', $code)->first();
        if (!$promo) {
            return back()->with('error', 'Promo code not found.');
        }
        if (!$promo->active) {
            return back()->with('error', 'Promo code is inactive.');
        }

        $cart = $this->getOpenCartForUser((string)Auth::id());
        $this->recomputeTotals($cart); // current subtotal

        if ($promo->min > 0 && $cart->total < $promo->min) {
            return back()->with('error', 'Order does not meet the minimum for this code.');
        }
        if ($promo->expires_at && $promo->expires_at->isPast()) {
            return back()->with('error', 'Promo code has expired.');
        }

        // compute discount against current subtotal
        $discount = 0.0;
        if ($promo->type === 'percent') {
            $discount = round($cart->total * ($promo->amount / 100), 2);
        } else {
            $discount = round(min($promo->amount, $cart->total), 2);
        }

        $cart->promo_code  = $promo->code;
        $cart->discount    = $discount;
        $cart->grand_total = round(max(0, $cart->total - $discount), 2);
        $cart->save();

        return back()->with('success', "Code applied: {$promo->code} (−$".number_format($discount,2).')');
    }

    public function webRemovePromo(Request $request)
    {
        $cart = $this->getOpenCartForUser((string)Auth::id());
        $cart->promo_code = null;
        $cart->discount   = 0;
        $cart->grand_total= 0;
        $this->recomputeTotals($cart);

        return back()->with('success', 'Promo code removed.');
    }

    /* ======================
     * API (JSON)
     * ====================== */

    public function apiShow()
    {
        $cart = $this->getOpenCartForUser((string)Auth::id());
        $this->recomputeTotals($cart);
        return response()->json($cart);
    }

    public function apiAdd(Request $request)
    {
        $request->validate(['product_id'=>'required|string|size:24','quantity'=>'nullable|integer|min:1']);
        $cart = $this->coreAdd((string)Auth::id(), $request->product_id, (int)($request->quantity ?? 1));
        return $cart ? response()->json(['ok'=>true,'cart'=>$cart]) : response()->json(['ok'=>false,'message'=>'Product not found'],404);
    }

    public function apiUpdateQty(Request $request)
    {
        $ok = $this->coreUpdateQty((string)Auth::id(), $request->product_id, (int)$request->quantity);
        return $ok ? response()->json(['ok'=>true]) : response()->json(['ok'=>false,'message'=>'Item not found'],404);
    }

    public function apiRemove(Request $request)
    {
        $this->coreRemove((string)Auth::id(), $request->product_id);
        return response()->json(['ok'=>true]);
    }

    public function apiClear()
    {
        $this->coreClear((string)Auth::id());
        return response()->json(['ok'=>true]);
    }

    public function apiCheckout(Request $request)
    {
        $order = $this->coreCheckout((string)Auth::id(), $request->location ?? '');
        return $order ? response()->json(['ok'=>true,'order'=>$order]) : response()->json(['ok'=>false,'message'=>'Cart empty'],400);
    }
}
