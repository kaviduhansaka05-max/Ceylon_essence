<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
      {{ __('Checkout') }}
    </h2>
  </x-slot>

  @php
    $mode  = $mode ?? 'cart';   // 'cart' or 'buy-now'
    $items = collect();
    $total = 0.0;
    $qty   = 0;

    if ($mode === 'buy-now' && isset($order)) {
        $items = $order->items ?? collect();
        $total = (float) ($order->total ?? 0);
        $qty   = (int)   ($order->quantity ?? 0);
    } else {
        // cart mode (be null-safe)
        $cartItems   = isset($cart) && isset($cart->items) ? $cart->items : collect();
        $items       = $cartItems instanceof \Illuminate\Support\Collection ? $cartItems : collect($cartItems);
        $total       = (float) (($cart->grand_total ?? null) !== null ? $cart->grand_total : ($cart->total ?? 0));
        $qty         = (int)   ($cart->quantity ?? 0);
    }
  @endphp

  <div class="py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

      {{-- flash --}}
      @if (session('success'))
        <div class="rounded-lg bg-emerald-100 text-emerald-800 px-4 py-2">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="rounded-lg bg-rose-100 text-rose-800 px-4 py-2">{{ session('error') }}</div>
      @endif

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Items summary --}}
        <div class="md:col-span-2 space-y-3">
          <div class="rounded-2xl bg-white shadow ring-1 ring-gray-200 p-4">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-lg font-semibold">
                {{ $mode === 'buy-now' ? 'Buy Now' : 'Your Cart' }}
              </h3>
              <span class="text-sm text-gray-500">Items: {{ $qty }}</span>
            </div>

            @if($items->isEmpty())
              <div class="text-gray-500">No items to checkout.
                @if($mode !== 'buy-now')
                  <a href="{{ route('cart.show') }}" class="underline">Back to Cart</a>
                @else
                  <a href="{{ route('products') }}" class="underline">Browse products</a>
                @endif
              </div>
            @else
              <div class="space-y-3">
                @foreach($items as $it)
                  @php
                    $img = \Illuminate\Support\Str::startsWith($it->image ?? '', 'data:image')
                        ? $it->image
                        : (($it->image ?? '') !== '' ? 'data:image/png;base64,'.$it->image : 'https://placehold.co/80x80/png');
                    $lineName  = $it->name ?? '—';
                    $lineQty   = (int)($it->quantity ?? 0);
                    $linePrice = number_format((float)($it->price ?? 0), 2);
                    $lineTotal = number_format((float)($it->total ?? 0), 2);
                  @endphp

                  <div class="flex items-center gap-4 rounded-xl ring-1 ring-gray-200 p-3">
                    <img src="{{ $img }}" class="h-16 w-16 rounded-lg object-cover ring-1 ring-gray-200" alt="">
                    <div class="flex-1">
                      <div class="font-semibold text-gray-900">{{ $lineName }}</div>
                      <div class="text-sm text-gray-600">Qty: {{ $lineQty }} &middot; ${{ $linePrice }}</div>
                    </div>
                    <div class="text-right font-semibold text-gray-900">${{ $lineTotal }}</div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>

          <div class="rounded-2xl bg-white shadow ring-1 ring-gray-200 p-4">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-600">Subtotal</div>
              <div class="text-sm text-gray-900">${{ number_format($total, 2) }}</div>
            </div>
            <div class="mt-1 flex items-center justify-between">
              <div class="text-2xl font-bold">Total</div>
              <div class="text-2xl font-bold">${{ number_format($total, 2) }}</div>
            </div>
          </div>
        </div>

        {{-- Payment form --}}
        <div class="md:col-span-1">
          <form method="POST" action="{{ route('checkout.process') }}">
    @csrf
    <input type="hidden" name="mode" value="{{ $mode }}">
    @if($mode === 'buy-now' && isset($order))
        <input type="hidden" name="orderId" value="{{ (string) $order->_id }}">
    @endif



            <div>
              <label class="block text-sm font-medium text-gray-700">Name on Card</label>
              <input name="card_name" value="{{ old('card_name') }}"
                     class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
              @error('card_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Card Number</label>
              <input name="card_number" value="{{ old('card_number') }}"
                     class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
              @error('card_number') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-3 gap-2">
              <div>
                <label class="block text-sm font-medium text-gray-700">Exp. Month</label>
                <input type="number" name="exp_month" min="1" max="12" value="{{ old('exp_month') }}"
                       class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
                @error('exp_month') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Exp. Year</label>
                <input type="number" name="exp_year" min="{{ date('Y') }}" max="{{ date('Y') + 15 }}" value="{{ old('exp_year') }}"
                       class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
                @error('exp_year') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">CVC</label>
                <input name="cvc" value="{{ old('cvc') }}"
                       class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
                @error('cvc') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Address (optional)</label>
              <textarea name="address" rows="3"
                        class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500">{{ old('address') }}</textarea>
              @error('address') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <button class="w-full rounded-full bg-rose-600 px-4 py-3 font-semibold text-white hover:bg-rose-700">
              Pay ${{ number_format($total, 2) }}
            </button>

            @if ($mode !== 'buy-now')
              <a href="{{ route('cart.show') }}" class="block text-center text-sm text-gray-600 hover:text-gray-900 underline mt-2">
                Back to Cart
              </a>
            @endif
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
