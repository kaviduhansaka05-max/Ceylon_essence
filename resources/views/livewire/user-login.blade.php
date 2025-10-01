{{-- login: split layout (left welcome + right sign-in) --}}
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
  <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 rounded-2xl overflow-hidden shadow-2xl border border-gray-200 bg-white">

    {{-- LEFT: welcome / sign up --}}
    <div class="relative bg-gradient-to-br from-indigo-500 via-indigo-500 to-violet-600 p-8 md:p-10 text-white">
      {{-- subtle circles --}}
      <div class="absolute -top-10 -left-10 h-40 w-40 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
      <div class="absolute -bottom-12 -right-12 h-48 w-48 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>

      {{-- brand mark --}}
      <div class="flex items-center justify-center">
        <div class="h-14 w-14 rounded-full bg-white/20 flex items-center justify-center shadow">
          <svg viewBox="0 0 24 24" class="h-7 w-7 text-white/90" fill="currentColor">
            <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm3.4 6.2l-4.9 7.6a1 1 0 01-1.7-1.1l4.9-7.6a1 1 0 111.7 1.1z"/>
          </svg>
        </div>
      </div>

      <div class="mt-8 space-y-3 md:space-y-4">
        <h2 class="text-3xl md:text-4xl font-extrabold leading-tight">Hey There!</h2>
        <p class="text-white/90">Welcome back. You are just one step away from your feed.</p>
      </div>

      <div class="mt-8">
        <a href="{{ Route::has('register') ? route('register') : '#' }}"
           class="inline-flex items-center justify-center rounded-full border border-white/70 px-5 py-2.5 text-sm font-semibold hover:bg-white hover:text-indigo-600 transition">
          Sign Up
        </a>
      </div>
    </div>

    {{-- RIGHT: sign-in form (kept EXACT same bindings) --}}
    <div class="p-8 md:p-10">
      {{-- flashes (unchanged) --}}
      @if (session()->has('error'))
        <div class="mb-4 rounded-md bg-rose-50 text-rose-700 px-4 py-2 text-sm">{{ session('error') }}</div>
      @endif
      @if (session()->has('message'))
        <div class="mb-4 rounded-md bg-emerald-50 text-emerald-700 px-4 py-2 text-sm">{{ session('message') }}</div>
      @endif

      <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">Sign In</h3>

      <form wire:submit.prevent="login" class="space-y-5">
        {{-- Email (same model/ids) --}}
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" id="email" wire:model="email" required
                 class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5" />
          @error('email') <span class="text-rose-600 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Password (same model/ids) --}}
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input type="password" id="password" wire:model="password" required
                 class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5" />
          @error('password') <span class="text-rose-600 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- helper row (visual only; no extra bindings) --}}
        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center gap-2 text-gray-600 select-none">
            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            Keep me logged in
          </label>
          <a href="{{ Route::has('password.request') ? route('password.request') : '#' }}"
             class="text-indigo-600 hover:text-indigo-700 font-medium">
            Forgot your password?
          </a>
        </div>

        <button type="submit"
                class="w-full rounded-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 transition">
          Sign In
        </button>

        {{-- social (pure UI, optional) --}}
        <div class="mt-6">
          <p class="text-center text-xs text-gray-500 mb-3">Or, use social media to sign in</p>
          <div class="flex items-center justify-center gap-3">
            <button type="button" class="h-10 w-10 rounded-full bg-[#1877F2] text-white flex items-center justify-center shadow">
              <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor"><path d="M22 12a10 10 0 10-11.6 9.9v-7h-2.2V12h2.2V9.6c0-2.2 1.3-3.4 3.2-3.4.9 0 1.8.1 1.8.1v2h-1c-1 0-1.3.6-1.3 1.2V12h2.3l-.4 2.9h-1.9v7A10 10 0 0022 12z"/></svg>
            </button>
            <button type="button" class="h-10 w-10 rounded-full bg-[#1DA1F2] text-white flex items-center justify-center shadow">
              <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor"><path d="M22.46 6c-.77.35-1.6.59-2.46.69a4.27 4.27 0 001.88-2.36 8.48 8.48 0 01-2.7 1.03 4.24 4.24 0 00-7.22 3.87A12.05 12.05 0 013 5.16a4.23 4.23 0 001.31 5.66 4.2 4.2 0 01-1.92-.53v.05a4.24 4.24 0 003.4 4.16c-.47.13-.97.2-1.48.2-.36 0-.72-.03-1.06-.1a4.25 4.25 0 003.96 2.95A8.5 8.5 0 012 19.54a12 12 0 006.5 1.9c7.81 0 12.08-6.47 12.08-12.08 0-.18 0-.35-.01-.53A8.63 8.63 0 0022.46 6z"/></svg>
            </button>
            <button type="button" class="h-10 w-10 rounded-full bg-[#0A66C2] text-white flex items-center justify-center shadow">
              <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor"><path d="M20.45 20.45h-3.55v-5.26c0-1.26-.02-2.87-1.75-2.87-1.75 0-2.02 1.36-2.02 2.77v5.36H9.58V9h3.4v1.56h.05c.47-.9 1.62-1.85 3.34-1.85 3.57 0 4.23 2.35 4.23 5.4v6.34zM5.34 7.43a2.06 2.06 0 110-4.12 2.06 2.06 0 010 4.12zM7.12 20.45H3.56V9h3.56v11.45zM22.23 0H1.77C.79 0 0 .77 0 1.72v20.56C0 23.22.79 24 1.77 24h20.46c.98 0 1.77-.78 1.77-1.72V1.72C24 .77 23.21 0 22.23 0z"/></svg>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
