<x-guest-layout>
  <div class="relative min-h-screen flex items-center justify-center bg-gray-100 px-4">

    {{-- Decorative background glows (same as login pages) --}}
    <div class="pointer-events-none absolute inset-0 -z-10">
      <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-rose-200/40 blur-3xl"></div>
      <div class="absolute -bottom-28 -right-28 h-80 w-80 rounded-full bg-indigo-200/40 blur-3xl"></div>
    </div>

    {{-- Card (60/40 split like the admin/user login cards) --}}
    <div
      class="w-full md:w-11/12 max-w-6xl grid grid-cols-1 md:grid-cols-5 rounded-3xl overflow-hidden bg-white my-8
             border border-white/70 ring-1 ring-black/5"
      style="box-shadow: 0 40px 80px -20px rgba(0,0,0,.35), 0 18px 36px -18px rgba(0,0,0,.25);"
    >
      {{-- LEFT: Image panel (60%) --}}
      <div class="relative md:col-span-3 flex items-center justify-center bg-white min-h-[420px] md:min-h-[820px]">
        <img
          src="{{ asset('images/login.png') }}"
          alt="{{ config('app.name') }}"
          class="max-h-full w-full object-contain drop-shadow-xl"
        />
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-indigo-600/10 to-violet-600/10"></div>
      </div>

      {{-- RIGHT: Content (40%) --}}
      <div class="md:col-span-2 bg-white p-8 md:p-12 flex flex-col justify-center">
        <h2 class="text-3xl font-bold tracking-tight text-indigo-700 mb-8">
          Choose Login Type
        </h2>

        <div class="w-full max-w-sm space-y-4">
          <a href="{{ route('login.user') }}"
             class="block w-full rounded-full bg-indigo-600 hover:bg-indigo-700 text-white text-center font-semibold py-3 shadow-lg">
            Login as User
          </a>

          <a href="{{ route('login.admin') }}"
             class="block w-full rounded-full bg-rose-600 hover:bg-rose-700 text-white text-center font-semibold py-3 shadow-lg">
            Login as Admin
          </a>
        </div>
      </div>
    </div>
  </div>
</x-guest-layout>
