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
        <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body>
        <div class="w-full h-screen flex">
            <!-- Bagian Gambar (50%) -->
            <div class="hidden lg:flex w-1/2 h-full items-center justify-center bg-white">
                <img src="{{ asset('images/assets/auth_pict.svg') }}" class="w-3/4 mix-blend-difference">
            </div>       
        
            <!-- Bagian Form Login (100% pada layar kecil, 50% pada layar besar) -->
            <div class="w-full lg:w-1/2 h-full flex items-center justify-center">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @livewireScripts
    </body>
</html>
