<x-app-layout>
    <div id="booking-section" class="relative w-full min-h-screen flex flex-col bg-white py-20">
        <div class="w-full flex overflow-auto">
            <!-- Bagian Form -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center py-10 px-12 md:px-20">
                <x-authentication-card class="overflow-auto max-h-[90vh]">
                    <x-slot name="logo">
                        <p class="text-6xl font-bold font-oswald" style="padding-bottom: 85px;">BOOKING</p>
                    </x-slot>

                    <x-validation-errors class="mb-4" />

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form id="booking-form" action="{{ route('booking.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <x-label for="name" value="{{ __('USERNAME') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="Auth::user()?->name" required readonly />
                        </div>

                        <div class="flex flex-row justify-between">
                            <div>
                                <label for="date" class="block text-lg font-medium">DATE</label>
                                <input type="text" id="date" name="date"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-4 py-2"
                                    required>
                            </div>
                            <div>
                                <label for="time" class="block text-lg font-medium">TIME</label>
                                <input type="text" id="time" name="time"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-4 py-2 cursor-pointer bg-gray-100"
                                    readonly required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-lg font-medium">BARBERMAN</label>
                            <select name="barberman_id"
                                class="w-full px-3 py-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required>
                                <option value="">Pilih Barberman</option>
                                @foreach ($barbermans as $barberman)
                                    <option value="{{ $barberman->id }}">{{ $barberman->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-lg font-medium">BUTUH REKOMENDASI?</label>
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="rekomendasiCheckbox" name="butuh_rekomendasi" value="1"
                                    class="rounded">
                                <label for="rekomendasiCheckbox" class="text-sm text-gray-700">Centang jika ingin sistem
                                    merekomendasikan hairstyle</label>
                            </div>
                        </div>

                        <div id="faceShapeContainer" class="mt-3 hidden">
                            <label for="face_shape" class="block text-lg font-medium">BENTUK WAJAH</label>
                            <select id="faceShapeSelect" name="face_shape"
                                class="w-full px-3 py-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Pilih Bentuk Wajah</option>
                                @foreach ($faceshapes as $faceshape)
                                    <option value="{{ $faceshape->id }}"
                                        data-image="{{ asset('storage/shapes/' . $faceshape->name . '.png') }}"
                                        data-deskripsi="{{ $faceshape->deskripsi }}">
                                        {{ $faceshape->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="faceshape-preview"
                            class="hidden flex flex-col sm:flex-row gap-4 sm:gap-6 items-start border p-4 rounded shadow-md mt-4">
                            <div class="flex-shrink-0 w-full sm:w-1/3">
                                <img id="faceshape-preview-img" src="" alt="Preview Face Shape"
                                    class="mx-auto rounded shadow-md border max-w-full h-auto object-contain" />
                            </div>
                            <div class="w-full sm:w-2/3">
                                <p id="faceshape-name-label"
                                    class="text-base sm:text-lg font-semibold text-gray-700 break-words"></p>
                                <p id="faceshape-desc-label"
                                    class="mt-2 text-sm sm:text-base text-gray-600 whitespace-pre-line break-words">
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-lg font-medium">HAIRSTYLE</label>

                            <select id="hairstyle-select" name="hairstyle_id"
                                class="select2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-3 py-2">
                                <option value="">Pilih Hairstyle</option>
                                @foreach ($hairstyles as $hairstyle)
                                    <option value="{{ $hairstyle->id }}"
                                        data-image="{{ asset('storage/' . $hairstyle->photo) }}"
                                        data-deskripsi="{{ $hairstyle->deskripsi }}">
                                        {{ $hairstyle->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div id="hairstyle-preview"
                                class="hidden flex flex-col sm:flex-row gap-4 sm:gap-6 items-start border p-4 rounded shadow-md">
                                <!-- Kiri: Gambar -->
                                <div class="flex-shrink-0 w-full sm:w-1/3">
                                    <img id="hairstyle-preview-img" src="" alt="Preview Hairstyle"
                                        class="mx-auto rounded shadow-md border max-w-full h-auto object-contain" />
                                </div>

                                <!-- Kanan: Nama & Deskripsi -->
                                <div class="w-full sm:w-2/3">
                                    <p id="hairstyle-name-label"
                                        class="text-base sm:text-lg font-semibold text-gray-700 break-words"></p>
                                    <p id="hairstyle-desc-label"
                                        class="mt-2 text-sm sm:text-base text-gray-600 whitespace-pre-line break-words">
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-lg font-medium">DESCRIPTION</label>
                            <textarea name="deskripsi"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-3 py-2"
                                rows="3"></textarea>
                        </div>

                        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                            <button type="button" onclick="history.back()"
                                class="w-full md:w-1/2 border text-red-500 py-2 rounded">CANCEL</button>
                            <button type="button" id="pay-button"
                                class="w-full md:w-1/2 bg-red-500 text-white py-2 rounded flex justify-center items-center gap-2">
                                <span>BOOK NOW</span>
                                <svg id="book-spinner" class="hidden animate-spin h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </x-authentication-card>
            </div>
            <!-- Bagian Gambar (50%) -->
            <div class="hidden lg:flex w-1/2 items-center justify-center overflow-hidden">
                <img src="{{ asset('images/assets/auth_pict.svg') }}" class="w-full h-full mix-blend-difference">
            </div>
        </div>
    </div>
    <!-- Modal Pilihan Time Slot -->
    <div id="timeModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold mb-4">Available Time Slots</h2>
            <ul id="timeSlots" class="space-y-2 max-h-60 overflow-y-auto">
                <li class="p-2 bg-gray-100 text-center rounded cursor-pointer hover:bg-gray-200">Loading...</li>
            </ul>
            <div class="mt-4 text-right">
                <button type="button" onclick="closeTimeModal()"
                    class="px-4 py-2 bg-gray-500 text-white rounded">Close</button>
            </div>
        </div>
    </div>

    <!-- Modal Ketentuan Pengguna -->
    <div id="termsModal" class="hidden fixed inset-0 z-50 bg-white flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-xl w-full">
            <h2 class="text-xl font-bold mb-4 text-center">Ketentuan Booking Online</h2>
            <div class="text-sm text-gray-700 space-y-3 max-h-64 overflow-y-auto px-1">
                <p>1. User hanya dapat melakukan <strong>1 kali booking online</strong> dalam satu waktu, hingga booking
                    tersebut selesai.</p>
                <p>2. Booking hanya dapat dilakukan dengan <strong>pembayaran online</strong>.</p>
                <p>3. User wajib hadir minimal <strong>10 menit sebelum waktu yang telah dijadwalkan</strong>.</p>
                <p>4. Setibanya di lokasi, user <strong>harus melakukan konfirmasi kedatangan</strong> kepada admin.</p>
                <p>5. Pembatalan dapat dilakukan maksimal <strong>2 jam sebelum jadwal</strong>. Dana akan dikembalikan
                    <strong>50%</strong> melalui komunikasi langsung via chat admin.
                </p>
                <p>6. Jika user <strong>terlambat datang</strong>, maka antrean akan dipindah ke <strong>paling belakang
                        antrean</strong> yang sedang berjalan di lokasi.</p>
            </div>
            <div class="mt-6 text-right">
                <button id="agree-button" onclick="acceptTerms()"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded flex items-center justify-center gap-2">
                    <span>Saya Setuju</span>
                    <svg id="agree-spinner" class="hidden animate-spin h-5 w-5 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @vite(['resources/js/booking-datetime.js'])

</x-app-layout>
