<x-app-layout>
    <div class="relative w-full min-h-screen flex flex-col">
        <!-- Wrapper untuk gambar -->
        <div class="relative bg-[#4c4c4c]">
            <img src="images/assets/about_1.svg" alt="about" class="w-full h-full object-cover mix-blend-multiply">

            <!-- Overlay (Tulisan di atas gambar) -->
            <div class="absolute inset-0 flex items-center justify-center text-white text-center px-4">
                <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-oswald uppercase drop-shadow-lg text-[#F91118] max-w-[579px]">
                    DAPATKAN POTONGAN RAMBUT TERBAIK
                </h1>
            </div>
        </div>

        <!-- Konten utama -->
        <div class="py-10 px-5 md:px-10 lg:px-[140px]">
            <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl text-center">
                Potongan Rambut Terbaik Adalah Potongan Yang Sesuai Dan Cocok Pada Tubuh Kita, Salah Satunya Bentuk Wajah. Bentuk Wajah Mempunyai Peran Yang Cukup Penting Bagi Model Potongan Rambut Yang Kalian Pilih. Kami Disini Akan Memberikan Sebuah Edukasi Tentang Peran Bentuk Wajah Dalam Memilih Model Potongan Rambut.
            </p>
        </div>

        <!-- Pilihan bentuk wajah -->
        <div class="flex flex-wrap justify-center gap-5 sm:gap-8 px-5 md:px-10 lg:px-[35px] py-20">
            @foreach ($faceShapes as $shape)
                <a href="{{ route('rekomendasi.detail', ['shape' => $shape->name]) }}" 
                   class="w-[150px] h-[150px] sm:w-[180px] sm:h-[180px] md:w-[200px] md:h-[200px] lg:w-[235px] lg:h-[235px] 
                          bg-gray-300 rounded-lg shadow-md flex-shrink-0 transition-transform transform hover:scale-105">
                    <img src="{{ asset('storage/shapes/' . $shape->name . '.png') }}" 
                         alt="{{ ucfirst($shape->name) }}" 
                         class="w-full h-full object-contain rounded-lg">
                </a>
            @endforeach
        </div>

        <!-- Tombol CTA -->
        {{-- <div class="flex justify-center lg:justify-end px-5 md:px-10 lg:pr-[82px] py-10">
            <a href="#" class="mt-4 bg-black text-white py-3 px-6 rounded-md shadow-md hover:bg-gray-800 text-sm sm:text-base md:text-lg">
                KENALI BENTUK WAJAH ANDA
            </a>
        </div> --}}
    </div>
</x-app-layout>