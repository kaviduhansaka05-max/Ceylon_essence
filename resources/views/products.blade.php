{{-- resources/views/livewire/products.blade.php --}}
<div wire:poll.keep-alive.15s="refreshData" class="py-8">
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-12 gap-6">
            {{-- Filters --}}
            <aside class="col-span-12 md:col-span-3">
                <div class="bg-white rounded-2xl shadow ring-1 ring-gray-200 p-5 space-y-5 sticky top-6">
                    {{-- Category --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select wire:model="category"
                                class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500">
                            <option value="">All</option>
                            @foreach(($categories ?? []) as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Price range --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price Range ($)</label>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="number" step="any" placeholder="Min"
                                   wire:model.lazy="min_price"
                                   class="rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" />
                            <input type="number" step="any" placeholder="Max"
                                   wire:model.lazy="max_price"
                                   class="rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" />
                        </div>
                    </div>

                    {{-- Availability --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" value="Instock" wire:model="availability" class="rounded border-gray-300">
                                In Stock
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" value="Out of Stock" wire:model="availability" class="rounded border-gray-300">
                                Out of Stock
                            </label>
                            <p class="text-xs text-gray-500">Leave both unchecked to include all.</p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-3 pt-2">
                        <button wire:click="apply"
                                class="px-4 py-2 rounded bg-slate-900 text-white hover:bg-slate-700">
                            Apply Filters
                        </button>
                        <button wire:click="resetFilters"
                                class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Reset
                        </button>
                    </div>
                </div>
            </aside>

            {{-- Products --}}
            <section class="col-span-12 md:col-span-9">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($products as $p)
                        @php
                            $raw = $p['image'] ?? null;
                            $img = $raw
                                ? (\Illuminate\Support\Str::startsWith($raw, 'data:image') ? $raw : 'data:image/png;base64,'.$raw)
                                : 'https://placehold.co/800x800/png';
                            $id        = (string)($p['_id'] ?? '');
                            $category  = trim($p['category'] ?? '');
                            $name      = $p['name'] ?? '—';
                            $price     = number_format((float)($p['price'] ?? 0), 2);
                            $status    = $p['status'] ?? 'Instock';
                            $isInStock = strtolower($status) === 'instock';
                        @endphp

                        <div class="relative group rounded-[22px] bg-white border border-rose-200 shadow overflow-hidden transition min-h-[24rem] flex flex-col hover:-translate-y-1 hover:shadow-2xl">
                            <a href="{{ route('products.show', $id) }}" class="absolute inset-0 z-10"><span class="sr-only">View {{ $name }}</span></a>

                            <div class="h-64 sm:h-72 w-full overflow-hidden">
                                <img src="{{ $img }}" alt="{{ $name }}" class="h-full w-full object-cover" />
                            </div>

                            <div class="flex-1 px-5 pt-4 pb-5">
                                <h3 class="font-semibold text-gray-900">{{ $name }}</h3>

                                <div class="mt-2 flex items-center justify-between">
                                    <span class="text-2xl font-bold">${{ $price }}</span>
                                    <span class="text-xs px-3 py-1 rounded-full {{ $isInStock ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $isInStock ? 'In Stock' : 'Out of Stock' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No products found.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
