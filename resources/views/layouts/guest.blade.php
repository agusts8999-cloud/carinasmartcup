<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CarinaSmartCup') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-emerald-50 via-teal-50 to-stone-100">
        <div class="mb-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-600 text-white font-bold text-lg">C</span>
                <span class="font-bold text-emerald-800 text-xl">CarinaSmartCup</span>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-2 px-6 py-6 bg-white shadow-lg border border-emerald-100 overflow-hidden sm:rounded-2xl">
            {{ $slot }}
        </div>

        <p class="mt-6 text-sm text-emerald-700">
            <a href="{{ route('home') }}" class="hover:underline">← Kembali ke toko</a>
        </p>
    </div>
</body>
</html>
