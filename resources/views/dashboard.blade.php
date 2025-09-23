<x-app-layout>
    {{-- optional header --}}
    <x-slot name="header">
        
    </x-slot>

    <main class="py-10">
      <div class="max-w-screen-2xl mx-auto px-3 sm:px-4 lg:px-6 space-y-8">

      {{-- HERO (clamped height, forced) --}}
<section class="relative overflow-hidden rounded-2xl text-white !h-[clamp(500px,22vh,360px)]">
  <video
    class="absolute inset-0 w-full h-full object-cover"
    autoplay
    muted
    loop
    playsinline
    preload="metadata"
  >
    <source src="{{ asset('video/hero.mp4') }}" type="video/mp4">
  </video>

<div class="absolute inset-0 z-10 bg-gradient-to-tr from-rose-500 via-pink-400 to-orange-300 mix-blend-multiply opacity-60"></div>

  <div class="relative z-20 h-full flex items-center p-4 md:p-6 lg:p-8">
    <div class="max-w-xl">
      <p class="uppercase tracking-widest text-white/90 text-xs md:text-sm">Discover</p>
      <h1 class="mt-1 text-lg md:text-2xl lg:text-3xl font-semibold leading-snug">Preserve Youthful Radiance</h1>
      <p class="mt-2 text-white/90 text-[11px] md:text-sm">Therapeutic papaya & saffron blend with ancient botanicals.</p>
      <a href="#shop" class="inline-flex items-center mt-3 px-3 py-1.5 md:px-4 md:py-2 rounded-full bg-white/95 text-rose-700 font-semibold shadow hover:bg-white transition">
        Shop Now
      </a>
    </div>
  </div>
</section>

     {{-- PROMO TILES --}}
<section class="grid grid-cols-1 sm:grid-cols-3 gap-4" id="shop">
  <a href="#" class="group relative overflow-hidden rounded-xl bg-orange-50">
    <img src="{{ asset('images/gifts.jpg') }}" alt="Gifts of Paradise"
         class="w-full h-48 object-cover transition group-hover:scale-105">
    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-black/0"></div>
    <div class="absolute bottom-3 left-4 text-white">
      <div class="text-sm uppercase tracking-wider text-white/90">Collection</div>
      <div class="text-lg font-semibold">Gifts of Paradise</div>
    </div>
  </a>

  <a href="#" class="group relative overflow-hidden rounded-xl bg-pink-50">
    <img src="{{ asset('images/new.jpg') }}" alt="New Arrivals"
         class="w-full h-48 object-cover transition group-hover:scale-105">
    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-black/0"></div>
    <div class="absolute bottom-3 left-4 text-white">
      <div class="text-sm uppercase tracking-wider text-white/90">Just In</div>
      <div class="text-lg font-semibold">New Arrivals</div>
    </div>
  </a>

  <a href="#" class="group relative overflow-hidden rounded-xl bg-fuchsia-50">
    <img src="{{ asset('images/sale.jpg') }}" alt="Season Sale"
         class="w-full h-48 object-cover transition group-hover:scale-105">
    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-black/0"></div>
    <div class="absolute bottom-3 left-4 text-white">
      <div class="text-sm uppercase tracking-wider text-white/90">Save</div>
      <div class="text-lg font-semibold">Season Sale</div>
    </div>
  </a>
</section>


        {{-- FEATURE 1 --}}
        <section class="grid md:grid-cols-2 gap-8 items-center">
          <div class="overflow-hidden rounded-2xl shadow-sm">
            <img src="{{ asset('images/spa-1.jpg') }}" alt="Botanical ritual" class="w-full h-80 object-cover">
          </div>
          <div>
            <h2 class="text-2xl md:text-3xl font-semibold">Curated Wellness, Modern Living</h2>
           <p class="mt-3 text-gray-600">
  Explore a curated collection of wellness brands — from clean skincare to herbal remedies — all chosen to support your body, calm your mind, and elevate daily rituals.
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
</x-app-layout>
