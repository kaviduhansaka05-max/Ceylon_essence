<x-app-layout>
  {{-- Page header --}}
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Our Story') }}
    </h2>
  </x-slot>

  <main class="py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">

      {{-- SECTION 1: Our Story --}}
      <section class="grid md:grid-cols-2 gap-8 md:gap-12 items-start">
        <img
          src="{{ asset('images/hero-left.jpg') }}"
          alt="Ceylon Essence founder & origin"
          class="w-full h-auto rounded-xl shadow-sm object-cover aspect-[4/5]"
        />

        <div>
          <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900">Our Story</h1>
          <p class="mt-5 leading-7 text-gray-600">
            Ceylon Essence was born from a deep passion for natural beauty and the timeless wellness
            traditions of Sri Lanka. For centuries, the island has been celebrated for its lush landscapes,
            healing botanicals, and ancient herbal remedies passed down through generations...
          </p>
          <p class="mt-4 leading-7 text-gray-600">
            Each of our products is carefully crafted using ingredients known for their skin-loving
            benefits—coconut, sandalwood, turmeric, cinnamon, and other natural treasures...
          </p>
          <p class="mt-4 leading-7 text-gray-600">
            At the heart of Ceylon Essence is a commitment to sustainability. From responsibly sourcing our
            raw ingredients to eco-conscious packaging, we take every step to protect the environment...
          </p>
        </div>
      </section>

      {{-- SECTION 2: Community --}}
      <section>
        <h2 class="text-lg font-semibold tracking-wide text-gray-900 uppercase">Community</h2>

        @php
          $grams = [
            [
              'img' => 'images/community-1.jpg',
              'url' => 'https://instagram.com/steffy_sunny_',
              'handle' => '@steffy_sunny_',
              'quote' => '“This serum changed my routine completely—my skin feels alive again.”',
            ],
            [
              'img' => 'images/community-2.jpg',
              'url' => 'https://instagram.com/nikita_bhardwaj_00',
              'handle' => '@nikita_bhardwaj_00',
              'quote' => '“Finally found a product that’s both ethical and effective.”',
            ],
            [
              'img' => 'images/community-3.jpg',
              'url' => 'https://instagram.com/mikaylanogueira',
              'handle' => '@mikaylanogueira',
              'quote' => '“Every drop feels like luxury—I get compliments all the time now.”',
            ],
          ];
        @endphp

        <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-6">
          @foreach ($grams as $g)
            <a href="{{ $g['url'] }}" target="_blank" rel="noopener"
               class="group block rounded-2xl overflow-hidden bg-white ring-1 ring-gray-100 shadow transition
                      hover:shadow-xl hover:-translate-y-1 hover:scale-[1.01]">
              <div class="relative">
                <img src="{{ asset($g['img']) }}" alt="Community {{ $g['handle'] }}"
                     class="w-full h-80 sm:h-72 object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent
                            opacity-0 group-hover:opacity-100 transition"></div>
                <span class="absolute bottom-3 left-3 px-3 py-1 rounded-full text-xs font-medium
                             bg-white/90 text-gray-900 shadow-sm opacity-0 group-hover:opacity-100 transition">
                  {{ $g['handle'] }}
                </span>
              </div>
              <div class="p-4 text-center">
                <p class="text-sm text-gray-700 italic">{{ $g['quote'] }}</p>
              </div>
            </a>
          @endforeach
        </div>
      </section>

      {{-- SECTION 3: About Ceylon Essence --}}
      <section class="text-center">
        <h2 class="text-lg font-semibold tracking-wide text-gray-900 uppercase">About Ceylon Essence</h2>
        <p class="mt-4 max-w-3xl mx-auto text-gray-600 leading-7">
          Amazing cosmetics that have absorbed the natural wealth of Ceylon and the age-old wisdom of Ayurveda...
        </p>

        <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
          <div class="flex flex-col items-center gap-2 p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
            <i class="fas fa-leaf text-green-600 text-3xl"></i>
            <div class="text-[11px] tracking-wide text-gray-800 font-semibold uppercase">100% Natural</div>
            <div class="text-[10px] text-gray-500 uppercase">Plant-Based</div>
          </div>

          <div class="flex flex-col items-center gap-2 p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
            <i class="fas fa-seedling text-green-500 text-3xl"></i>
            <div class="text-[11px] tracking-wide text-gray-800 font-semibold uppercase">Eco</div>
            <div class="text-[10px] text-gray-500 uppercase">Low Impact</div>
          </div>

          <div class="flex flex-col items-center gap-2 p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
            <i class="fas fa-paw text-orange-500 text-3xl"></i>
            <div class="text-[11px] tracking-wide text-gray-800 font-semibold uppercase">Cruelty Free</div>
            <div class="text-[10px] text-gray-500 uppercase">No Animal Tests</div>
          </div>

          <div class="flex flex-col items-center gap-2 p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
            <i class="fas fa-recycle text-blue-500 text-3xl"></i>
            <div class="text-[11px] tracking-wide text-gray-800 font-semibold uppercase">Recyclable</div>
            <div class="text-[10px] text-gray-500 uppercase">Sustainable</div>
          </div>

          <div class="flex flex-col items-center gap-2 p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
            <i class="fas fa-carrot text-red-500 text-3xl"></i>
            <div class="text-[11px] tracking-wide text-gray-800 font-semibold uppercase">Vegan</div>
            <div class="text-[10px] text-gray-500 uppercase">No By-Products</div>
          </div>

          <div class="flex flex-col items-center gap-2 p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
            <i class="fas fa-star text-yellow-500 text-3xl"></i>
            <div class="text-[11px] tracking-wide text-gray-800 font-semibold uppercase">Premium</div>
            <div class="text-[10px] text-gray-500 uppercase">Quality First</div>
          </div>
        </div>
      </section>

    </div>
  </main>

  {{-- Add Font Awesome CDN --}}
  @push('scripts')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  @endpush
</x-app-layout>
