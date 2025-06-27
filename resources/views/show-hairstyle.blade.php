<x-app-layout>
    <div class="bg-white px-10 pb-20 md:px-[82px]">
        <div class="flex flex-col md:flex-row justify-center items-center px-4 md:px-16 lg:px-32 py-10 gap-8">
            <!-- Kotak Kiri dengan Gambar -->
            <div class="w-full md:w-1/2 flex justify-center">
                <img src="{{ asset('storage//' . $hairstyle->photo) }}" alt="{{ $hairstyle->name }}"
                    class="w-[300px] h-[300px] object-cover rounded-lg shadow-md">
            </div>

            <!-- Layar Kanan dengan Deskripsi -->
            <div class="w-full md:w-1/2 text-left px-4 md:ml-[50px] text-center md:text-left">
                <h2 class="text-3xl font-bold text-gray-800">{{ $hairstyle->name }}</h2>
                <p class="mt-4 text-gray-600 text-xl leading-relaxed">
                    {{ $hairstyle->deskripsi }}
                </p>
            </div>
        </div>

        <div class="text-center mb-[39px] mt-[138px]">
            <h2 class="text-4xl md:text-5xl font-bold uppercase text-[#f91118]">IMPLEMENTASI</h2>
        </div>

        <div x-data="{
            open: false,
            imgSrc: '',
            currentReview: {},
            otherImages: []
        }">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @forelse ($images as $review)
                    @php
                        $firstImage = $review->images->first();
                    @endphp
                    @if ($firstImage)
                        <div class="w-[235px] h-[235px] rounded-lg shadow-md overflow-hidden bg-white cursor-pointer"
                            @click="open = true; 
                                    imgSrc = '{{ asset('storage/' . $firstImage->image) }}';
                                    currentReview = {{ $review->toJson() }};
                                    otherImages = {{ $review->images->pluck('image')->toJson() }};
                            ">
                            <img src="{{ asset('storage/' . $firstImage->image) }}"
                                class="w-full h-[190px] object-cover rounded shadow-md" alt="Review Image">
                            <div class="text-center py-2 text-sm font-medium text-gray-700">
                                {{ $review->user->name }}
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-span-full text-center text-gray-500">
                        Belum ada review pengguna.
                    </div>
                @endforelse
            </div>

            <!-- Modal -->
            <div x-show="open" x-transition
                class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                @click.self="open = false">

                <div
                    class="bg-white rounded-lg shadow-lg flex flex-col md:flex-row w-full max-w-4xl overflow-hidden relative">

                    <!-- Tombol Close -->
                    <button @click="open = false"
                        class="absolute top-2 right-2 text-gray-600 hover:text-red-600 text-2xl font-bold z-10">
                        &times;
                    </button>

                    <!-- Kanan: Gambar Utama -->
                    <div class="w-full md:w-1/2 p-3 flex justify-center items-center bg-gray-100">
                        <img :src="imgSrc" class="max-h-[400px] object-contain rounded shadow-md">
                    </div>

                    <!-- Kiri: Detail Review -->
                    <div class="w-full md:w-1/2 p-5 flex flex-col">
                        <h3 class="text-lg font-bold text-gray-800" x-text="currentReview.user.name"></h3>
                        <div class="text-yellow-500 mt-2" x-html="'★'.repeat(currentReview.rating || 5)"></div>
                        <p class="mt-3 text-sm text-gray-700" x-text="currentReview.comment"></p>

                        <!-- Thumbnail List -->
                        <div class="grid grid-cols-3 gap-2 mt-5">
                            <template x-for="img in otherImages" :key="img">
                                <img :src="'/storage/' + img"
                                    class="w-full h-20 object-cover rounded cursor-pointer border hover:border-red-500"
                                    @click="imgSrc = '/storage/' + img">
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>
