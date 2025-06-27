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
    <div class="flex justify-between items-center px-[74px] py-4 border-b shadow">
        <a href="{{ route('welcome') }}">
            <img src="{{ asset('images/assets/logo.svg') }}" alt="Logo" class="h-8 lg:h-10 w-auto">
        </a>
        <h1 class="text-2xl font-oswald">DAFTAR ANTREAN</h1>
        <div class="text-right text-sm">
            <div>{{ \Carbon\Carbon::now()->translatedFormat('l, d-m-Y') }}</div>
            <div id="clock">00:00:00</div>
        </div>
    </div>

    <div class="mx-auto mt-10 w-[80%] h-[300px] border border-black"></div>

    <div class="flex justify-center gap-6 mt-12 ">
        @for ($i = 0; $i < 3; $i++)
            <div class="border w-[220px] shadow">
                <div class="bg-gray-100 text-center font-bold py-2 border-b">Rudy Alamsyah</div>
                <div class="flex">
                    <div class="flex-1 flex flex-col items-center justify-center py-6 border-r text-4xl font-bold">
                        <div>1</div>
                        <div class="text-sm font-normal mt-1">Anonim</div>
                    </div>
                    <div class="flex-1 flex flex-col justify-center py-2 px-2 space-y-2">
                        <div class="border h-6"></div>
                        <div class="border h-6"></div>
                        <div class="border h-6"></div>
                        <div class="border h-6"></div>
                    </div>
                </div>
                <div class="bg-gray-100 text-center py-1 font-semibold border-t">5/5</div>
            </div>
        @endfor
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>
