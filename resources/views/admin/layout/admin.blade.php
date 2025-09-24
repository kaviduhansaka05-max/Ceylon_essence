<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin • {{ config('app.name') }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100">
  @include('admin.layout.nav')

  {{-- push content right of the 16rem sidebar --}}
  <main class="ml-64 min-h-screen p-6">
    {{-- flash --}}
    @if (session('success'))
      <div class="mb-4 rounded-lg bg-emerald-50 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
    @endif
    @yield('content')
  </main>
</body>
</html>
