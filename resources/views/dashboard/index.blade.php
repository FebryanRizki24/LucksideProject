@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
    @if (Auth::user()->hasRole('admin'))
        {{-- {{ dd($data) }} --}}

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white shadow p-4 rounded-lg">
                <h2 class="font-medium text-gray-600">Barberman Aktif</h2>
                <p class="text-3xl font-bold mt-2">{{ $data['active_barbermen'] }}</p>
            </div>
            <div class="bg-white shadow p-4 rounded-lg">
                <h2 class="font-medium text-gray-600">Total User</h2>
                <p class="text-3xl font-bold mt-2">{{ $data['total_users'] }}</p>
            </div>
            <div class="bg-white shadow p-4 rounded-lg">
                <h2 class="font-medium text-gray-600">Total Hairstyle</h2>
                <p class="text-3xl font-bold mt-2">{{ $data['total_hairstyles'] }}</p>
            </div>
            <div class="bg-white shadow p-4 rounded-lg">
                <h2 class="font-medium text-gray-600">Total Booking</h2>
                <p class="text-3xl font-bold mt-2">{{ $data['total_booking'] }}</p>
            </div>
        </div>
        <div class="flex justify-end mb-4">
            <form method="GET" action="" class="flex items-center space-x-2">
                <label for="filter" class="text-sm text-gray-600">Filter Waktu:</label>
                <select name="filter" id="filter"
                    class="border border-gray-300 rounded px-3 py-1 focus:ring focus:ring-indigo-200 text-sm">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="week" {{ request('filter') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                </select>
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm">Terapkan</button>
            </form>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white shadow p-4 rounded-lg">
                <h2 class="font-medium text-gray-600">Booking Terkonfirmasi</h2>
                <p class="text-3xl font-bold mt-2">{{ $data['booking_konfirmasi'] }}</p>
            </div>
            <div class="bg-white shadow p-4 rounded-lg">
                <h2 class="font-medium text-gray-600">Booking Pending</h2>
                <p class="text-3xl font-bold mt-2">{{ $data['booking_pending'] }}</p>
            </div>
            <div class="bg-white shadow p-4 rounded-lg">
                <h2 class="font-medium text-gray-600">Booking Selesai</h2>
                <p class="text-3xl font-bold mt-2">{{ $data['booking_selesai'] }}</p>
            </div>
            <div class="bg-white shadow p-4 rounded-lg">
                <h2 class="font-medium text-gray-600">Booking Dibatalkan</h2>
                <p class="text-3xl font-bold mt-2">{{ $data['booking_batal'] }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div class="bg-white shadow p-4 rounded-lg">
                <h3 class="font-medium text-gray-600 text-center">Jumlah Booking</h3>
                <div class="flex justify-center">
                    <canvas id="bookingChart" class="w-full h-64"></canvas>
                </div>
            </div>

            <div class="bg-white shadow p-4 rounded-lg">
                <h3 class="font-medium text-gray-600 text-center mb-4">Antrean Harian</h3>
                <div class="text-right mb-4">
                    <button id="processQueueBtn" class="relative bg-blue-500 text-white px-4 py-2 rounded">
                        Proses Antrean
                        <span id="notificationDot"
                            class="absolute top-0 right-0 bg-red-500 w-3 h-3 rounded-full hidden"></span>
                    </button>
                </div>
                <div class="mb-4">
                    <label for="barberDropdown" class="block text-sm font-medium text-gray-700">Pilih Barberman:</label>
                    <select id="barberDropdown"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        <option value="" disabled selected>-- Pilih Barberman --</option>
                        @foreach ($data['daily_queue'] as $barber => $customers)
                            <option value="{{ $barber }}">{{ $barber }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="queueWrapper" class="bg-white rounded-lg shadow p-4 hidden">
                    <ol id="queueList" class="list-decimal list-inside space-y-2"></ol>
                    <div id="viewAllWrapper" class="text-center mt-4 hidden">
                        <button id="viewAllBtn" class="text-sm text-blue-600 hover:underline">
                            Lihat Semua Antrean (<span id="totalQueue"></span>)
                        </button>
                    </div>
                </div>

                <!-- Modal -->
                <div id="queueModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50">
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Daftar Antrean Lengkap</h2>
                        <ol id="fullQueueList" class="list-decimal list-inside space-y-2 max-h-80 overflow-y-auto"></ol>
                        <button id="closeModal"
                            class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @elseif (Auth::user()->hasRole('user'))
        {{-- {{ dd($data) }} --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            @if ($data['booking_aktif'])
                <div class="bg-white shadow p-4 rounded-lg text-center" id="jadwal-booking-aktif">
                    <h3 class="font-medium text-gray-600 text-center mb-4">Jadwal Booking Aktif</h3>

                    <p class="text-lg font-semibold" id="tanggal-booking">{{ $data['booking_aktif']['tanggal'] }}</p>
                    <p class="text-gray-600" id="jam-booking">{{ $data['booking_aktif']['jam'] }} WIB</p>

                    <p class="text-sm text-gray-500 mt-2">Barberman:
                        <span class="font-medium">{{ $data['booking_aktif']['barberman'] }}</span>
                    </p>
                    <p class="text-sm text-gray-500">Hairstyle:
                        <span class="font-medium">{{ $data['booking_aktif']['hairstyle'] }}</span>
                    </p>
                    <p class="text-sm text-gray-500 mt-2">Status Pembayaran:
                        <span
                            class="font-semibold {{ $data['booking_aktif']['status_pembayaran'] === 'paid' ? 'text-green-600' : 'text-red-500' }}">
                            {{ $data['booking_aktif']['status_pembayaran'] === 'paid' ? 'Sudah Dibayar' : 'Belum Bayar' }}
                        </span>
                    </p>

                    <div class="flex justify-center gap-2 mt-4">
                        <button data-id="{{ $data['booking_aktif']['booking_id'] }}"
                            class="btn-detail bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                            Detail
                        </button>

                        @if ($data['booking_aktif']['status_pembayaran'] === 'paid')
                            <button data-id="{{ $data['booking_aktif']['booking_id'] }}"
                                class="btn-completed bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm">
                                Selesaikan
                            </button>
                        @else
                            <button data-id="{{ $data['booking_aktif']['booking_id'] }}"
                                class="btn-pay bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm">
                                Bayar
                            </button>
                        @endif

                        <button data-id="{{ $data['booking_aktif']['booking_id'] }}"
                            data-payment-status="{{ $data['booking_aktif']['status_pembayaran'] }} }}"
                            data-booking-status="{{ $data['booking_aktif']['status_booking'] }} }}"
                            class="btn-batal bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">
                            Batalkan
                        </button>
                    </div>
                </div>
            @else
                <div class="bg-white shadow p-4 rounded-lg text-center">
                    <h3 class="font-medium text-gray-600 text-center mb-4">Jadwal Booking Aktif</h3>
                    <p class="text-gray-500">Tidak ada booking yang berjalan saat ini.</p>
                </div>
            @endif

            <div class="bg-white shadow p-4 rounded-lg">
                <h3 class="font-medium text-gray-600 text-center mb-4">Review</h3>

                @if ($data['review_belum'])
                    @foreach ($data['review_belum'] as $index => $booking)
                        <div
                            class="booking-card {{ $index > 2 ? 'hidden' : '' }} border border-gray-200 rounded-lg p-4 mb-4 flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">
                                    Kode Booking: <span class="font-semibold text-gray-800">{{ $booking['kode'] }}</span>
                                </p>
                                <p class="text-sm text-gray-700 mb-2">
                                    Belum ada review untuk aspek
                                    <span
                                        class="font-semibold">{{ implode(', ', $booking['aspek_belum_direview']) }}</span>
                                </p>
                            </div>
                            <button id="openReviewModal"
                                class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded"
                                data-id="{{ $booking['id'] }}" data-user-id="{{ auth()->user()->id }}"
                                data-kode="{{ $booking['kode'] }}">
                                Review
                            </button>

                        </div>
                    @endforeach

                    @if (count($data['review_belum']) > 3)
                        <div class="text-center">
                            <button onclick="document.getElementById('allReviewModal').classList.remove('hidden')"
                                class="text-blue-500 hover:underline text-sm mt-2">
                                Lihat semua
                            </button>
                        </div>
                    @endif
                @else
                    <p class="text-gray-500 text-center">Tidak ada yang bisa di review.</p>
                @endif
            </div>
        </div>

        <div id="reviewModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg w-1/3">
                <h2 class="text-xl font-semibold mb-4">Tambah Review <span id="review_kode"
                        class="font-medium text-gray-800"></span></h2>

                <!-- Pilihan Aspek -->
                <label for="review_aspect" class="block font-semibold mb-2">Aspek Review</label>
                <select id="review_aspect" class="w-full border rounded px-3 py-2 mb-4">
                    <option value="booking">Booking</option>
                    <option value="barberman">Barberman</option>
                    <option value="hairstyle">Hairstyle</option>
                </select>

                <!-- Pilihan Rating -->
                <label for="review_rating" class="block font-semibold mb-2">Rating</label>
                <select id="review_rating" class="w-full border rounded px-3 py-2 mb-4">
                    <option value="5">⭐⭐⭐⭐⭐</option>
                    <option value="4">⭐⭐⭐⭐</option>
                    <option value="3">⭐⭐⭐</option>
                    <option value="2">⭐⭐</option>
                    <option value="1">⭐</option>
                </select>

                <!-- Review -->
                <label for="review_text" class="block font-semibold mb-2">Review</label>
                <textarea id="review_text" class="w-full border rounded px-3 py-2 mb-4" rows="3"></textarea>

                <!-- Upload Gambar -->
                <div id="review_image_section">
                    <label for="review_images" class="block font-semibold mb-2">Upload Gambar</label>
                    <input type="file" id="review_images" multiple accept="image/*"
                        class="w-full border rounded px-3 py-2 mb-4">
                    <div id="imagePreview" class="grid grid-cols-3 gap-2 mb-4"></div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button id="closeReviewModal" class="px-4 py-2 bg-gray-500 text-white rounded">Batal</button>
                    <button id="submitReview" class="px-4 py-2 bg-blue-500 text-white rounded">Kirim</button>
                </div>
            </div>
        </div>

        <div id="allReviewModal"
            class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center hidden">
            <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 relative">
                <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600"
                    onclick="document.getElementById('allReviewModal').classList.add('hidden')">
                    &times;
                </button>
                <h4 class="text-lg font-semibold mb-4 text-center">Semua Booking Belum Direview</h4>

                <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                    @foreach ($data['review_belum'] as $booking)
                        <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">
                                    Kode Booking: <span class="font-semibold text-gray-800">{{ $booking['kode'] }}</span>
                                </p>
                                <p class="text-sm text-gray-700">
                                    Belum ada review untuk aspek
                                    <span
                                        class="font-semibold">{{ implode(', ', $booking['aspek_belum_direview']) }}</span>
                                </p>
                            </div>
                            <button id="openReviewModal"
                                class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded"
                                data-id="{{ $booking['id'] }}">
                                Review
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="detailBookingModal"
            class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg w-1/2">
                <h2 class="text-xl font-semibold mb-4">Detail Booking</h2>

                <!-- Tabs -->
                <div class="border-b flex space-x-4 mb-4">
                    <button class="tab-button px-4 py-2 border-b-2 border-transparent focus:border-blue-500"
                        data-tab="booking">Booking</button>
                    <button class="tab-button px-4 py-2 border-b-2 border-transparent focus:border-blue-500"
                        data-tab="payment">Payment</button>
                </div>

                <!-- Tab Content -->
                <div class="tab-content" id="booking-tab">
                    <table class="w-full border-collapse border border-gray-300" id="booking-details-table">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 text-left">No</th>
                                <th class="border px-4 py-2 text-left">Pelanggan</th>
                                <th class="border px-4 py-2 text-left">Barberman</th>
                                <th class="border px-4 py-2 text-left">Hairstyle</th>
                                <th class="border px-4 py-2 text-left">Jadwal</th>
                                <th class="border px-4 py-2 text-left">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Akan diisi secara dinamis oleh JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div class="tab-content hidden" id="payment-tab">
                    <table class="w-full border-collapse border border-gray-300">
                        <tr>
                            <td class="font-semibold border px-4 py-2">Total Bayar</td>
                            <td id="detail_total_bayar" class="border px-4 py-2"></td>
                        </tr>
                        <tr>
                            <td class="font-semibold border px-4 py-2">Metode Bayar</td>
                            <td id="detail_metode_bayar" class="border px-4 py-2"></td>
                        </tr>
                        <tr>
                            <td class="font-semibold border px-4 py-2">Status Pembayaran</td>
                            <td id="detail_status_bayar" class="border px-4 py-2"></td>
                        </tr>
                    </table>
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" id="closeDetailModal"
                        class="px-4 py-2 bg-gray-500 text-white rounded">Tutup</button>
                </div>
            </div>
        </div>
    @else
        {{-- {{ dd($data) }} --}}
        <div class="flex justify-end mb-4">
            <form method="GET" action="" class="flex items-center space-x-2">
                <label for="filter" class="text-sm text-gray-600">Filter Waktu:</label>
                <select name="filter" id="filter"
                    class="border border-gray-300 rounded px-3 py-1 focus:ring focus:ring-indigo-200 text-sm">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="week" {{ request('filter') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                </select>
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm">Terapkan</button>
            </form>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white shadow p-4 rounded-lg">
                <h2 class="font-medium text-gray-600">Booking Terkonfirmasi</h2>
                <p class="text-3xl font-bold mt-2">{{ $data['booking_konfirmasi'] }}</p>
            </div>
            <div class="bg-white shadow p-4 rounded-lg">
                <h2 class="font-medium text-gray-600">Booking Pending</h2>
                <p class="text-3xl font-bold mt-2">{{ $data['booking_pending'] }}</p>
            </div>
            <div class="bg-white shadow p-4 rounded-lg">
                <h2 class="font-medium text-gray-600">Booking Selesai</h2>
                <p class="text-3xl font-bold mt-2">{{ $data['booking_selesai'] }}</p>
            </div>
            <div class="bg-white shadow p-4 rounded-lg">
                <h2 class="font-medium text-gray-600">Booking Dibatalkan</h2>
                <p class="text-3xl font-bold mt-2">{{ $data['booking_batal'] }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div class="bg-white shadow p-4 rounded-lg">
                <h3 class="font-medium text-gray-600 text-center">Jumlah Booking</h3>
                <div class="flex justify-center">
                    <canvas id="bookingChart" class="w-full h-64"></canvas>
                </div>
            </div>

            <div class="bg-white shadow p-4 rounded-lg">
                <h3 class="font-medium text-gray-600 text-center mb-4">Antrean Harian</h3>
                <div class="mb-4">
                    <label for="barberDropdown" class="block text-sm font-medium text-gray-700">Pilih Barberman:</label>
                    <select id="barberDropdown"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        <option value="" disabled selected>-- Pilih Barberman --</option>
                        @foreach ($data['daily_queue'] as $barber => $customers)
                            <option value="{{ $barber }}">{{ $barber }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="queueWrapper" class="bg-white rounded-lg shadow p-4 hidden">
                    <ol id="queueList" class="list-decimal list-inside space-y-2"></ol>
                    <div id="viewAllWrapper" class="text-center mt-4 hidden">
                        <button id="viewAllBtn" class="text-sm text-blue-600 hover:underline">
                            Lihat Semua Antrean (<span id="totalQueue"></span>)
                        </button>
                    </div>
                </div>

                <!-- Modal -->
                <div id="queueModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50">
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Daftar Antrean Lengkap</h2>
                        <ol id="fullQueueList" class="list-decimal list-inside space-y-2 max-h-80 overflow-y-auto"></ol>
                        <button id="closeModal"
                            class="mt-4 w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('barberman'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Chart.js: Booking Chart
                const ctx = document.getElementById('bookingChart').getContext('2d');
                const bookingChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json(array_keys($data['monthly_bookings'] ?? [])),
                        datasets: [{
                            label: 'Jumlah Booking',
                            data: @json(array_values($data['monthly_bookings'] ?? [])),
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });

                // Process Queue Button
                document.getElementById('processQueueBtn').addEventListener('click', function() {
                    if (confirm('Apakah Anda yakin ingin memproses antrean hari ini?')) {
                        fetch('/dashboard/process-daily-queue', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({}),
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    alert('Berhasil memproses antrean!');
                                    window.location.reload();
                                } else {
                                    alert('Gagal memproses: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Terjadi kesalahan saat memproses antrean.');
                            });
                    }
                });

                // Check for Missing Queue and show red dot
                function checkMissingQueue() {
                    fetch('/dashboard/queue/check-missing')
                        .then(response => response.json())
                        .then(data => {
                            const dot = document.getElementById('notificationDot');
                            if (data.has_missing) {
                                dot.classList.remove('hidden');
                            } else {
                                dot.classList.add('hidden');
                            }
                        })
                        .catch(error => console.error('Error checking missing queue:', error));
                }
                checkMissingQueue();

                // jQuery DOM Ready
                const queueData = @json($data['daily_queue']);
                $(function() {
                    // Barber Dropdown Change
                    $('#barberDropdown').on('change', function() {
                        const barber = $(this).val();
                        const customers = queueData[barber] || [];
                        console.log(customers);

                        $('#queueList').empty();
                        if (customers.length > 0) {
                            const waitingCustomers = customers.filter(c => ['booked', 'waiting',
                                'in_service', 'late'
                            ].includes(c.status));

                            waitingCustomers.slice(0, 5).forEach((customer, index) => {
                                const currentStatus = customer.status || 'waiting';
                                const isDisabled = ['done', 'cancelled'].includes(
                                    currentStatus) ? 'disabled' : '';

                                const item = `
                                <li class="flex items-center justify-between bg-gray-100 px-4 py-2 rounded">
                                    <span>${index + 1}. ${customer.name}</span>
                                    <div class="flex space-x-2 items-center">
                                        <select class="status-dropdown bg-gray-200 text-sm px-2 py-1 rounded"
                                                ${isDisabled}
                                                data-id="${customer.id}" 
                                                data-barber="${barber}" 
                                                data-previous-status="${currentStatus}">
                                            <option value="booked" disabled ${currentStatus === 'booked' ? 'selected' : ''}>Booked</option>
                                            <option value="waiting" ${currentStatus === 'waiting' ? 'selected' : ''}>Menunggu</option>
                                            <option value="in_service" ${currentStatus === 'in_service' ? 'selected' : ''}>Proses</option>
                                            <option value="done" ${currentStatus === 'done' ? 'selected' : ''}>Selesai</option>
                                            <option value="late" ${currentStatus === 'late' ? 'selected' : ''}>Terlambat</option>
                                            <option value="cancelled" ${currentStatus === 'cancelled' ? 'selected' : ''}>Batal</option>
                                        </select>
                                    </div>
                                </li>`;
                                $('#queueList').append(item);
                            });

                            $('#queueWrapper').removeClass('hidden');
                            $('#viewAllWrapper').toggleClass('hidden', customers.length === 0);
                            $('#totalQueue').text(customers.length);

                            // Full Queue Modal
                            $('#fullQueueList').empty();
                            customers.forEach((customer, index) => {
                                $('#fullQueueList').append(`
                                <li class="bg-gray-100 px-4 py-2 rounded">${customer.name} - ${customer.status}</li>
                            `);
                            });
                        } else {
                            $('#queueList').html(
                                '<p class="text-center text-gray-500">Belum ada antrean</p>');
                            $('#queueWrapper').removeClass('hidden');
                            $('#viewAllWrapper').addClass('hidden');
                        }
                    });

                    // Open Modal
                    $('#viewAllBtn').on('click', function() {
                        $('#queueModal').removeClass('hidden').addClass('flex');
                    });

                    // Close Modal
                    $('#closeModal').on('click', function() {
                        $('#queueModal').addClass('hidden').removeClass('flex');
                    });

                    // Status Update
                    $(document).on('change', '.status-dropdown', function() {
                        const select = $(this);
                        const newStatus = select.val();
                        const previousStatus = select.data('previous-status');
                        const customerId = select.data('id');
                        const barberId = select.data('barber');

                        if (newStatus === previousStatus) return;

                        Swal.fire({
                            title: 'Yakin ubah status?',
                            text: `Dari "${statusLabel(previousStatus)}" ke "${statusLabel(newStatus)}"?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Ubah',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: '/dashboard/queue/update-status',
                                    method: 'POST',
                                    data: {
                                        id: customerId,
                                        status: newStatus,
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                            .attr('content')
                                    },
                                    success: function(res) {
                                        Swal.fire('Berhasil!',
                                            'Status berhasil diubah.', 'success'
                                        ).then(() => {
                                            select.data('previous-status',
                                                newStatus);
                                            select.attr(
                                                'data-previous-status',
                                                newStatus);
                                        });
                                    },
                                    error: function(err) {
                                        Swal.fire('Gagal',
                                            'Terjadi kesalahan saat mengubah status.',
                                            'error');
                                        select.val(previousStatus);
                                    }
                                });
                            } else {
                                select.val(previousStatus);
                            }
                        });
                    });

                    // Helper: Status Label
                    function statusLabel(value) {
                        switch (value) {
                            case 'waiting':
                                return 'Menunggu';
                            case 'in_service':
                                return 'Proses';
                            case 'done':
                                return 'Selesai';
                            case 'late':
                                return 'Terlambat';
                            case 'cancelled':
                                return 'Batal';
                            default:
                                return value;
                        }
                    }
                });
            });
        </script>
    @elseif(Auth::user()->hasRole('user'))
        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                initTabs();
                initBookingDetail();
                initPayment();
                initCancellation();
                initCompleted();
                initReviewModal();
                initReviewAspectLogic();
            });

            // Navigasi tab
            function initTabs() {
                $('.tab-button').click(function() {
                    let tab = $(this).data('tab');

                    $('.tab-content').addClass('hidden');
                    $('#' + tab + '-tab').removeClass('hidden');

                    $('.tab-button').each(function() {
                        if ($(this).data('tab') === tab) {
                            $(this).removeClass('border-transparent').addClass('border-blue-500');
                        } else {
                            $(this).removeClass('border-blue-500').addClass('border-transparent');
                        }
                    });
                });
            }

            function formatRupiah(angka) {
                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Detail Booking
            function initBookingDetail() {
                $(document).on('click', '.btn-detail', function() {
                    const bookingId = $(this).data('id');

                    $.ajax({
                        url: `/dashboard/booking/show/${bookingId}`,
                        method: 'GET',
                        success: function(response) {
                            let bookingRows = '';
                            response.data.details.forEach((detail, index) => {
                                bookingRows += `
                        <tr>
                            <td class="border px-4 py-2">${index + 1}</td>
                            <td class="border px-4 py-2">${detail.customer_name ?? '-'}</td>
                            <td class="border px-4 py-2">${detail.barberman?.name ?? '-'}</td>
                            <td class="border px-4 py-2">${detail.hairstyle?.name ?? '-'}</td>
                            <td class="border px-4 py-2">${
                                detail.time
                                    ? new Date(`${response.data.date}T${detail.time}`).toLocaleString('id-ID', {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        hour12: false
                                    })
                                    : '-'
                            }</td>
                            <td class="border px-4 py-2">${detail.deskripsi ?? '-'}</td>
                        </tr>
                    `;
                            });

                            $('#booking-details-table tbody').html(bookingRows);

                            // Total bayar
                            $('#detail_total_bayar').text(response.data.payment?.gross_amount ?
                                `Rp. ${formatRupiah(response.data.payment.gross_amount)}` : '-');

                            // Metode pembayaran
                            let metodeBayar = '-';
                            const payment = response.data.payment;

                            if (payment && payment.payment_type) {
                                switch (payment.payment_type) {
                                    case 'bank_transfer':
                                        metodeBayar = 'Transfer Bank';
                                        break;
                                    case 'credit_card':
                                        metodeBayar = 'Kartu Kredit';
                                        break;
                                    case 'e-wallet':
                                        metodeBayar = 'E-Wallet';
                                        break;
                                    case 'qris':
                                        metodeBayar = 'QRIS';
                                        break;
                                    default:
                                        metodeBayar = 'Metode Lain';
                                }

                                if (payment.acquirer) metodeBayar += ` (${payment.acquirer})`;
                                if (['pending', 'unpaid'].includes(payment.status)) metodeBayar += ' -';
                            }

                            $('#detail_metode_bayar').text(metodeBayar);
                            $('#detail_status_bayar').text(payment?.status ?? '-');

                            // Navigasi tab
                            $('.tab-content').addClass('hidden');
                            $('#booking-tab').removeClass('hidden');
                            $('.tab-button').each(function() {
                                if ($(this).data('tab') === 'booking') {
                                    $(this).removeClass('border-transparent').addClass(
                                        'border-blue-500');
                                } else {
                                    $(this).removeClass('border-blue-500').addClass(
                                        'border-transparent');
                                }
                            });

                            $('#detailBookingModal').removeClass('hidden');
                        },
                        error: function() {
                            Swal.fire("Error!", "Gagal mengambil data booking.", "error");
                        }
                    });
                });

                $('#closeDetailModal').click(function() {
                    $('#detailBookingModal').addClass('hidden');
                });
            }

            // Pembayaran
            function initPayment() {
                $('.btn-pay').click(function() {
                    const bookingId = $(this).data('id');

                    $.ajax({
                        url: `/booking/${bookingId}/snap-token`,
                        type: 'GET',
                        success: function(response) {
                            snap.pay(response.snap_token, {
                                onSuccess: function() {
                                    alert("Pembayaran berhasil!");
                                    location.reload();
                                },
                                onPending: function() {
                                    alert("Pembayaran masih pending.");
                                    location.reload();
                                },
                                onError: function() {
                                    alert("Terjadi kesalahan pembayaran.");
                                },
                                onClose: function() {
                                    console.log(
                                        "User menutup popup tanpa menyelesaikan pembayaran."
                                    );
                                }
                            });
                        },
                        error: function() {
                            alert("Gagal mengambil token pembayaran.");
                        }
                    });
                });
            }

            // Pembatalan booking
            function initCancellation() {
                $('.btn-batal').click(function() {
                    const bookingId = $(this).data('id');

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda yakin ingin membatalkan booking ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, batalkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            cancelBooking(bookingId);
                        }
                    });
                });

                function cancelBooking(id) {
                    $.ajax({
                        url: `/dashboard/booking/updateStatus/${id}`,
                        type: 'POST',
                        data: {
                            booking_id: id,
                            status: 'cancelled',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Booking berhasil dibatalkan.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Gagal membatalkan booking.', 'error');
                        }
                    });
                }
            }

            function initCompleted() {
                $('.btn-completed').click(function() {
                    const id = $(this).data('id');
                    const status = 'completed';

                    Swal.fire({
                        title: 'Apakah kamu yakin?',
                        text: "Booking akan dinyatakan selesai!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#22C55E',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, selesaikan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/dashboard/booking/updateStatus/${id}`,
                                type: 'POST',
                                data: {
                                    booking_id: id,
                                    status: status,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function() {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Booking telah selesai.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function() {
                                    Swal.fire('Gagal!', 'Gagal menyelesaikan booking.', 'error');
                                }
                            });
                        }
                    });
                });
            }

            // Review Booking
            function initReviewModal() {
                let selectedFiles = [];

                $('#openReviewModal').click(function() {
                    const bookingId = $(this).data('id');
                    const userId = $(this).data('user-id');
                    const kode = $(this).data('kode');

                    console.log('Clicked review button');
                    console.log('Booking ID:', bookingId);
                    console.log('User ID:', userId);
                    console.log('Kode Booking:', kode);

                    $('#review_kode').text(kode);
                    $('#reviewModal')
                        .attr('data-id', bookingId)
                        .attr('data-user-id', userId)
                        .removeClass('hidden');
                });


                $('#closeReviewModal').click(function() {
                    $('#reviewModal').addClass('hidden');
                    clearReviewForm();
                });

                $('#submitReview').click(function(e) {
                    e.preventDefault();

                    const bookingId = $('#reviewModal').attr('data-id');
                    const userId = $('#reviewModal').attr('data-user-id');
                    const reviewAspect = $('#review_aspect').val();
                    const reviewRating = $('#review_rating').val();
                    const reviewText = $('#review_text').val();

                    if (!reviewAspect || !reviewRating || !reviewText) {
                        Swal.fire("Peringatan!", "Harap isi semua kolom yang diperlukan!", "warning");
                        return;
                    }

                    const formData = new FormData();
                    formData.append('booking_id', bookingId);
                    formData.append('user_id', userId);
                    formData.append('aspect', reviewAspect);
                    formData.append('rating', reviewRating);
                    formData.append('review', reviewText);

                    selectedFiles.forEach((file) => {
                        formData.append('images[]', file);
                    });

                    Swal.fire({
                        title: 'Mengirim Review...',
                        text: 'Mohon tunggu sebentar...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '/dashboard/review/store',
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function() {
                            Swal.fire("Sukses!", "Review berhasil dikirim!", "success");
                            $('#reviewModal').addClass('hidden');
                            clearReviewForm();
                        },
                        error: function(xhr) {
                            let errorMessage = "Terjadi kesalahan saat mengirim review!";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message + "\n" + xhr.responseJSON.data;
                            }
                            Swal.fire("Error!", errorMessage, "error");
                        }
                    });
                });

                $('#review_images').on('change', function() {
                    const files = this.files;
                    if (selectedFiles.length + files.length > 3) {
                        Swal.fire("Peringatan!", "Maksimal 3 gambar!", "warning");
                        return;
                    }

                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const imgId = selectedFiles.length;
                            selectedFiles.push(file);

                            const imgPreview = `
                    <div class="relative w-20 h-20">
                        <img src="${e.target.result}" class="w-full h-full object-cover rounded">
                        <button type="button" class="absolute top-0 right-0 bg-red-500 text-white w-5 h-5 rounded-full text-xs flex items-center justify-center remove-img" data-id="${imgId}">X</button>
                    </div>
                `;

                            $('#imagePreview').append(imgPreview);
                        };

                        reader.readAsDataURL(file);
                    }
                });

                $(document).on('click', '.remove-img', function() {
                    const imgId = $(this).data('id');
                    selectedFiles.splice(imgId, 1);
                    $(this).parent().remove();
                });

                function clearReviewForm() {
                    $('#review_aspect').val('');
                    $('#review_rating').val('');
                    $('#review_text').val('');
                    $('#review_images').val('');
                    $('#imagePreview').empty();
                    selectedFiles = [];
                }
            }

            function initReviewAspectLogic() {
                $('#review_aspect').on('change', function() {
                    const selectedAspect = $(this).val();

                    if (selectedAspect === 'hairstyle') {
                        $('#review_image_section').removeClass('hidden');
                    } else {
                        $('#review_image_section').addClass('hidden');
                    }
                });

                // Trigger saat pertama kali modal dibuka
                $('#openReviewModal').click(function() {
                    $('#review_aspect').trigger('change');
                });
            }
        </script>
    @endif
@endpush
