<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Contact Us') }}</h2>
  </x-slot>

  <main class="py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-2 gap-10">
      {{-- Left: info --}}
      <div>
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">We’d love to hear from you</h1>
        <p class="mt-3 text-gray-600">
          Questions, feedback, or wholesale inquiries? Send us a note and we’ll get back shortly.
        </p>

        <dl class="mt-6 space-y-4 text-gray-700">
          <div>
            <dt class="font-medium">Email</dt>
            <dd>
              <a href="mailto:ceylonessence@gmail.com" class="text-indigo-600 hover:text-indigo-700">
                ceylonessence@gmail.com
              </a>
            </dd>
          </div>

          <div>
            <dt class="font-medium">Phone</dt>
            <dd>+94 77 123 4567</dd>
          </div>

          <div>
            <dt class="font-medium">Address</dt>
            <dd>Colombo, Sri Lanka</dd>
          </div>

          <div>
            <dt class="font-medium">Instagram</dt>
            <dd>
              <a href="https://www.instagram.com/glossier/?hl=en" target="_blank" rel="noopener noreferrer"
                 class="text-indigo-600 hover:text-indigo-700">
                ceylon essence
              </a>
            </dd>
          </div>
        </dl>
      </div>

      {{-- Right: simple form (no backend yet) --}}
      <form class="bg-white rounded-2xl shadow p-6 space-y-4">
        @csrf
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input type="text"
                 class="mt-1 w-full rounded-md border-gray-300 focus:border-rose-500 focus:ring-rose-500"
                 placeholder="Your name">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email"
                 class="mt-1 w-full rounded-md border-gray-300 focus:border-rose-500 focus:ring-rose-500"
                 placeholder="you@example.com">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Message</label>
          <textarea rows="5"
                    class="mt-1 w-full rounded-md border-gray-300 focus:border-rose-500 focus:ring-rose-500"
                    placeholder="How can we help?"></textarea>
        </div>

        <button type="button"
                class="w-full inline-flex justify-center px-4 py-2 rounded-md bg-rose-600 text-white font-semibold hover:bg-rose-700">
          Send Message
        </button>
      </form>
    </div>
  </main>
</x-app-layout>
