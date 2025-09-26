<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Layout: sidebar (filters) on the left, products on the right --}}
            <div class="grid grid-cols-12 gap-6">
                {{-- Filters --}}
                <aside class="col-span-12 md:col-span-3 relative -left-3 sm:-left-5 lg:-left-7 xl:-left-9">
                    <form method="GET" action="{{ route('products') }}"
                          class="bg-white rounded-2xl shadow ring-1 ring-gray-200 p-5 space-y-5 sticky top-6">

                        {{-- Category --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category"
                                    class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500">
                                <option value="">All</option>
                                @foreach(($categories ?? []) as $cat)
                                    <option value="{{ $cat }}" {{ request('category')===$cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Price range --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price Range ($)</label>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="number" step="any" name="min_price" placeholder="Min"
                                       value="{{ request('min_price') }}"
                                       class="rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" />
                                <input type="number" step="any" name="max_price" placeholder="Max"
                                       value="{{ request('max_price') }}"
                                       class="rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" />
                            </div>
                        </div>

                        {{-- Availability --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                            @php $av = (array) request('availability', []); @endphp
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="availability[]" value="Instock"
                                           class="rounded border-gray-300"
                                           {{ in_array('Instock', $av) ? 'checked' : '' }}>
                                    In Stock
                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="availability[]" value="Out of Stock"
                                           class="rounded border-gray-300"
                                           {{ in_array('Out of Stock', $av) ? 'checked' : '' }}>
                                    Out of Stock
                                </label>
                                <p class="text-xs text-gray-500">Leave both unchecked to include all.</p>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-3 pt-2">
                            <button class="px-4 py-2 rounded bg-slate-900 text-white hover:bg-slate-700">
                                Apply Filters
                            </button>
                            <a href="{{ route('products') }}"
                               class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                                Reset
                            </a>
                        </div>
                    </form>
                </aside>

                {{-- Products --}}
                <section class="col-span-12 md:col-span-9">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                        @forelse($products as $product)
                            @php
                                $p = is_array($product) ? $product : $product->toArray();

                                // Resolve image
                                $raw = $p['image'] ?? null;
                                if ($raw) {
                                    if (\Illuminate\Support\Str::startsWith($raw, 'data:image')) {
                                        $img = $raw;
                                    } else {
                                        $img = 'data:image/png;base64,' . $raw;
                                    }
                                } else {
                                    $img = 'https://placehold.co/800x800/png';
                                }

                                $id        = (string)($p['_id'] ?? ($product->_id ?? ''));
                                $category  = trim($p['category'] ?? '');
                                $name      = $p['name'] ?? '—';
                                $priceRaw  = (float)($p['price'] ?? 0);
                                $price     = number_format($priceRaw, 2);
                                $status    = $p['status'] ?? 'Instock';
                                $isInStock = strtolower($status) === 'instock';
                            @endphp

                            {{-- CARD (no stretched overlay link anymore) --}}
                            <div
                                class="relative group rounded-[22px] bg-white border border-rose-200 shadow-[0_10px_25px_-10px_rgba(0,0,0,0.25)]
                                       overflow-hidden transition-all duration-300 min-h-[24rem] flex flex-col
                                       hover:-translate-y-1 hover:shadow-2xl hover:ring-2 hover:ring-rose-300/60">

                                {{-- Clickable image area only --}}
                                <div class="relative">
                                    <a href="{{ route('products.show', $id) }}" class="block">
                                        <div class="h-64 sm:h-72 w-full overflow-hidden">
                                            <img
                                                src="{{ $img }}" alt="{{ $name }}"
                                                class="h-full w-full object-cover select-none" />
                                            <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-white/10 via-transparent to-black/0"></div>
                                        </div>
                                    </a>

                                    @if($category)
                                        <span
                                            class="absolute top-3 right-3 text-[11px] px-2 py-0.5 rounded-full bg-white/95 text-gray-700 shadow">
                                            {{ $category }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Body --}}
                                <div class="flex-1 px-5 pt-4 pb-5">
                                    <a href="{{ route('products.show', $id) }}">
                                        <h3 class="font-semibold text-gray-900 hover:underline">{{ $name }}</h3>
                                    </a>

                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-2xl font-bold text-gray-900">${{ $price }}</span>

                                        <span class="text-xs px-3 py-1 rounded-full
                                            {{ $isInStock
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-rose-100 text-rose-700' }}">
                                            {{ $isInStock ? 'In Stock' : 'Out of Stock' }}
                                        </span>
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
                                        {{-- Add to Cart --}}
                                        <form method="POST" action="{{ route('cart.add') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button
                                                class="w-full rounded-full border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                                Add to Cart
                                            </button>
                                        </form>

                                        {{-- Buy Now --}}
                                        <form method="POST" action="{{ route('checkout.buyNow') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button
                                                class="w-full rounded-full px-4 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition">
                                                Buy Now
                                            </button>
                                        </form>

                                        {{-- TEST1 (same as Buy Now) --}}
                                        <form method="POST" action="{{ route('checkout.buyNow') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button
                                                class="w-full rounded-full px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                                                Test1
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="pointer-events-none inset-0 rounded-[22px] ring-1 ring-rose-200/70"></div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">No products found.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
