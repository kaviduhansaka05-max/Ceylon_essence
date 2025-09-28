<div x-data="{ open: false }" class="relative">

    <!-- ✅ Top bar with hamburger (mobile only) -->
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
        :class="open ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'"
        class="fixed sm:static top-0 left-0 h-screen w-64 bg-gray-800 text-white z-40 transform transition-transform duration-300">

        <div class="px-4 py-4 text-lg font-bold border-b border-gray-700 flex justify-between items-center sm:block">
            <span>Admin Dashboard</span>
            <!-- Close button (mobile only) -->
            <button @click="open = false" class="sm:hidden p-1 hover:bg-gray-700 rounded">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
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

    <!--  Dark overlay when sidebar is open (mobile only) -->
    <div
        x-show="open"
        @click="open = false"
        class="fixed inset-0 bg-black/50 sm:hidden z-30">
    </div>

</div>
