<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl">Your Cart</h2>
  </x-slot>

  <div class="py-8 max-w-5xl mx-auto px-4">

    {{-- flash messages --}}
    @if (session('success'))
      <div class="mb-4 rounded-lg bg-emerald-100 text-emerald-800 px-4 py-2">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="mb-4 rounded-lg bg-rose-100 text-rose-800 px-4 py-2">{{ session('error') }}</div>
    @endif

    {{-- STEP PROGRESS (simple + subtle) --}}
    <nav class="mb-6">
      <ol class="flex items-center gap-6 text-sm text-gray-500">
        <li class="flex items-center gap-2">
          <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-500 text-white text-xs">1</span>
          <span class="font-semibold text-gray-900">Cart</span>
        </li>
        <li class="h-px w-16 bg-gray-200"></li>
        <li class="flex items-center gap-2">
          <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-gray-600 text-xs">2</span>
          <span>Payment</span>
        </li>
        <li class="h-px w-16 bg-gray-200"></li>
        <li class="flex items-center gap-2">
          <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-gray-600 text-xs">3</span>
          <span>Delivery</span>
        </li>
        <li class="h-px w-16 bg-gray-200"></li>
        <li class="flex items-center gap-2">
          <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-gray-600 text-xs">4</span>
          <span>Done</span>
        </li>
      </ol>
    </nav>

    @if ($cart->items->isEmpty())
      <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
        <p class="text-gray-600">Your cart is empty.</p>
        <a href="{{ route('products') }}" class="mt-4 inline-block rounded-lg bg-rose-600 px-4 py-2 text-white hover:bg-rose-700">Browse products</a>
      </div>
    @else

      {{-- HEADER ROW --}}
      <div class="hidden md:grid grid-cols-12 text-[11px] tracking-wide text-gray-500 mb-2 px-2">
        <div class="col-span-6">ITEMS</div>
        <div class="col-span-2 text-right">PRICE</div>
        <div class="col-span-2 text-center">QUANTITY</div>
        <div class="col-span-2 text-right">TOTAL</div>
      </div>

      {{-- LINE ITEMS --}}
      <div class="space-y-3">
        @foreach ($cart->items as $it)
          <div class="grid grid-cols-12 items-center gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
            <div class="col-span-12 md:col-span-6 flex items-center gap-4">
              <img
                src="{{ \Illuminate\Support\Str::startsWith($it->image, 'data:image')
                        ? $it->image
                        : ($it->image ? 'data:image/png;base64,'.$it->image : 'https://placehold.co/80x80/png') }}"
                class="h-16 w-16 rounded-lg object-cover ring-1 ring-gray-200" alt="">
              <div>
                <div class="font-semibold text-gray-900">{{ $it->name }}</div>
                @php $inStock = !isset($it->status) || \Illuminate\Support\Str::lower($it->status)==='instock'; @endphp
                <span class="mt-1 inline-flex items-center rounded-full px-2 py-0.5 text-xs
                    {{ $inStock ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                  {{ $inStock ? 'In Stock' : 'Out of Stock' }}
                </span>
              </div>
            </div>

            <div class="col-span-6 md:col-span-2 md:text-right text-gray-900 font-medium">
              ${{ number_format($it->price, 2) }}
            </div>

            {{-- replaced forms with axios-driven buttons --}}
            <div class="col-span-6 md:col-span-2 flex md:justify-center items-center gap-2">
              <div class="flex overflow-hidden rounded-full border border-gray-300 bg-white shadow-sm">
                <button type="button" class="px-4 py-2 leading-none hover:bg-gray-50"
                        data-action="dec" data-product-id="{{ $it->product_id }}">−</button>

                <input type="number" min="1"
                       value="{{ (int)$it->quantity }}"
                       class="w-16 border-x border-gray-300 text-center focus:border-slate-500 focus:ring-slate-500"
                       data-qty-input data-product-id="{{ $it->product_id }}">

                <button type="button" class="px-4 py-2 leading-none hover:bg-gray-50"
                        data-action="inc" data-product-id="{{ $it->product_id }}">＋</button>
              </div>

              <button type="button"
                      class="hidden md:inline-flex rounded-full bg-slate-900 px-3 py-1 text-white hover:bg-slate-700"
                      data-action="update" data-product-id="{{ $it->product_id }}">
                Update
              </button>

              <button type="button"
                      class="hidden md:block rounded-full border border-gray-300 px-3 py-1 hover:bg-gray-50"
                      data-action="remove" data-product-id="{{ $it->product_id }}">
                Remove
              </button>
            </div>

            <div class="col-span-12 md:col-span-2 md:text-right font-semibold text-gray-900">
              ${{ number_format($it->total, 2) }}
            </div>

            <div class="col-span-12 md:hidden flex justify-end">
              <button type="button"
                      class="rounded-full border border-gray-300 px-3 py-1 hover:bg-gray-50"
                      data-action="remove" data-product-id="{{ $it->product_id }}">
                Remove
              </button>
            </div>
          </div>
        @endforeach
      </div>

      {{-- FOOTER SUMMARY --}}
      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Promo --}}
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
          <div class="text-sm font-medium text-gray-800 mb-2">Promo Code</div>

          <form class="flex items-center gap-2" onsubmit="return false;">
            <input type="text" id="promo-code" value="{{ old('code', $cart->promo_code) }}"
                   class="flex-1 rounded-lg border-gray-300" placeholder="Enter code"
                   {{ $cart->promo_code ? 'readonly' : '' }}>
            @if ($cart->promo_code)
              <button type="button" id="promo-remove"
                      class="rounded-full border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 
                             hover:bg-gray-100 hover:text-gray-900 cursor-pointer">
                Remove
              </button>
            @else
              <button type="button" id="promo-apply"
                      class="rounded-lg bg-slate-900 text-white px-3 py-2 hover:bg-slate-700">
                Apply
              </button>
            @endif
          </form>

          @if ($cart->promo_code)
            <p class="mt-2 inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">
              Applied: {{ $cart->promo_code }} (−${{ number_format($cart->discount ?? 0, 2) }})
            </p>
          @else
            <p class="mt-2 text-xs text-gray-500">Have a code? Apply it here.</p>
          @endif
        </div>

        {{-- Discount --}}
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
          <div class="text-sm text-gray-500">Discount</div>
          <div class="mt-1 text-xl font-semibold">
            -${{ number_format($cart->discount ?? 0, 2) }}
          </div>
          @if ($cart->promo_code)
            <div class="mt-1 text-xs text-gray-500">Code: {{ $cart->promo_code }}</div>
          @endif
        </div>

        {{-- Totals + actions --}}
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
          <div class="space-y-1">
            <div class="flex items-center justify-between text-sm text-gray-600">
              <span>Subtotal</span>
              <span>${{ number_format($cart->total, 2) }}</span>
            </div>

            @if (($cart->discount ?? 0) > 0)
              <div class="flex items-center justify-between text-sm text-emerald-700">
                <span>Discount {{ $cart->promo_code ? "({$cart->promo_code})" : '' }}</span>
                <span>−${{ number_format($cart->discount, 2) }}</span>
              </div>
            @endif

            <div class="flex items-center justify-between">
              <div class="text-xs text-gray-500">Items: {{ $cart->quantity }}</div>
              <div class="text-2xl font-bold">
                Total: ${{ number_format($cart->grand_total ?? ($cart->total - ($cart->discount ?? 0)), 2) }}
              </div>
            </div>
          </div>

          <div class="mt-3 flex items-center gap-2">
            <button type="button" class="rounded-full border border-gray-300 px-4 py-2 hover:bg-gray-50"
                    id="btn-clear-cart">
              Clear
            </button>

            <button type="button" class="flex-1 w-full rounded-full bg-rose-600 px-4 py-3 font-semibold text-white hover:bg-rose-700"
                    id="btn-checkout">
              Checkout
            </button>
          </div>
        </div>
      </div>
    @endif
  </div>

  {{-- Axios + handlers (cookie-based Sanctum auth) --}}
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
  // ========= Axios (Sanctum cookie mode) =========
  const api = axios.create({
    baseURL: '/api',          // we'll call /api/v1/...
    withCredentials: true     // send cookies for Sanctum session
  });

  // Get CSRF cookie once for write requests
  async function ensureCsrf() {
    try { await axios.get('/sanctum/csrf-cookie', { withCredentials: true }); }
    catch (e) { console.error('CSRF init failed', e); throw e; }
  }

  // Flash helper
  function flash(msg, type='success') {
    const klass = type === 'success'
      ? 'bg-emerald-100 text-emerald-800'
      : 'bg-rose-100 text-rose-800';
    const box = document.createElement('div');
    box.className = `mb-4 rounded-lg px-4 py-2 ${klass}`;
    box.textContent = msg;
    const container = document.querySelector('.max-w-5xl') || document.body;
    container.prepend(box);
    setTimeout(() => box.remove(), 2600);
  }

  async function refreshCart() {
    window.location.reload();
  }

  // ========= Cart actions via API =========
  async function updateQty(productId, opOrQty) {
    await ensureCsrf();
    const payload = (typeof opOrQty === 'string')
      ? { product_id: productId, op: opOrQty } // 'inc' | 'dec'
      : { product_id: productId, quantity: Number(opOrQty) || 1 };

    await api.post('/v1/cart/update', payload);
    flash('Cart updated');
    await refreshCart();
  }

  async function removeItem(productId) {
    await ensureCsrf();
    await api.post('/v1/cart/remove', { product_id: productId });
    flash('Removed from cart');
    await refreshCart();
  }

  async function clearCart() {
    await ensureCsrf();
    await api.post('/v1/cart/clear');
    flash('Cart cleared');
    await refreshCart();
  }

  async function goCheckout() {
    // If you have API checkout, call it here; we redirect to web checkout page
    window.location.href = "{{ route('checkout.show') }}";
  }

  // ========= Promo actions =========
  // These call your existing WEB routes (they're already protected and alter the Mongo cart).
  async function promoApply(code) {
    if (!code) return flash('Enter a promo code', 'error');
    await ensureCsrf();
    await axios.post("{{ route('cart.promo.apply') }}", { code }, { withCredentials: true });
    flash('Promo applied');
    await refreshCart();
  }

  async function promoRemove() {
    await ensureCsrf();
    await axios.post("{{ route('cart.promo.remove') }}", {}, { withCredentials: true });
    flash('Promo removed');
    await refreshCart();
  }

  // ========= Wire up buttons (event delegation) =========
  document.addEventListener('click', (e) => {
    const t = e.target;

    if (t.matches('[data-action="inc"], [data-action="dec"]')) {
      const productId = t.getAttribute('data-product-id');
      const op = t.getAttribute('data-action'); // inc|dec
      updateQty(productId, op);
    }

    if (t.matches('[data-action="update"]')) {
      const productId = t.getAttribute('data-product-id');
      const input = document.querySelector(`[data-qty-input][data-product-id="${productId}"]`);
      updateQty(productId, input?.value || 1);
    }

    if (t.matches('[data-action="remove"]')) {
      const productId = t.getAttribute('data-product-id');
      removeItem(productId);
    }

    if (t.id === 'btn-clear-cart') clearCart();
    if (t.id === 'btn-checkout')   goCheckout();

    if (t.id === 'promo-apply') {
      const code = document.getElementById('promo-code')?.value?.trim();
      promoApply(code);
    }
    if (t.id === 'promo-remove') promoRemove();
  });
  </script>
</x-app-layout>
