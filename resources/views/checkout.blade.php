<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl">Payment</h2>
  </x-slot>

  <div class="py-8 max-w-5xl mx-auto px-4 grid md:grid-cols-3 gap-6">
    {{-- Order Summary --}}
    <section class="md:col-span-1 rounded-2xl bg-white p-5 shadow ring-1 ring-gray-200">
      <h3 class="text-sm font-semibold text-gray-800 mb-3">Order Summary</h3>
      <ul class="divide-y divide-gray-200">
        @foreach ($cart->items as $it)
          <li class="py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <img
                src="{{ \Illuminate\Support\Str::startsWith($it->image, 'data:image')
                        ? $it->image
                        : ($it->image ? 'data:image/png;base64,'.$it->image : 'https://placehold.co/48x48/png') }}"
                class="h-10 w-10 rounded object-cover ring-1 ring-gray-200" alt="">
              <div>
                <div class="text-sm font-medium">{{ $it->name }}</div>
                <div class="text-xs text-gray-500">Qty: {{ (int)$it->quantity }}</div>
              </div>
            </div>
            <div class="text-sm font-semibold">${{ number_format($it->total, 2) }}</div>
          </li>
        @endforeach
      </ul>
      <div class="mt-4 border-t pt-3 text-right">
        <div class="text-xs text-gray-500">Items: {{ $cart->quantity }}</div>
        <div class="text-xl font-bold">Total: ${{ number_format($cart->total, 2) }}</div>
      </div>
    </section>

    {{-- Card Form --}}
    <section class="md:col-span-2 rounded-2xl bg-white p-6 shadow ring-1 ring-gray-200">
      <form method="POST" action="{{ route('checkout.process') }}" class="space-y-5">
        @csrf

        @if ($errors->any())
          <div class="rounded-lg bg-rose-50 text-rose-800 px-4 py-3">
            <ul class="list-disc list-inside">
              @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
          </div>
        @endif

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Name on card</label>
          <input name="card_name" value="{{ old('card_name', auth()->user()->name) }}"
                 class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Card number</label>
          <input name="card_number" inputmode="numeric" autocomplete="cc-number"
                 placeholder="4242 4242 4242 4242"
                 value="{{ old('card_number') }}"
                 class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
        </div>

        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Exp. Month</label>
            <input type="number" name="exp_month" min="1" max="12"
                   value="{{ old('exp_month') }}"
                   class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Exp. Year</label>
            <input type="number" name="exp_year" min="{{ now()->year }}" max="{{ now()->year + 15 }}"
                   value="{{ old('exp_year') }}"
                   class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">CVC</label>
            <input type="password" name="cvc" inputmode="numeric" autocomplete="cc-csc"
                   value="{{ old('cvc') }}"
                   class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Billing / Delivery Address</label>
          <textarea name="address" rows="3"
                    class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                    placeholder="Street, City, ZIP">{{ old('address') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
          <a href="{{ route('cart.show') }}"
             class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
            Back to Cart
          </a>
          <button class="px-5 py-2.5 rounded bg-rose-600 text-white font-semibold hover:bg-rose-700">
            Confirm & Pay
          </button>
        </div>
      </form>
    </section>
  </div>
</x-app-layout>
