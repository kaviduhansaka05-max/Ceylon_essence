<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite (CSS + JS build) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        {{-- ✅ Navigation Menu --}}
        @livewire('navigation-menu')

        {{-- ✅ Page Header --}}
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        {{-- ✅ Main Page Content --}}
        <main>
            {{ $slot }}
        </main>

        {{-- ✅ Global Footer --}}
        @include('layouts.footer')
    </div>

    @stack('modals')

    {{-- ✅ Load Livewire scripts (only once!) --}}
    @livewireScripts

    {{-- ✅ DO NOT include extra Livewire or Alpine JS from CDN here --}}
    {{-- Jetstream and Vite already load everything correctly --}}
</body>
</html>
