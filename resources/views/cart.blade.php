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

            <div class="col-span-6 md:col-span-2 flex md:justify-center items-center gap-2">
              <form method="POST" action="{{ route('cart.update') }}" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="product_id" value="{{ $it->product_id }}">

                <div class="flex overflow-hidden rounded-full border border-gray-300 bg-white shadow-sm">
                  <button type="submit" name="op" value="dec" class="px-4 py-2 leading-none hover:bg-gray-50" aria-label="Decrease quantity">−</button>

                  <input type="number" min="1" name="quantity" value="{{ (int)$it->quantity }}"
                         class="w-16 border-x border-gray-300 text-center focus:border-slate-500 focus:ring-slate-500">

                  <button type="submit" name="op" value="inc" class="px-4 py-2 leading-none hover:bg-gray-50" aria-label="Increase quantity">＋</button>
                </div>

                <button class="hidden md:inline-flex rounded-full bg-slate-900 px-3 py-1 text-white hover:bg-slate-700">
                  Update
                </button>
              </form>

              <form method="POST" action="{{ route('cart.remove') }}" class="hidden md:block">
                @csrf
                <input type="hidden" name="product_id" value="{{ $it->product_id }}">
                <button class="rounded-full border border-gray-300 px-3 py-1 hover:bg-gray-50">Remove</button>
              </form>
            </div>

            <div class="col-span-12 md:col-span-2 md:text-right font-semibold text-gray-900">
              ${{ number_format($it->total, 2) }}
            </div>

            <div class="col-span-12 md:hidden flex justify-end">
              <form method="POST" action="{{ route('cart.remove') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $it->product_id }}">
                <button class="rounded-full border border-gray-300 px-3 py-1 hover:bg-gray-50">Remove</button>
              </form>
            </div>
          </div>
        @endforeach
      </div>

      {{-- FOOTER SUMMARY --}}
      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Promo --}}
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
          <div class="text-sm font-medium text-gray-800 mb-2">Promo Code</div>

          <form method="POST" action="{{ $cart->promo_code ? route('cart.promo.remove') : route('cart.promo.apply') }}" class="flex items-center gap-2">
            @csrf
            <input
              type="text"
              name="code"
              value="{{ old('code', $cart->promo_code) }}"
              class="flex-1 rounded-lg border-gray-300"
              placeholder="Enter code"
              {{ $cart->promo_code ? 'readonly' : '' }}
            >
            @if ($cart->promo_code)
              <button class="rounded-lg border border-gray-300 px-3 py-2 hover:bg-gray-50">Remove</button>
            @else
              <button class="rounded-lg bg-slate-900 text-white px-3 py-2 hover:bg-slate-700">Apply</button>
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
            <form method="POST" action="{{ route('cart.clear') }}">
              @csrf
              <button class="rounded-full border border-gray-300 px-4 py-2 hover:bg-gray-50">Clear</button>
            </form>

            <form method="GET" action="{{ route('checkout.show') }}" class="flex-1">
              <button class="w-full rounded-full bg-rose-600 px-4 py-3 font-semibold text-white hover:bg-rose-700">
                Checkout
              </button>
            </form>
          </div>
        </div>
      </div>
    @endif
  </div>
</x-app-layout>
