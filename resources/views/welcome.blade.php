<x-app-layout>
    {{-- Home --}}
    <section id="home" class="bg-white pb-20">
        <div class="relative w-full h-screen">
            <!-- Background Image -->
            <img src="images/assets/bg_home.svg" alt="Luckside Barbershop"
                class="w-full h-full object-cover mix-blend-luminosity">

            <!-- Overlay (Tulisan di atas gambar) -->
            <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center px-4">
                <!-- Wrapper untuk teks supaya ada di tengah -->
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                    <h1 class="text-5xl md:text-6xl font-oswald uppercase drop-shadow-lg">
                        Welcome to <br> Luckside Barbershop
                    </h1>
                    <p class="mt-2 text-2xl md:text-3xl text-gray-300 uppercase">
                        Local Stray Barber <br> Since • 2017
                    </p>
                </div>

                <!-- Tombol Book Now di bawah gambar -->
                @guest
                    <a href="{{ route('login') }}"
                        class="absolute bottom-8 px-[43px] py-[13px] bg-[#979797] hover:bg-neutral-400 rounded-md font-semibold uppercase">
                        Get Started
                    </a>
                @else
                    @if (Auth::user()->hasRole('user'))
                        <a href="{{ route('booking.index') }}"
                            class="absolute bottom-8 px-[43px] py-[13px] bg-[#979797] hover:bg-neutral-400 rounded-md font-semibold uppercase">
                            Book Now
                        </a>
                    @else
                        <a href="{{ route('dashboard.index') }}"
                            class="absolute bottom-8 px-[43px] py-[13px] bg-[#979797] hover:bg-neutral-400 rounded-md font-semibold uppercase">
                            Dashboard
                        </a>
                    @endif
                @endguest
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="w-full pb-20 bg-white relative">
        <div class="w-full h-screen flex">
            <!-- Bagian Gambar (50%) -->
            <div class="hidden lg:flex w-1/2 h-full items-center justify-center overflow-hidden">
                <img src="{{ asset('images/assets/about_home.svg') }}" class="w-full h-full object-cover">
            </div>

            <!-- Bagian Tulisan -->
            <div class="w-full lg:w-1/2 h-full flex flex-col justify-center px-12 md:px-20">
                <div class="text-center">
                    <h2 class="text-4xl md:text-5xl font-bold uppercase">
                        About Us
                    </h2>
                    <p class="text-sm text-gray-600">Local Stray Barber Since 2017</p>
                </div>

                <div class="mt-10 text-center">
                    <div class="w-1/3 md:w-1/2 border-b-2 border-gray-800 mr-auto my-2"></div>
                    <p class="text-red-600 text-2xl md:text-4xl font-semibold uppercase">
                        "We Repair Homemade Haircut"
                    </p>
                    <p class="text-2xl md:text-4xl font-light mt-4 leading-relaxed">
                        Dengan tukang cukur yang berpengalaman, kami siap membantu Anda mendapatkan gaya rambut terbaik
                        yang sesuai dengan keinginan Anda.
                    </p>
                    <div class="w-1/3 md:w-1/2 border-b-2 border-gray-800 ml-auto my-4"></div>
                </div>

                <div class="mt-6 flex justify-center md:justify-end">
                    <a href="{{ route('about') }}"
                        class="bg-[#f91118] hover:bg-red-400 text-white text-lg md:text-xl px-6 md:px-8 py-3 md:py-4 rounded-md font-semibold uppercase">
                        Selengkapnya...
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- barberman --}}
    <section id="barberman" class="w-full pb-20 bg-white relative px-4 sm:px-6 md:px-10 ">
        <div class="text-center">
            <h2 class="text-4xl md:text-5xl font-bold uppercase">Barberman</h2>
            <p class="text-sm text-gray-600 max-w-lg mx-auto">
                Setiap Tukang Cukur Dari Barbershop Kami berkomitmen untuk memberikan layanan yang luar biasa,
                memastikan Anda meninggalkan toko kami dengan penampilan dan perasaan terbaik.
            </p>
        </div>

        <div class="relative mt-10 px-4 md:px-32 pt-20">
            <!-- Container slider: overflow-hidden untuk menyembunyikan konten di luar viewport -->
            <div class="relative overflow-hidden w-full">
                <div id="sliderWrapper" class="flex justify-center items-center overflow-hidden w-full h-[674px]">
                    <div id="slider" class="flex transition-transform duration-500 ease-in-out">
                        @foreach ($barbermen as $index => $barber)
                            <div class="barber-box relative flex-shrink-0 flex items-center justify-center cursor-pointer transition-all duration-500 w-[270.75px] md:w-[636px] h-[674px] bg-white md:bg-[#0A0A12]"
                                data-index="{{ $index }}">
                                <img class="barber-image w-full h-auto object-contain max-h-[500px] md:max-h-[574px]"
                                    src="{{ $barber->photo ? asset('storage/' . $barber->photo) : asset('images/assets/barberman.svg') }}"
                                    alt="{{ $barber->name }}">
                                <!-- Info (disembunyikan dulu) -->
                                <div class="infoText absolute top-4 left-4 text-white hidden">
                                    <h3 class="text-3xl">{{ $barber->name }}</h3>
                                    <p class="text-xl text-gray-300">Barberman</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-2xl">⭐</span>
                                        <span class="text-xl">
                                            {{ fmod($barber->average_rating, 1) == 0 ? number_format($barber->average_rating, 0) : number_format($barber->average_rating, 1) }}
                                            <span
                                                class="openReviewModal cursor-pointer hover:underline text-sm text-gray-300"
                                                data-barber-id="{{ $barber->id }}">
                                                ({{ $barber->reviews_count }} ulasan)
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="infoIcons absolute top-4 right-4 flex flex-col space-y-4 hidden">
                                    <a href="https://instagram.com/{{ $barber->instagram }}" target="_blank">
                                        <img src="{{ asset('images/icons/icon_ig.svg') }}" class="w-12 h-12">
                                    </a>
                                    <a href="{{ route('booking.index') }}">
                                        <img src="{{ asset('images/icons/icon_calendar.svg') }}" class="w-12 h-12">
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button id="prevBtn"
                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-2 rounded-full z-10 md:hidden">
                    ←
                </button>
                <button id="nextBtn"
                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-2 rounded-full z-10 md:hidden">
                    →
                </button>
            </div>
            <div id="reviewModal"
                class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
                <div class="bg-white p-6 rounded-lg w-1/3">
                    <h2 class="text-xl font-semibold mb-4">Review Barberman</h2>
                    <div id="reviewList">
                        <p class="text-gray-500 text-center" id="reviewLoading">Memuat review...</p>
                    </div>
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" id="closeReviewModal"
                            class="px-4 py-2 bg-gray-500 text-white rounded">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Gallery --}}
    <section id="gallery" class="w-full pb-20 bg-white relative px-4 sm:px-6 md:px-0">
        <div class="text-center">
            <h2 class="text-4xl md:text-5xl font-bold uppercase">Gallery</h2>
            <p class="text-sm text-gray-600 max-w-lg mx-auto">
                Beberapa hasil dari layanan yang kami berikan
            </p>
        </div>
        <div class="flex flex-col md:flex-row items-start md:items-center w-full pt-20 pb-[60px]">
            <!-- Kiri: Teks -->
            <div class="max-w-full sm:max-w-[421px] mx-4 sm:ml-[104px] sm:mr-[66px] mb-4">
                <p class="mt-4 text-base sm:text-xl text-gray-800">
                    Tukang cukur kami yang terdiri dari tukang cukur berpengalaman
                    berdedikasi pada keahlian mereka. Kami tetap mengikuti teknik
                    potongan dan gaya rambut terbaru untuk memastikan Anda mendapatkan
                    layanan terbaik. Jika Anda membutuhkan <span class="font-bold text-red-500">REKOMENDASI</span>
                    untuk potongan rambut sempurna yang sesuai dengan gaya dan kepribadian Anda,
                    jangan ragu untuk <span class="font-bold text-red-500">KLIK TOMBOL DI BAWAH</span> dan jelajahi
                    saran pilihan kami.
                </p>
                <div class="flex justify-end">
                    <a href="{{ route('rekomendasi.index') }}"
                        class="mt-4 bg-black text-white py-2 px-4 rounded-md shadow-md hover:bg-gray-800">
                        DAPATKAN REKOMENDASI
                    </a>
                </div>
            </div>

            <div class="relative w-full md:w-[950px] overflow-hidden">
                <div id="slideshow" class="flex gap-[19px] transition-transform duration-500 ease-in-out">
                    @foreach ($gallery as $g)
                        <div class="flex-shrink-0 w-full md:w-[304px] h-[347px] rounded-lg shadow-md overflow-hidden">
                            <img src="{{ asset('storage/' . $g->image) }}" class="w-full h-full object-cover"
                                alt="Slide">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="w-full flex flex-col lg:flex-row justify-center items-center gap-10 px-4 lg:px-0">
            <div class="flex-1 max-w-lg text-center lg:text-left">
                <h1 class="text-2xl md:text-4xl font-bold">JIKA ANDA SEORANG <span class="text-[#f91118]">PRIA</span>,
                </h1>
                <p class="mt-2 text-2xl md:text-4xl">Anda bisa mendapatkan layanan potong rambut terbaik dari kami
                    hanya
                    dengan membayar</p>
                <div class="flex flex-col items-center lg:items-start my-4">
                    <div class="w-32 ml-[70px] border-t-2 border-gray-800 mb-2"></div>
                    <p class="text-4xl font-bold text-[#f91118]">RP. {{ number_format($service->price, 0, ',', '.') }}
                    </p>
                    <div class="w-32 mr-[70px] border-t-2 border-gray-800 mt-2 mb-2"></div>
                </div>
                <a href="{{ route('booking.index') }}"
                    class="bg-[#f91118] hover:bg-red-400 text-white px-6 py-2 rounded-md font-bold uppercase">
                    Book Now
                </a>
            </div>

            <!-- Operating Hours -->
            <div class="flex flex-col gap-6">
                <div class="bg-black text-white px-[100px] py-2 text-center">
                    <p class="font-bold text-2xl md:text-3xl">JAM BUKA</p>
                    <p class="text-lg md:text-2xl">
                        {{ \Carbon\Carbon::parse($operational->open_time)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($operational->close_time)->format('H:i') }}
                    </p>
                </div>
                <div class="bg-[#f91118] text-white p-4 text-center">
                    <p class="font-bold text-2xl md:text-3xl">JAM ISTIRAHAT</p>
                    <p class="text-lg md:text-2xl">12.00 - 13.00</p>
                    <p class="text-lg md:text-2xl">17.00 - 18.30</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact --}}
    <section id="contact" class="w-full pb-20 px-4 sm:px-6 md:px-10  bg-white relative">
        <div class="text-center">
            <h2 class="text-4xl md:text-5xl font-bold uppercase">Contact Us</h2>
            <p class="text-sm text-gray-600 max-w-lg mx-auto">
                Hubungi kami dan katakan apa yang Anda butuhkan
            </p>
        </div>
        <section class="flex flex-col lg:flex-row items-center justify-center gap-8 pt-[66px]">
            <!-- Google Maps -->
            <div class="w-full max-w-md">
                <iframe class="w-full h-64 md:h-80 rounded-lg shadow-lg"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.087473884332!2d112.2693965748606!3d-6.880123593118778!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e77c173d490d997%3A0x4eee199467dfc2c9!2sLUCKSIDE%20BARBERSHOP!5e0!3m2!1sid!2sid!4v1739967883530!5m2!1sid!2sid"
                    allowfullscreen="" loading="lazy">
                </iframe>
            </div>

            <!-- Form -->
            <div class="w-full max-w-lg">
                <h2 class="text-4xl font-bold text-center mb-4">TULIS PESANMU</h2>
                <form id="waForm" class="space-y-4">
                    <div class="flex gap-4">
                        <input type="text" id="firstName" placeholder="Nama Depan"
                            class="w-1/2 border p-2 rounded" required>
                        <input type="text" id="lastName" placeholder="Nama Belakang"
                            class="w-1/2 border p-2 rounded">
                    </div>
                    <select id="subject" class="w-full border p-2 rounded">
                        <option value="pertanyaan">Pertanyaan</option>
                        <option value="keluhan">Keluhan</option>
                        <option value="pesanan">Pesanan</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                    <textarea id="message" placeholder="Deskripsi" class="w-full border p-2 rounded h-32" required></textarea>
                    <button type="submit"
                        class="bg-[#f91118] hover:bg-red-400 text-white px-6 py-2 rounded-md font-bold justify-end ml-auto block">KIRIM</button>
                </form>
            </div>
        </section>
    </section>

    @vite(['resources/js/box-barberman.js', 'resources/js/scroll-top.js', 'resources/js/slideshow-gallery.js', 'resources/js/contact.js'])
</x-app-layout>
