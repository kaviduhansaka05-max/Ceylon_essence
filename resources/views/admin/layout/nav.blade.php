<!-- ✅ Sidebar -->
<aside class="hidden sm:block fixed top-0 left-0 h-screen w-64 bg-gray-800 text-white z-40">
    <div class="px-4 py-4 text-lg font-bold border-b border-gray-700">
        Admin Dashboard
    </div>

    @php
      $link   = 'block px-3 py-2 rounded hover:bg-gray-700';
      $active = 'bg-gray-700 text-white';
    @endphp

    <nav class="mt-2 px-2 space-y-1 overflow-y-auto h-[calc(100vh-160px)]">
        <a href="{{ route('admin.customers.index') }}"
           class="{{ request()->routeIs('admin.customers.*') ? $active : '' }} {{ $link }}">
            Customers
        </a>

        <a href="{{ route('admin.products.index') }}"
           class="{{ request()->routeIs('admin.products.*') ? $active : '' }} {{ $link }}">
            Products
        </a>

        <a href="{{ route('admin.orders.index') }}"
           class="{{ request()->routeIs('admin.orders.*') ? $active : '' }} {{ $link }}">
            Orders
        </a>
    </nav>

    <!-- ✅ Logout button -->
    <form method="POST" action="{{ route('admin.logout') }}" class="px-2 mt-4 border-t border-gray-700 pt-3">
        @csrf
        <button type="submit" class="w-full text-left px-3 py-2 rounded bg-rose-600 hover:bg-rose-500">
            Logout
        </button>
    </form>
</aside>
