<?php

namespace App\Http\Controllers;

use App\Models\MongoCart;
use App\Models\MongoCartItem;
use App\Models\MongoOrder;
use App\Models\MongoOrderItem;
use App\Models\MongoProduct;
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
                'user_id'  => $userId,
                'status'   => 'open',
                'items'    => [],
                'total'    => 0,
                'quantity' => 0,
            ]);
            $cart->save();
        }

        // ⬇️ Make sure items is ALWAYS an array in Mongo
        $this->ensureItemsArray($cart);

        return $cart;
    }

    /**
     * Coerce items into a plain PHP array and persist if needed.
     */
    private function ensureItemsArray(MongoCart $cart): void
    {
        // When embedded relation is present it's usually an EloquentEmbeddedCollection,
        // but if at some point a Collection or stdClass was assigned directly to items
        // it can serialize as an object {} in Mongo. Guard against that here.
        $raw = $cart->getAttribute('items');

        if (is_object($raw)) {
            // Broken doc from past writes → reset to empty array
            $cart->items = [];
            $cart->save();
            return;
        }

        // If it's a Laravel Collection, convert to array before saving again
        if ($raw instanceof \Illuminate\Support\Collection) {
            $cart->items = $raw->values()->all();
            $cart->save();
        }
    }

    protected function recomputeTotals(MongoCart $cart): void
    {
        $this->ensureItemsArray($cart);

        $qty = 0;
        $sum = 0.0;
        foreach ($cart->items as $it) {
            $qty += (int) $it->quantity;
            $sum += (float) $it->total;
        }
        $cart->quantity = $qty;
        $cart->total    = round($sum, 2);
        $cart->save();
    }

    private function coreAdd(string $userId, string $productId, int $qty): ?MongoCart
    {
        $cart = $this->getOpenCartForUser($userId);
        $prod = MongoProduct::where('_id', $productId)->first();
        if (!$prod) return null;

        // make sure items is array BEFORE embedsMany save ($addToSet)
        $this->ensureItemsArray($cart);

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
        if (!$item) return false;

        $item->quantity = $qty; // embedded doc mutator recomputes line total
        $this->recomputeTotals($cart);
        return true;
    }

    private function coreRemove(string $userId, string $productId): bool
    {
        $cart = $this->getOpenCartForUser($userId);

        // Convert to plain array to avoid storing a Collection (would serialize as object)
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
        $cart->items = [];
        $this->recomputeTotals($cart);
    }

    private function coreCheckout(string $userId, string $location = ''): ?MongoOrder
    {
        $cart = $this->getOpenCartForUser($userId);
        if ($cart->items->isEmpty()) return null;

        $this->recomputeTotals($cart);

        $order = new MongoOrder([
            'user_id'  => $userId,
            'status'   => 'pending',
            'location' => $location,
            'total'    => $cart->total,
            'quantity' => $cart->quantity,
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
        $this->getOpenCartForUser($userId); // new empty cart

        return $order;
    }

    /* ======================
     * WEB (redirect/Blade)
     * ====================== */

    public function show()
    {
        $userId = (string) Auth::id();
        $cart   = $this->getOpenCartForUser($userId);
        $this->recomputeTotals($cart);
        return view('cart', ['cart' => $cart]);
    }

    public function webAdd(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string|size:24',
            'quantity'   => 'nullable|integer|min:1'
        ]);

        $cart = $this->coreAdd((string)Auth::id(), $request->product_id, (int)($request->quantity ?? 1));
        return redirect()->route('cart.show')->with($cart ? 'success' : 'error', $cart ? 'Added to cart' : 'Product not found');
    }

    public function webUpdateQty(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string|size:24',
            'quantity'   => 'required|integer|min:1',
            'op'         => 'nullable|in:inc,dec',
        ]);

        $userId = (string) Auth::id();
        $qty    = (int) $request->input('quantity', 1);

        // handle +/- buttons
        $op = $request->input('op');
        if ($op === 'inc') $qty++;
        if ($op === 'dec') $qty = max(1, $qty - 1);

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
        $request->validate([
            'product_id' => 'required|string|size:24',
            'quantity'   => 'required|integer|min:1',
            'op'         => 'nullable|in:inc,dec',
        ]);

        $qty = (int) $request->input('quantity', 1);
        $op  = $request->input('op');
        if ($op === 'inc') $qty++;
        if ($op === 'dec') $qty = max(1, $qty - 1);

        $ok = $this->coreUpdateQty((string)Auth::id(), $request->product_id, $qty);
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
