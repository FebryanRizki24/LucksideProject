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

<body class="flex flex-col h-screen">
    <!-- HEADER -->
    <div class="px-4 md:px-[74px] py-4 border-b shadow flex justify-between items-center">
        <a href="{{ route('welcome') }}">
            <img src="{{ asset('images/assets/logo.svg') }}" alt="Logo" class="h-8 lg:h-10 w-auto">
        </a>
        <h1 class="text-xl md:text-2xl font-oswald">DAFTAR ANTREAN</h1>
        <div class="text-right text-xs md:text-sm">
            <div>{{ \Carbon\Carbon::now()->translatedFormat('l, d-m-Y') }}</div>
            <div id="clock">00:00:00</div>
        </div>
    </div>

    <!-- BAGIAN ATAS -->
    <div class="flex-1 flex items-center justify-center border px-4">
        <div class="w-full md:w-[80%] h-[40%] md:h-[60%] border border-black"></div>
    </div>

    <!-- BAGIAN BAWAH -->
    <div id="queue-container" class="flex-1 flex flex-wrap justify-center gap-4 md:gap-8 p-4 overflow-y-auto">
    </div>

    <!-- Script Jam -->
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

        async function fetchQueue() {
            try {
                const response = await fetch('/api/live-queue');
                const data = await response.json();
                updateQueueView(data);
            } catch (error) {
                console.error('Gagal memuat antrean:', error);
            }
        }

        function updateQueueView(barbermans) {
            const container = document.querySelector("#queue-container");
            container.innerHTML = '';

            barbermans.forEach(barberman => {
                const card = document.createElement('div');
                card.className =
                    "border w-full sm:w-[250px] md:w-[280px] lg:w-[320px] shadow-xl rounded-lg overflow-hidden flex flex-col";

                card.innerHTML = `
                <div class="bg-gray-100 text-center font-bold py-3 text-base md:text-lg border-b">
                    ${barberman.barberman_name}
                </div>
                <div class="flex flex-1">
                    <div class="flex-1 flex flex-col items-center justify-center py-6 md:py-8 border-r text-4xl md:text-5xl font-bold">
                        <div>${barberman.antrean_saat_ini?.antrean ?? '-'}</div>
                        <div class="text-sm md:text-base font-normal mt-2">
                            ${barberman.antrean_saat_ini?.customer_name ?? '-'}
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col items-start py-4 px-3 space-y-2">
                        ${[...Array(5)].map((_, i) => {
                            const next = barberman.antrean_akan_datang[i];
                            return `<div class="border h-8 w-full rounded flex items-center px-2 text-sm">
                                        ${next ? `${next.antrean} - ${next.customer_name}` : '-'}
                                    </div>`;
                        }).join('')}
                    </div>
                </div>
                <div class="bg-gray-100 text-center py-2 text-sm md:text-base font-semibold border-t mt-auto">
                    ${barberman.antrean_selesai.length} / ${barberman.jumlah_antrean}
                </div>
            `;

                container.appendChild(card);
            });
        }

        setInterval(fetchQueue, 5000);
        fetchQueue();
    </script>
</body>

</html>
