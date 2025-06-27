<x-app-layout>
    <div class="bg-white px-10 pb-20 md:px-[82px]">
        <div class="flex flex-col md:flex-row justify-center items-center px-4 md:px-16 lg:px-32 py-10 gap-8">
            <!-- Kotak Kiri dengan Gambar -->
            <div class="w-full md:w-1/2 flex justify-center">
                @foreach ($faceShapes as $faceShape)
                    @if ($shape === $faceShape->name)
                        <img src="{{ asset('storage/shapes/' . $faceShape->name . '.png') }}" 
                             alt="{{ ucfirst($faceShape->name) }}" 
                             class="w-[300px] h-[300px] md:w-[500px] md:h-[500px] object-contain rounded-lg shadow-lg">
                    @endif
                @endforeach
            </div>
            
            <!-- Layar Kanan dengan Tulisan -->
            <div class="w-full md:w-1/2 text-left px-4 md:ml-[50px] text-center md:text-left">
                @foreach ($faceShapes as $faceShape)
                    @if ($shape === $faceShape->name)
                        <p class="text-xl md:text-3xl text-gray-800 leading-relaxed max-w-[500px]">
                            {!! nl2br($faceShape->deskripsi) !!}
                        </p>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="text-center mb-[39px] mt-[275px]">
            <h2 class="text-4xl md:text-5xl font-bold uppercase text-[#f91118]">REKOMENDASI</h2>
        </div>
        <div class="relative w-full overflow-x-auto flex justify-center">
            <div id="scrollable_recommendation" class="flex gap-[19px] flex-nowrap">
                @foreach ($hairstyles as $hairstyle)
                <a href="{{ route('rekomendasi.show', $hairstyle->id) }}" 
                    class="relative w-[235px] h-[235px] rounded-lg shadow-md flex-shrink-0 overflow-hidden block group">
                    
                     <img src="{{ asset('storage/' . $hairstyle->photo) }}" 
                          alt="{{ $hairstyle->name }}" 
                          class="w-full h-full object-cover rounded-lg transition-transform duration-300 ease-in-out group-hover:scale-110">

                          <div class="absolute bottom-0 left-0 w-full bg-black bg-opacity-60 text-white text-center py-2">
                            <p class="text-lg font-semibold">
                                {{ $hairstyle->name }}
                                <span class="text-yellow-400 text-base ml-1">
                                    ⭐ 
                                    @if ($hairstyle->average_rating > 0)
                                        {{ fmod($hairstyle->average_rating, 1) == 0 ? number_format($hairstyle->average_rating, 0) : number_format($hairstyle->average_rating, 1) }} / 5
                                    @else
                                        -
                                    @endif
                                </span>
                            </p>                            
                        </div>                        
                 </a>                 
                @endforeach
            </div>
        </div>        
    </div>
    @vite(['resources/js/slideshow_detailrecommen.js'])
</x-app-layout>