<x-app-layout>
  {{-- Optional page masthead --}}
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Our Story') }}
    </h2>
  </x-slot>

  <main class="py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">

      {{-- SECTION 1: Our Story (image + copy) --}}
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
    healing botanicals, and ancient herbal remedies passed down through generations. Inspired by
    this rich heritage, we set out on a journey to create skincare that is not only effective, but
    also pure, sustainable, and connected to nature’s wisdom.
  </p>

  <p class="mt-4 leading-7 text-gray-600">
    Each of our products is carefully crafted using ingredients known for their skin-loving
    benefits—coconut, sandalwood, turmeric, cinnamon, and other natural treasures that have been
    trusted by our ancestors for their healing properties. We combine these traditional botanicals
    with modern skincare science to ensure our formulas are gentle yet powerful, effective yet
    nourishing.
  </p>

  <p class="mt-4 leading-7 text-gray-600">
    At the heart of Ceylon Essence is a commitment to sustainability. From responsibly sourcing our
    raw ingredients to eco-conscious packaging, we take every step to protect the environment while
    empowering local communities. Our vision is to share Sri Lanka’s botanical wealth with the world
    while honoring the land and traditions that inspire us.
  </p>

         
        </div>
      </section>

      {{-- SECTION 2: Community (cards with quote + IG handle, clickable) --}}
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

                {{-- subtle gradient overlay on hover --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent
                            opacity-0 group-hover:opacity-100 transition"></div>

                {{-- handle badge on hover (bottom-left) --}}
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

      {{-- SECTION 3: About Ceylon Essence (under Community) --}}
      <section class="text-center">
        <h2 class="text-lg font-semibold tracking-wide text-gray-900 uppercase">About Ceylon Essence</h2>
        <p class="mt-4 max-w-3xl mx-auto text-gray-600 leading-7">
          Amazing cosmetics that have absorbed the natural wealth of Ceylon and the age-old wisdom of Ayurveda.
          Our products are based only on natural ingredients, and the recipes are based on the authentic principles
          of the balance of the elements, the harmony of health and beauty.
        </p>

        @php
          $features = [
            ['icon' => 'natural.svg',   'labelTop' => '100% NATURAL',  'labelBottom' => 'PLANT-BASED'],
            ['icon' => 'eco.svg',       'labelTop' => 'ECO',           'labelBottom' => 'LOW IMPACT'],
            ['icon' => 'cruelty.svg',   'labelTop' => 'CRUELTY FREE',  'labelBottom' => 'NO ANIMAL TESTS'],
            ['icon' => 'recycle.svg',   'labelTop' => 'RECYCLABLE',    'labelBottom' => 'SUSTAINABLE'],
            ['icon' => 'vegan.svg',     'labelTop' => 'VEGAN',         'labelBottom' => 'NO ANIMAL BY-PRODUCTS'],
            ['icon' => 'quality.svg',   'labelTop' => 'PREMIUM',       'labelBottom' => 'QUALITY FIRST'],
          ];
        @endphp

        <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
          @foreach ($features as $f)
            <div class="flex flex-col items-center gap-2 p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
              <img src="{{ asset('images/icons/'.$f['icon']) }}" alt="{{ $f['labelTop'] }}" class="h-10 w-10 object-contain">
              <div class="text-[11px] tracking-wide text-gray-800 font-semibold uppercase leading-tight">
                {{ $f['labelTop'] }}
              </div>
              <div class="text-[10px] text-gray-500 uppercase">{{ $f['labelBottom'] }}</div>
            </div>
          @endforeach
        </div>
      </section>

    </div>
  </main>
</x-app-layout>
