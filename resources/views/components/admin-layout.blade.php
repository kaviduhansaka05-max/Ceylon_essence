{{-- No Jetstream user nav here --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Admin' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
  @include('admin.layout.nav')  {{-- your admin nav --}}
  <main class="max-w-7xl mx-auto p-6">
    {{ $slot }}
  </main>
</body>
</html>
