<x-guest-layout>
  <div class="relative min-h-screen flex items-center justify-center bg-gray-100 px-4">

    {{-- ambient background glows (purely decorative) --}}
    <div class="pointer-events-none absolute inset-0 -z-10">
      <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-rose-200/40 blur-3xl"></div>
      <div class="absolute -bottom-28 -right-28 h-80 w-80 rounded-full bg-indigo-200/40 blur-3xl"></div>
    </div>

    <!-- card -->
    <div
      class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 rounded-2xl overflow-hidden
             border border-white/70 ring-1 ring-black/5 bg-white
             shadow-2xl hover:shadow-[0_60px_120px_-25px_rgba(0,0,0,.35)]
             transition-shadow duration-300 ease-out">

      <!-- LEFT: full image panel (taller + show full image) -->
      <div class="relative flex items-center justify-center bg-white min-h-[420px] md:min-h-[760px]">
        <img
          src="{{ asset('images/loginimage.png') }}"
          alt="{{ config('app.name') }}"
          class="max-h-full w-full object-contain drop-shadow-xl"
        />
        {{-- soft tint (optional) --}}
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-indigo-600/10 to-violet-600/10"></div>
      </div>

      <!-- RIGHT: sign-in form (texts/fields unchanged) -->
      <div class="bg-white p-8 md:p-10">
        <x-validation-errors class="mb-4" />

        @session('status')
          <div class="mb-4 font-medium text-sm text-green-600">
            {{ $value }}
          </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
          @csrf

          <div>
            <x-label for="email" value="{{ __('Email') }}" class="text-gray-700" />
            <x-input
              id="email"
              class="block mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3"
              type="email"
              name="email"
              :value="old('email')"
              required
              autofocus
              autocomplete="username"
            />
          </div>

          <div>
            <x-label for="password" value="{{ __('Password') }}" class="text-gray-700" />
            <x-input
              id="password"
              class="block mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3"
              type="password"
              name="password"
              required
              autocomplete="current-password"
            />
          </div>

          <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center">
              <x-checkbox id="remember_me" name="remember" class="text-indigo-600 focus:ring-indigo-500" />
              <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
              <a class="text-sm font-medium text-indigo-600 hover:text-indigo-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                 href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
              </a>
            @endif
          </div>

          <div class="pt-2">
            <x-button
              class="w-full justify-center rounded-full bg-rose-600 hover:bg-rose-700 border-0 py-3 text-base
                     shadow-lg shadow-rose-400/40 transition-colors"
            >
              {{ __('Log in') }}
            </x-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-guest-layout>
