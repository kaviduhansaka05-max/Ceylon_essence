{{-- Left sidebar (full height) --}}
<aside class="fixed top-0 left-0 h-screen w-64 bg-gray-800 text-white z-40">
  <div class="px-4 py-4 text-lg font-bold border-b border-gray-700">
    Admin Dashboard
  </div>

  @php
    $link   = 'block px-3 py-2 rounded hover:bg-gray-700';
    $active = 'bg-gray-700 text-white';
  @endphp

<nav class="mt-2 px-2 space-y-1 overflow-y-auto h-[calc(100vh-112px)]">
  <a href="{{ route('admin.users.index') }}"
     class="{{ request()->routeIs('admin.users.*') ? $active : '' }} {{ $link }}">Users</a>

  <a href="{{ route('admin.products.index') }}"
     class="{{ request()->routeIs('admin.products.*') ? $active : '' }} {{ $link }}">Products</a>

 <a href="{{ route('admin.customers.index') }}"
   class="{{ request()->routeIs('admin.customers.*') ? $active : '' }} {{ $link }}">
  Customers
</a>
</nav>


</aside>
