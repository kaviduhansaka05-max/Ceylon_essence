<div>
    <div class="flex items-end justify-between mb-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-semibold">Hot Sellers</h2>
            <p class="text-gray-600 text-sm mt-1">Top 5 most-ordered items of all time</p>
        </div>
        <a href="{{ route('products') }}" class="text-rose-600 hover:text-rose-700 text-sm font-medium">
            Browse all
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        @forelse($products as $item)
            <a href="{{ route('products.show', $item['id']) }}"
               class="rounded-xl overflow-hidden bg-gray-50 border border-gray-200 shadow-md
                      hover:shadow-lg transform transition duration-300 hover:scale-[1.02] flex flex-col">
                <div class="relative">
                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                         class="w-full h-60 object-cover">
                </div>
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="text-base font-medium text-gray-900 line-clamp-2">{{ $item['name'] }}</div>
                        <div class="mt-2 text-lg font-bold text-rose-600">${{ number_format($item['price'], 2) }}</div>
                        <div class="mt-1">
                            @if(strtolower($item['status']) === 'instock')
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                    In Stock
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">
                                    Out of Stock
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center text-sm text-gray-500">
                No hot sellers found.
            </div>
        @endforelse
    </div>
</div>
