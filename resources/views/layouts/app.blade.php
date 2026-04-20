<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased overflow-x-hidden">
        <div class="min-h-screen relative bg-gradient-to-br from-[#5F1BF2] via-[#8704BF] to-[#BF1F6A] text-white">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_15%,rgba(242,5,159,0.18),transparent_35%),radial-gradient(circle_at_80%_0%,rgba(95,27,242,0.18),transparent_32%),radial-gradient(circle_at_60%_85%,rgba(191,31,106,0.2),transparent_40%)] pointer-events-none"></div>

            <div class="relative">
                @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/10 backdrop-blur-md shadow-sm border-b border-white/20">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <div class="text-white">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
            </div>
        </div>
    </body>
</html>
