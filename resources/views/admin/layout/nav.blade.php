<div x-data="{ open: false }" class="relative">

    <!-- ✅ Top bar with hamburger -->
    <div class="flex items-center justify-between bg-gray-800 text-white px-4 py-3 sm:hidden">
        <div class="font-bold text-lg">Admin Dashboard</div>
        <button @click="open = !open" class="p-2 rounded hover:bg-gray-700">
            <!-- Hamburger / X icon -->
            <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-show="open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- ✅ Sidebar -->
    <aside
        :class="open ? 'translate-x-0' : '-translate-x-full'"
        class="fixed top-0 left-0 h-screen w-64 bg-gray-800 text-white z-40 transform transition-transform duration-300 sm:translate-x-0">

        <div class="px-4 py-4 text-lg font-bold border-b border-gray-700">
            Admin Dashboard
        </div>

        @php
          $link   = 'block px-3 py-2 rounded hover:bg-gray-700';
          $active = 'bg-gray-700 text-white';
        @endphp

        <nav class="mt-2 px-2 space-y-1 overflow-y-auto h-[calc(100vh-112px)]">
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
    </aside>

</div>
