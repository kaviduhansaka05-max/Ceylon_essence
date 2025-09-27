<footer class="bg-gray-900 text-gray-300 mt-12">
  <div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
      
      {{-- Brand / About --}}
      <div>
        <h3 class="text-xl font-bold text-white mb-3">Ceylon Essence</h3>
        <p class="text-sm leading-relaxed">
          Bringing you natural, authentic, and sustainable products 
          straight from Sri Lanka.
        </p>
        <div class="mt-4 space-y-1 text-sm">
          <p><span class="font-semibold">Phone:</span> +94 77 123 4567</p>
          <p><span class="font-semibold">Email:</span> support@ceylonessence.com</p>
        </div>
      </div>

      {{-- Quick Links --}}
      <div>
        <h3 class="text-xl font-bold text-white mb-3">Quick Links</h3>
        <ul class="space-y-2 text-sm">
          <li><a href="{{ route('home') }}" class="hover:text-white transition">Home</a></li>
          <li><a href="{{ route('cart.show') }}" class="hover:text-white transition">Cart</a></li>
          <li><a href="{{ route('contact') }}" class="hover:text-white transition">Contact</a></li>
          <li><a href="{{ route('our.story') }}" class="hover:text-white transition">Our Story</a></li>
        </ul>
      </div>

      {{-- Connect With Us --}}
      <div>
        <h3 class="text-xl font-bold text-white mb-3">Connect With Us</h3>

        {{-- Social Icons --}}
        <div class="flex space-x-4 mb-4">
          <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-linkedin-in"></i></a>
        </div>

        {{-- Mini Contact Form --}}
        <form method="POST" action="{{ route('contact') }}" class="space-y-3">
          @csrf
          <input type="email" name="email" placeholder="Your Email"
                 class="w-full rounded-md border border-gray-600 bg-gray-800 px-3 py-2 text-sm text-white focus:border-rose-500 focus:ring-rose-500" required>

          <textarea name="message" rows="2" placeholder="Your Message"
                    class="w-full rounded-md border border-gray-600 bg-gray-800 px-3 py-2 text-sm text-white focus:border-rose-500 focus:ring-rose-500" required></textarea>

          <button type="submit"
                  class="w-full rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 transition">
            Send
          </button>
        </form>
      </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-gray-700 mt-10 pt-4 text-center text-xs text-gray-500">
      &copy; {{ date('Y') }} Ceylon Essence. All rights reserved.
    </div>
  </div>
</footer>
