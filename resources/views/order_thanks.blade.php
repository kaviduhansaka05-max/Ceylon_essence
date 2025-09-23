<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl">Thank you!</h2>
  </x-slot>

  <div class="py-12 max-w-2xl mx-auto px-4">
    <div class="rounded-2xl bg-white p-8 shadow ring-1 ring-gray-200 text-center">
      <div class="text-3xl mb-2">🎉</div>
      <h3 class="text-xl font-semibold">Your order was placed successfully.</h3>
      <p class="mt-2 text-gray-600">Order ID: <span class="font-mono text-sm">{{ $orderId }}</span></p>

      <div class="mt-6 flex justify-center gap-3">
        <a href="{{ route('products') }}" class="px-4 py-2 rounded bg-slate-900 text-white hover:bg-slate-700">
          Continue Shopping
        </a>
        <a href="{{ route('cart.show') }}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">
          View Cart
        </a>
      </div>
    </div>
  </div>
</x-app-layout>
