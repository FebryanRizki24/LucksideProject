<x-app-layout>
    <div class="relative w-full min-h-screen flex flex-col">
        <!-- Wrapper untuk gambar -->
        <div class="relative bg-[#4c4c4c]">
            <img src="images/assets/about_1.svg" alt="about" class="w-full h-full object-cover mix-blend-multiply">

            <!-- Overlay (Tulisan di atas gambar) -->
            <div class="absolute inset-0 flex items-center justify-center text-white text-center px-4">
                <h1 class="text-3xl sm:text-5xl md:text-6xl font-oswald uppercase drop-shadow-lg text-[#F91118]">
                    Local Stray Barber <br> Since 2017
                </h1>
            </div>
        </div>

        <div class="w-full pt-10 sm:pt-20 relative flex flex-col lg:flex-row">
            <!-- Bagian Gambar (50%) -->
            <div class="hidden lg:flex w-1/2 h-full items-center justify-center overflow-hidden">
                <img src="{{ asset('images/assets/logo.svg') }}" class="w-40 sm:w-60 md:w-[500px]">
            </div>

            <!-- Bagian Tulisan -->
            <div class="w-full lg:w-1/2 h-full flex flex-col justify-center px-6 sm:px-12 md:px-20">
                <p class="text-base sm:text-lg md:text-2xl font-light mt-4 leading-relaxed">
                    Salah satu barbershop populer yang terletak di Desa Sedayulawas sejak 2017. Menyajikan berbagai
                    macam potongan rambut sesuai permintaan Anda. Dengan tukang cukur yang berpengalaman, kami siap
                    memberikan pelayanan terbaik dan siap memberikan jaminan jika hasil yang diinginkan tidak sesuai.
                </p>
            </div>
        </div>

        <div class="pt-10 sm:pt-20 px-4 sm:px-10 md:px-[140px]">
            <div class="bg-black text-white p-4 sm:p-6 rounded-[20px] flex flex-col sm:flex-row justify-around items-center gap-4 sm:gap-0">
                <div class="text-center max-w-[240px]">
                    <p class="text-[#F91118] text-4xl sm:text-6xl font-bold">2017</p>
                    <p class="text-xl sm:text-3xl">Tahun Pertama Berdiri</p>
                </div>
                <div class="text-center max-w-[240px]">
                    <p class="text-[#F91118] text-4xl sm:text-6xl font-bold">3</p>
                    <p class="text-xl sm:text-3xl">Barbershop Berpengalaman</p>
                </div>
                <div class="text-center max-w-[240px]">
                    <p class="text-[#F91118] text-4xl sm:text-6xl font-bold">100</p>
                    <p class="text-xl sm:text-3xl">Pelanggan Setiap Minggunya</p>
                </div>
            </div>
        </div>

        <div class="py-10 sm:py-20 flex justify-center items-center px-4 sm:px-10 md:px-[140px]">
            <div class="w-full">
                <!-- FAQ 1 -->
                <div class="border border-red-500 rounded-lg mb-2 overflow-hidden">
                    <button class="flex justify-between items-center w-full p-4 text-left font-semibold" data-toggle="1">
                        <span class="flex text-lg sm:text-2xl items-center"><span class="text-lg sm:text-2xl font-bold mr-2">❓</span> Dimana aku bisa menemukan Luckside Barbershop</span>
                        <span id="icon-1">▼</span>
                    </button>
                    <div id="content-1" class="hidden bg-black text-white p-4">
                        Luckside Barbershop berlokasi di Jl Melati, Sedayulawas Brondong Lamongan
                    </div>
                </div>
        
                <!-- FAQ 2 -->
                <div class="border border-red-500 rounded-lg mb-2 overflow-hidden">
                    <button class="flex justify-between items-center w-full p-4 text-left font-semibold" data-toggle="2">
                        <span class="flex text-lg sm:text-2xl items-center"><span class="text-lg sm:text-2xl font-bold mr-2">❓</span> Apa saja layanan yang diberikan oleh Luckside Barbershop</span>
                        <span id="icon-2">▼</span>
                    </button>
                    <div id="content-2" class="hidden bg-black text-white p-4">
                        Kami menyediakan layanan potong rambut khusus untuk pria.
                    </div>
                </div>
        
                <!-- FAQ 3 -->
                <div class="border border-red-500 rounded-lg mb-2 overflow-hidden">
                    <button class="flex justify-between items-center w-full p-4 text-left font-semibold" data-toggle="3">
                        <span class="flex text-lg sm:text-2xl items-center"><span class="text-lg sm:text-2xl font-bold mr-2">❓</span> Jam berapa Luckside Barbershop beroperasi</span>
                        <span id="icon-3">▼</span>
                    </button>
                    <div id="content-3" class="hidden bg-black text-white p-4">
                        Kami buka setiap hari dari pukul 08:00 hingga 21:00.
                    </div>
                </div>
            </div>
        </div>
    </div>
    @vite(['resources/js/toogle-about.js'])
</x-app-layout>