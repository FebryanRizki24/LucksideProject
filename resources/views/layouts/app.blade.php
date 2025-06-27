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
    <link rel="preload" href="https://fonts.gstatic.com/s/oswald/v48/TK3iWkUHHAIjg752GT8G.woff2" as="font"
        type="font/woff2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}">
    </script>

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-oswald antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-100 flex flex-col">
        @livewire('navigation-menu')

        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="flex-1">
            {{ $slot }}

            <button id="scrollToTopBtn"
                class="fixed bottom-5 right-5 bg-[#0A0A12] text-white p-3 rounded-full shadow-lg hover:bg-gray-600 transition-opacity opacity-0">
                <img src="images/icons/arrow.svg" alt="" class="invert">
            </button>
        </main>

        @include('footer')
    </div>

    @stack('modals')

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if ($.fn.select2) {
                const $select = $('#hairstyle-select');
                const $preview = $('#hairstyle-preview');
                const $previewImg = $('#hairstyle-preview-img');
                const $nameLabel = $('#hairstyle-name-label');
                const $descLabel = $('#hairstyle-desc-label');

                $select.select2({
                    placeholder: "Pilih Hairstyle",
                    allowClear: true,
                    width: '100%',
                });

                $select.on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const imageUrl = selectedOption.data('image');
                    const hairstyleName = selectedOption.text();
                    const hairstyleDesc = selectedOption.data('deskripsi');

                    if ($(this).val() && imageUrl) {
                        $previewImg.attr('src', imageUrl);
                        $nameLabel.text(hairstyleName);
                        $descLabel.text(hairstyleDesc || '-');
                        $preview.removeClass('hidden');
                    } else {
                        $preview.addClass('hidden');
                        $previewImg.attr('src', '');
                        $nameLabel.text('');
                        $descLabel.text('');
                    }
                });

            } else {
                console.error("Select2 masih tidak ditemukan! Coba cek urutan script.");
            }
        });
    </script>

    @livewireScripts
</body>

</html>
