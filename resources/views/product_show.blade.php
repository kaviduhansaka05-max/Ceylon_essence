<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Product Details') }}
        </h2>
    </x-slot>

    @php
        $p = is_array($product) ? $product : $product->toArray();

        // image
        $raw = $p['image'] ?? null;
        if ($raw) {
            if (\Illuminate\Support\Str::startsWith($raw, 'data:image')) {
                $img = $raw;
            } else {
                $img = 'data:image/png;base64,' . $raw;
            }
        } else {
            $img = 'https://placehold.co/1200x1200/png';
        }

        $id          = $p['_id'] ?? $p['id'] ?? null;
        $name        = $p['name'] ?? '—';
        $category    = trim($p['category'] ?? '');
        $description = $p['description'] ?? null;
        $size        = $p['size'] ?? null;
        $price       = number_format((float)($p['price'] ?? 0), 2);
        $inventory   = (int)($p['inventory'] ?? 0);
        $status      = $p['status'] ?? 'Instock';
        $isInStock   = strtolower($status) === 'instock';
    @endphp

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Image --}}
                <div class="bg-white rounded-2xl shadow overflow-hidden">
                    <img src="{{ $img }}" alt="{{ $name }}" class="w-full h-auto object-cover">
                </div>

                {{-- Info --}}
                <div class="bg-white rounded-2xl shadow p-6 md:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $name }}</h1>
                        <span class="text-xs px-3 py-1 rounded-full whitespace-nowrap
                            {{ $isInStock ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $isInStock ? 'In Stock' : 'Out of Stock' }}
                        </span>
                    </div>

                    @if($category || $size)
                        <div class="mt-2 text-sm text-gray-600 space-x-3">
                            @if($category)
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-700">{{ $category }}</span>
                            @endif
                            @if($size)
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-700">Size: {{ $size }}</span>
                            @endif
                        </div>
                    @endif

                    <div class="mt-4 text-3xl font-bold text-gray-900">${{ $price }}</div>

                    @if($description)
                        <div class="mt-6 prose prose-sm max-w-none text-gray-700">
                            <p>{{ $description }}</p>
                        </div>
                    @endif

                    <div class="mt-6 flex items-center gap-3">
                        {{-- Add to Cart --}}
                        <form method="POST" action="{{ route('cart.add') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $id }}">
                            <input type="hidden" name="quantity" value="1">

                            <button type="submit"
                                class="rounded-full border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                Add to Cart
                            </button>
                        </form>

                        {{-- Buy Now --}}
  <form method="POST" action="{{ route('checkout.buyNow') }}">
    @csrf
    <input type="hidden" name="product_id" value="{{ $id }}">
    <input type="hidden" name="quantity" value="1">

    <button type="submit"
        class="rounded-full px-5 py-2.5 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition">
        Buy Now
    </button>
</form>



                        <a href="{{ route('products') }}"
                           class="ml-auto text-sm text-gray-600 hover:text-gray-900 underline">Back to Products</a>
                    </div>

                    @if(!$isInStock)
                        <p class="mt-3 text-sm text-rose-600">Currently unavailable. Check back soon.</p>
                    @elseif($inventory > 0 && $inventory <= 5)
                        <p class="mt-3 text-sm text-amber-600">Only {{ $inventory }} left in stock!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
