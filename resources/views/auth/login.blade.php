<x-guest-layout>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <!-- container -->
    <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 rounded-2xl overflow-hidden shadow-2xl border border-gray-200 bg-white">

      <!-- LEFT: purple panel (logo only, no text changed) -->
      <div class="relative bg-gradient-to-br from-indigo-500 via-indigo-500 to-violet-600 p-10 flex items-center justify-center">
        <div class="absolute -top-10 -left-10 h-40 w-40 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -right-12 h-48 w-48 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>

        <div class="flex items-center justify-center">
          {{-- keep your existing logo slot/component --}}
          <x-authentication-card-logo class="h-16 w-16 text-white opacity-95" />
        </div>
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
              class="w-full justify-center rounded-full bg-indigo-600 hover:bg-indigo-700 border-0 py-3 text-base"
            >
              {{ __('Log in') }}
            </x-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-guest-layout>
