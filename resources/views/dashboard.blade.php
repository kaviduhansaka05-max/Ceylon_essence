<x-app-layout>
    <x-slot name="header"></x-slot>
    <main class="py-10">
      <div class="max-w-screen-2xl mx-auto px-3 sm:px-4 lg:px-6 space-y-8">
      {{-- HERO --}}
      <section class="relative overflow-hidden rounded-2xl text-white !h-[clamp(500px,22vh,360px)]">
        <video
          class="absolute inset-0 w-full h-full object-cover"
          autoplay muted loop playsinline preload="metadata">
          <source src="{{ asset('video/hero.mp4') }}" type="video/mp4">
        </video>
        <div class="absolute inset-0 z-10 bg-gradient-to-tr from-rose-500 via-pink-400 to-orange-300 mix-blend-multiply opacity-60"></div>

        <div class="relative z-20 h-full flex items-center p-4 md:p-6 lg:p-8">
          <div class="max-w-xl">
            <p class="uppercase tracking-widest text-white/90 text-xs md:text-sm">Discover</p>
            <h1 class="mt-1 text-lg md:text-2xl lg:text-3xl font-semibold leading-snug">
              Preserve Youthful Radiance
            </h1>
            <p class="mt-2 text-white/90 text-[11px] md:text-sm">
              Therapeutic papaya & saffron blend with ancient botanicals.
            </p>
            <a href="#hot-sellers"
               class="inline-flex items-center mt-3 px-3 py-1.5 md:px-4 md:py-2 rounded-full bg-white/95 text-rose-700 font-semibold shadow hover:bg-white transition">
              Shop Now
            </a>
          </div>
        </div>
      </section>
ugyvbuhbweinfiwebihbhi
      {{-- PROMO TILES --}}
      <section class="grid grid-cols-1 sm:grid-cols-3 gap-4" id="shop">
        <a href="#" class="group relative overflow-hidden rounded-xl bg-orange-50">
          <img src="{{ asset('images/gifts.jpg') }}" alt="Gifts of Paradise"
               class="w-full h-48 object-cover transition group-hover:scale-105">
          <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-black/0"></div>
          <div class="absolute bottom-3 left-4 text-white">
            <div class="text-sm uppercase tracking-wider text-white/90">Clean & Pure</div>
            <div class="text-lg font-semibold">Toxin free, skin loving ingredients for everydaay rituals</div>
          </div>
        </a>

        <a href="#" class="group relative overflow-hidden rounded-xl bg-pink-50">
          <img src="{{ asset('images/new.jpg') }}" alt="New Arrivals"
               class="w-full h-48 object-cover transition group-hover:scale-105">
          <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-black/0"></div>
          <div class="absolute bottom-3 left-4 text-white">
            <div class="text-sm uppercase tracking-wider text-white/90">simplicity</div>
            <div class="text-lg font-semibold">designed to feel effortless</div>
          </div>
        </a>

        <a href="#" class="group relative overflow-hidden rounded-xl bg-fuchsia-50">
          <img src="{{ asset('images/sale.jpg') }}" alt="Season Sale"
               class="w-full h-48 object-cover transition group-hover:scale-105">
          <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-black/0"></div>
          <div class="absolute bottom-3 left-4 text-white">
            <div class="text-sm uppercase tracking-wider text-white/90">luxury</div>
            <div class="text-lg font-semibold">Elevated wellness and beauty</div>
          </div>
        </a>
      </section>

    <section id="hot-sellers">
    @livewire('hot-sellers')
    </section>

      {{-- FEATURE 1 --}}
      <section class="grid md:grid-cols-2 gap-8 items-center">
        <div class="overflow-hidden rounded-2xl shadow-sm">
          <img src="{{ asset('images/spa-1.jpg') }}" alt="Botanical ritual" class="w-full h-80 object-cover">
        </div>
        <div>
          <h2 class="text-2xl md:text-3xl font-semibold">Curated Wellness, Modern Living</h2>
          <p class="mt-3 text-gray-600">
            Explore a curated collection of wellness brands — from clean skincare to herbal remedies —
            chosen to support your body, calm your mind, and elevate daily rituals.
          </p>
          <a href="#" class="inline-block mt-5 px-5 py-2.5 rounded-full bg-rose-600 text-white font-semibold hover:bg-rose-700 shadow">
            Learn More
          </a>
        </div>
      </section>

      {{-- FEATURE 2 --}}
      <section class="grid md:grid-cols-2 gap-8 items-center">
        <div class="order-2 md:order-1">
          <h2 class="text-2xl md:text-3xl font-semibold">Clean • Gentle • Effective</h2>
          <p class="mt-3 text-gray-600">
            We blend nature’s finest ingredients into luxurious face & body products,
            crafted with saffron, papaya, aloe, and vitamin-rich oils to cleanse,
            nourish, and restore.
          </p>
          <a href="#" class="inline-block mt-5 px-5 py-2.5 rounded-full bg-rose-600 text-white font-semibold hover:bg-rose-700 shadow">
            Explore Range
          </a>
        </div>
        <div class="order-1 md:order-2 overflow-hidden rounded-2xl shadow-sm">
          <img src="{{ asset('images/spa-2.jpg') }}" alt="Clean gentle effective" class="w-full h-80 object-cover">
        </div>
      </section>
      </div>
    </main>

    {{-- JS to fetch + render Hot Sellers --}}
    <script>
      (function () {
        const grid = document.getElementById('hot-sellers-grid');

        fetch('{{ url('/api/top-sellers') }}', { headers: { 'Accept': 'application/json' }})
          .then(r => r.json())
          .then(({ data }) => {
            if (!Array.isArray(data)) return;

            grid.innerHTML = data.map(item => {
              const price = (item.price !== null && !isNaN(item.price))
                ? `$${Number(item.price).toFixed(2)}`
                : '';

              const inStock = (item.category && item.category.toLowerCase() !== 'out of stock')
                ? '<span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">In Stock</span>'
                : '<span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Out of Stock</span>';

              return `
                <a href="/products/${item.id}" 
                   class="rounded-xl overflow-hidden bg-gray-50 border border-gray-200 shadow-md
                          hover:shadow-lg transform transition duration-300 hover:scale-[1.02] flex flex-col">
                  <div class="relative">
                    <img src="${item.image_url}" alt="${item.name ?? ''}"
                         class="w-full h-60 object-cover">
                  </div>
                  <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                      <div class="text-base font-medium text-gray-900 line-clamp-2">${item.name ?? ''}</div>
                      <div class="mt-2 text-lg font-bold text-rose-600">${price}</div>
                      <div class="mt-1">${inStock}</div>
                    </div>
                  </div>
                </a>`;
            }).join('');
          })
          .catch(() => {
            grid.innerHTML = `<div class="col-span-full text-center text-sm text-gray-500">
              Couldn’t load top sellers.
            </div>`;
          });
      })();
    </script>
</x-app-layout>
