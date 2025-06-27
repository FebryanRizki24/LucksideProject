@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('page-title', 'Booking')

@section('filter')
    @if (Auth::user()->hasRole('admin'))
        <div id="filterWrapper" class="flex gap-4" data-selected="1">
            <div class="filter-option w-1/2 p-4 bg-white rounded-lg shadow cursor-pointer transition border-red-500 border-2"
                data-value="1">
                Online
            </div>
            <div class="filter-option w-1/2 p-4 bg-white rounded-lg shadow cursor-pointer transition border border-transparent"
                data-value="2">
                Offline
            </div>
        </div>
    @endif
@endsection

@section('toolbar')
    <div class="flex space-x-2">
        @can('booking-store')
            <button id="addNew" class="px-4 py-2 bg-blue-500 text-white rounded">Tambah</button>
        @endcan
        @can('booking-update')
            <button id="editSelected" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</button>
        @endcan
        @can('booking-destroy')
            <button id="deleteSelected" class="px-4 py-2 bg-red-500 text-white rounded">Hapus</button>
        @endcan
    </div>
@endsection

@section('content')
    <div class="mb-4 flex flex-wrap gap-2 items-center">
        <!-- Filter Waktu -->
        <select id="filterTime" class="border px-2 py-1 rounded">
            <option value="">Filter Waktu</option>
            <option value="today">Hari Ini</option>
            <option value="this_week">Minggu Ini</option>
            <option value="this_month">Bulan Ini</option>
        </select>

        <!-- Filter Barberman -->
        <select id="filterBarberman" class="border px-2 py-1 rounded">
            <option value="">Pilih Barberman</option>
            @foreach ($barbermans as $barber)
                <option value="{{ $barber->id }}">{{ $barber->name }}</option>
            @endforeach
        </select>

        <!-- Filter Status Booking -->
        <select id="filterBookingStatus" class="border px-2 py-1 rounded">
            <option value="">Status Booking</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="canceled">Dibatalkan</option>
            <option value="done">Selesai</option>
        </select>

        <!-- Filter Status Pembayaran -->
        <select id="filterPaymentStatus" class="border px-2 py-1 rounded">
            <option value="">Status Pembayaran</option>
            <option value="pending">Belum Bayar</option>
            <option value="paid">Lunas</option>
            <option value="expired">Kedaluwarsa</option>
        </select>

        <!-- Tombol Filter -->
        <button id="btnFilter" class="bg-blue-500 text-white px-4 py-1 rounded">
            Filter
        </button>
    </div>

    <table id="bookingTable" class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-4 py-2">No</th>
                <th class="border border-gray-300 px-4 py-2">Kode Booking</th>
                <th class="border border-gray-300 px-4 py-2">Tanggal Booking</th>
                <th class="border border-gray-300 px-4 py-2">Total Bayar</th>
                <th class="border border-gray-300 px-4 py-2">Status Bayar</th>
                <th class="border border-gray-300 px-4 py-2">Status Booking</th>
                <th class="border border-gray-300 px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <div id="bookingModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50 hidden">
        <div class="bg-white p-4 sm:p-6 rounded-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Booking</h2>
            <form id="bookingForm">
                <input type="hidden" id="bookingId">

                <div class="mb-4">
                    <label for="customer_name" class="block text-sm font-medium">Nama</label>
                    <input type="text" id="customer_name" name="customer_name"
                        class="w-full px-3 py-2 border rounded text-sm">
                </div>

                <div class="mb-4">
                    <label for="service" class="block text-sm font-medium">Layanan</label>
                    <select id="service" name="service_id" class="w-full px-3 py-2 border rounded text-sm">
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" data-harga="{{ $service->price }}">{{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="date" class="block text-sm font-medium">Tanggal</label>
                    <input type="text" id="date" name="date" class="w-full px-3 py-2 border rounded text-sm"
                        readonly>
                </div>

                <div class="mb-4">
                    <label for="time" class="block text-sm font-medium">Jam</label>
                    <input type="text" id="time" name="time" class="w-full px-3 py-2 border rounded text-sm"
                        readonly>
                </div>

                <div class="mb-4">
                    <label for="barberman" class="block text-sm font-medium">Barberman</label>
                    <select id="barberman" name="barberman_id" class="w-full px-3 py-2 border rounded text-sm">
                        @foreach ($barbermans as $barberman)
                            <option value="{{ $barberman->id }}">{{ $barberman->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="hairstyle" class="block text-sm font-medium">Hairstyle</label>
                    <select id="hairstyle" name="hairstyle_id" class="w-full px-3 py-2 border rounded text-sm">
                        @foreach ($hairstyles as $hairstyle)
                            <option value="{{ $hairstyle->id }}">{{ $hairstyle->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="block text-sm font-medium">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="w-full px-3 py-2 border rounded text-sm"></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="button" id="addToTable"
                        class="px-4 py-2 bg-green-500 text-white rounded text-sm">Tambah</button>
                </div>

                <div id="bookingTableContainer" class="overflow-x-auto">
                    <table class="w-full mt-4 border text-sm" id="bookingTableForm">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border px-2 py-1">No</th>
                                <th class="border px-2 py-1">Nama Customer</th>
                                <th class="border px-2 py-1">Tanggal & Jam</th>
                                <th class="border px-2 py-1">Layanan</th>
                                <th class="border px-2 py-1">Harga</th>
                                <th class="border px-2 py-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data booking sementara akan ditambahkan di sini -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right font-semibold border px-2 py-1">Total</td>
                                <td id="totalHarga" class="border px-2 py-1 font-bold">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="flex justify-end mt-4 space-x-2">
                    <button type="button" id="closeModal" class="px-4 py-2 bg-gray-500 text-white rounded text-sm">
                        Batal
                    </button>
                    <button type="submit" id="saveAll" class="px-4 py-2 bg-blue-500 text-white rounded text-sm">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="detailBookingModal"
        class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden px-4">
        <div class="bg-white p-6 rounded-lg w-full max-w-4xl">
            <h2 class="text-xl font-semibold mb-4">Detail Booking</h2>

            <!-- Tabs -->
            <div class="border-b flex flex-wrap space-x-4 mb-4">
                <button class="tab-button px-4 py-2 border-b-2 border-transparent focus:border-blue-500"
                    data-tab="booking">Booking</button>
                <button class="tab-button px-4 py-2 border-b-2 border-transparent focus:border-blue-500"
                    data-tab="payment">Payment</button>
                @if (auth()->user()->roleName() === 'user' &&
                        auth()->user()->bookingDetails()->whereHas('booking', function ($q) {
                                $q->where('status', 'completed');
                            })->exists())
                    <button class="tab-button px-4 py-2 border-b-2 border-transparent focus:border-blue-500"
                        data-tab="review">Review</button>
                @endif
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="booking-tab">
                <div class="overflow-auto">
                    <table class="w-full border-collapse border border-gray-300 text-sm" id="booking-details-table">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-2 py-2 text-left">No</th>
                                <th class="border px-2 py-2 text-left">Pelanggan</th>
                                <th class="border px-2 py-2 text-left">Barberman</th>
                                <th class="border px-2 py-2 text-left">Hairstyle</th>
                                <th class="border px-2 py-2 text-left">Jadwal</th>
                                <th class="border px-2 py-2 text-left">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Akan diisi secara dinamis oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-content hidden" id="payment-tab">
                <table class="w-full border-collapse border border-gray-300 text-sm">
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

            <div class="tab-content hidden" id="review-tab">
                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <tr>
                        <td class="font-semibold border px-4 py-2">Rating</td>
                        <td id="detail_rating" class="border px-4 py-2"></td>
                    </tr>
                    <tr>
                        <td class="font-semibold border px-4 py-2">Review</td>
                        <td id="detail_review" class="border px-4 py-2"></td>
                    </tr>
                </table>
                <div class="flex justify-end mt-4">
                    <button id="openReviewModal" class="px-4 py-2 bg-blue-500 text-white rounded" data-id="">
                        Tambah Review
                    </button>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" id="closeDetailModal"
                    class="px-4 py-2 bg-gray-500 text-white rounded">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Review -->
    <div id="reviewModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg w-1/3">
            <h2 class="text-xl font-semibold mb-4">Tambah Review</h2>

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
            <label for="review_images" class="block font-semibold mb-2">Upload Gambar</label>
            <input type="file" id="review_images" multiple accept="image/*"
                class="w-full border rounded px-3 py-2 mb-4">

            <!-- Preview Gambar -->
            <div id="imagePreview" class="grid grid-cols-3 gap-2 mb-4"></div>

            <div class="flex justify-end space-x-2">
                <button id="closeReviewModal" class="px-4 py-2 bg-gray-500 text-white rounded">Batal</button>
                <button id="submitReview" class="px-4 py-2 bg-blue-500 text-white rounded">Kirim</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content')
                }
            });

            let selectedFilter = 2;
            let usedSlots = [];
            let bookingList = [];
            let count = 0;
            let totalHarga = 0;

            let table = $('#bookingTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('dashboard.booking.getData') }}',
                    data: function(d) {
                        d.status = $('#filterWrapper').attr('data-selected');
                        d.filter_time = $('#filterTime').val();
                        d.barberman = $('#filterBarberman').val();
                        d.booking_status = $('#filterBookingStatus').val();
                        d.payment_status = $('#filterPaymentStatus').val();
                        console.log(d);
                    }
                },
                deferRender: true,
                order: [
                    [2, 'desc']
                ],
                columns: [{
                        data: null,
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'kode_booking',
                        name: 'kode'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'total_bayar',
                        name: 'payment.amount',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status_bayar',
                        name: 'payment.status',
                    },
                    {
                        data: 'status_booking',
                        name: 'status',
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: '<"flex flex-wrap justify-between items-center gap-2 text-sm mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>',
            });

            $('#btnFilter').on('click', function() {
                table.ajax.reload();
            });

            $(document).on('click', '.filter-option', function() {
                let selectedValue = $(this).attr('data-value');
                $('#filterWrapper').attr('data-selected', selectedValue);

                $('.filter-option').removeClass('border-red-500 border-2').addClass(
                    'border border-transparent');
                $(this).addClass('border-red-500 border-2').removeClass('border-transparent');

                // console.log("Selected Filter:", selectedValue);
                table.ajax.reload();
            });

            $('#bookingTable tbody').css('cursor', 'pointer');

            $('#bookingTable tbody').on('click', 'tr', function() {
                $('#bookingTable tbody tr').removeClass('selected');
                $(this).addClass('selected');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#bookingTable').length) {
                    $('#bookingTable tbody tr').removeClass('selected');
                }
            });

            function checkDateStatus(dateStr) {
                const formattedDate = dateStr.split('-').reverse().join('-');

                $.post('/booking/check-date', {
                    date: formattedDate,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, function(response) {
                    if (response.message) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: response.message
                        });
                    }
                }).fail(() => {
                    console.error('Gagal memeriksa status tanggal.');
                });
            }

            $('#addNew').click(function() {
                $('#statusForm').addClass('hidden');
                usedSlots = [];
                $('#modalTitle').text('Tambah Booking');
                $('#bookingId').val('');
                $('#customer_name').val('');
                $('#barberman').val('');
                $('#hairstyle').val('');
                $('#deskripsi').val('');
                $('#time').val('');

                const formatter = new Intl.DateTimeFormat('en-CA', {
                    timeZone: 'Asia/Jakarta',
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                });

                const parts = formatter.formatToParts(new Date());
                const todayDate =
                    `${parts.find(p => p.type === 'year').value}-${parts.find(p => p.type === 'month').value}-${parts.find(p => p.type === 'day').value}`;
                const displayDate = todayDate.split('-').reverse().join('-');
                $('#date').val(todayDate);

                console.log('🟢 Modal dibuka');
                console.log('📅 Tanggal hari ini (input):', todayDate);

                checkDateStatus(todayDate);

                $('#bookingModal').removeClass('hidden');

                // Hindari duplicate event
                $('#barberman').off('change').on('change', function() {
                    const barbermanId = $(this).val();
                    console.log('💇‍♂️ Barberman dipilih:', barbermanId);

                    $.post('/booking/get-schedule', {
                        barberman_id: barbermanId,
                        date: displayDate,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }, function(response) {
                        const slots = response.available_slots || [];
                        console.log('📥 Slot diterima dari server:', slots);

                        if (slots.length > 0) {
                            const now = new Date();

                            // const closestSlot = slots.find(slot => {
                            //     const [hour, minute] = slot.split(':');
                            //     const slotTime = new Date();
                            //     slotTime.setHours(hour, minute, 0, 0);
                            //     return slotTime > now && !usedSlots.includes(slot.slice(0, 5));
                            // });
                            const closestSlot = slots.find(slot => {
                                const [hour, minute] = slot.split(':');
                                const slotTime = new Date();
                                slotTime.setHours(hour, minute, 0, 0);

                                console.log('🗂️ usedSlots saat ini:', usedSlots);
                                const isUsed = usedSlots.some(used =>
                                    used.time === slot.slice(0, 5) &&
                                    used.barberman_id === barbermanId
                                );

                                return slotTime > now && !isUsed;
                            });

                            if (closestSlot) {
                                const formattedTime = closestSlot.slice(0, 5);
                                $('#time').val(formattedTime);
                                console.log('⏰ Slot terdekat yang tersedia:', closestSlot);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Slot Tersedia',
                                    text: `Slot terdekat: ${formattedTime}`
                                });
                            } else {
                                $('#time').val('');
                                console.log(
                                    '⚠️ Tidak ada slot tersedia setelah jam sekarang atau semua sudah dipakai.'
                                );

                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Tidak Ada Slot Tersedia',
                                    text: 'Tidak ada slot yang bisa dipilih setelah waktu sekarang atau semua sudah digunakan.'
                                });
                            }
                        } else {
                            $('#time').val('');
                            console.log('🚫 Slot kosong semua');

                            Swal.fire({
                                icon: 'info',
                                title: 'Jadwal Kosong',
                                text: 'Barberman tidak memiliki slot jadwal di tanggal ini.'
                            });
                        }
                    }).fail(() => {
                        console.error('❌ Gagal mengambil slot jadwal dari server');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengambil slot jadwal.'
                        });
                    });
                });
            });

            $('#editSelected').click(function() {
                let selectedRow = table.row('.selected').data();
                console.log("Selected Row Data:", selectedRow);

                if (!selectedRow) {
                    Swal.fire("Peringatan!", "Pilih satu data untuk diedit!", "warning");
                    return;
                }

                $('#modalTitle').text('Edit Booking');
                $('#bookingId').val(selectedRow.id);
                $('#customer_name').val(selectedRow.customer_name);
                $('#deskripsi').val(selectedRow.deskripsi);
                $('#date').val(selectedRow.date);
                let timeFormatted = selectedRow.time?.substring(0, 5); // "14:30"
                $('#time').val(timeFormatted);

                let detail = selectedRow.details[0];

                $('#barberman').val(String(detail.barberman_id)).trigger('change');
                $('#hairstyle').val(String(detail.hairstyle_id)).trigger('change');
                $('#service').val(String(detail.service_id)).trigger('change');

                // $('#status').val(selectedRow.status);
                // if (selectedRow.status === 'completed') {
                //     $('#statusForm').addClass('hidden');
                // } else {
                //     $('#statusForm').removeClass('hidden');
                // }
                // $('#addToTable').addClass('hidden');
                $('#bookingTableContainer').addClass('hidden');

                // Kosongkan isi tabel
                $('#bookingTableForm tbody').empty();

                bookingList = []; // Reset dulu

                if (selectedRow.details && selectedRow.details.length > 0) {
                    let totalHarga = 0;

                    selectedRow.details.forEach((detail, index) => {
                        let harga = parseInt(detail.service?.price || 0);
                        totalHarga += harga;

                        // Masukkan ke bookingList (biar bisa diedit)
                        bookingList.push({
                            id: detail.id,
                            customer_name: detail.customer_name,
                            date: selectedRow.date,
                            time: detail.time.substring(0, 5),
                            barberman_id: detail.barberman?.id,
                            hairstyle_id: detail.hairstyle?.id,
                            service_id: detail.service?.id,
                            deskripsi: detail.deskripsi,
                            harga: harga
                        });

                        console.log("Detail ID:", detail.id);

                        // HTML baris seperti biasa
                        let rowHtml = `
                            <tr data-index="${index}">
                                <td class="border px-2 text-center">${index + 1}</td>
                                <td class="border px-2">${detail.customer_name}</td>
                                <td class="border px-2">${selectedRow.date} ${detail.time.substring(0, 5)}</td>
                                <td class="border px-2">${detail.service?.name} - ${detail.barberman?.name} - ${detail.hairstyle?.name}</td>
                                <td class="border px-2">Rp ${harga.toLocaleString('id-ID')}</td>
                                <td class="border px-2">
                                    <button type="button" class="text-blue-500 editBooking mr-2" data-index="${index}">Edit</button>
                                    <button type="button" class="text-red-500 deleteBooking" data-index="${index}">Hapus</button>
                                </td>
                            </tr>
                        `;

                        $('#bookingTableForm tbody').append(rowHtml);
                    });

                    $('#totalHarga').text(`Rp ${totalHarga.toLocaleString('id-ID')}`);
                    $('#bookingTableContainer').removeClass('hidden');
                }

                $('#addToTable').text('Ubah').data('edit-index', 0);

                $('#bookingModal').removeClass('hidden');
            });

            $(document).on('click', '.editBooking', function() {
                const index = $(this).data('index');
                const data = bookingList[index];

                if (!data) {
                    console.warn("Data tidak ditemukan untuk index", index);
                    return;
                }

                // Masukkan data ke form
                $('#customer_name').val(data.customer_name);
                $('#date').val(data.date);
                $('#time').val(data.time);
                $('#barberman').val(data.barberman_id);
                $('#hairstyle').val(data.hairstyle_id);
                $('#service').val(data.service_id);
                $('#deskripsi').val(data.deskripsi);

                // Tandai baris sedang diedit
                $('#addToTable').data('edit-index', index);

                $('#addToTable').text('Ubah');

                // Scroll ke atas atau fokus form jika mau
                $('html, body').animate({
                    scrollTop: 0
                }, 300);
            });

            $('#deleteSelected').click(function() {
                let selectedRow = table.row('.selected').data();
                if (!selectedRow) {
                    Swal.fire("Peringatan!", "Pilih satu data untuk dihapus!", "warning");
                    return;
                }

                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Data akan dihapus secara permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/dashboard/booking/destroy/${selectedRow.id}`,
                            method: 'DELETE',
                            success: function(response) {
                                Swal.fire("Terhapus!", response.message, "success");
                                table.ajax.reload();
                            },
                            error: function() {
                                Swal.fire("Error!", "Terjadi kesalahan, coba lagi.",
                                    "error");
                            }
                        });
                    }
                });
            });

            $('#closeModal').click(function() {
                $('#bookingModal').addClass('hidden');
            });

            $(document).ready(function() {
                $('.tab-button').click(function() {
                    let tab = $(this).data('tab');

                    $('.tab-content').addClass('hidden');
                    $('#' + tab + '-tab').removeClass('hidden');

                    $('.tab-button').each(function() {
                        if ($(this).data('tab') === tab) {
                            $(this)
                                .removeClass('border-transparent')
                                .addClass('border-blue-500');
                        } else {
                            $(this)
                                .removeClass('border-blue-500')
                                .addClass('border-transparent');
                        }
                    });
                });

                function formatRupiah(angka) {
                    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }

                $(document).on('click', '.btn-detail', function() {
                    let bookingId = $(this).data('id');

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

                            $('#detail_total_bayar').text(response.data.payment
                                ?.gross_amount ?
                                `Rp. ${formatRupiah(response.data.payment.gross_amount)}` :
                                '-');

                            $('#detail_metode_bayar').text(
                                response.data.payment && response.data.payment
                                .payment_type ?
                                (response.data.payment.payment_type ===
                                    'bank_transfer' ? 'Transfer Bank' :
                                    response.data.payment.payment_type ===
                                    'credit_card' ? 'Kartu Kredit' :
                                    response.data.payment.payment_type ===
                                    'e-wallet' ? 'E-Wallet' :
                                    response.data.payment.payment_type === 'qris' ?
                                    'QRIS' : 'Metode Lain') +
                                (response.data.payment.acquirer ? ' (' + response
                                    .data.payment.acquirer + ')' : '') +
                                (response.data.payment.status === 'pending' ||
                                    response.data.payment.status === 'unpaid' ?
                                    ' -' : '') :
                                '-'
                            );
                            $('#detail_status_bayar').text(response.data.payment
                                .status);
                            // Ambil review dengan aspect "booking"
                            let bookingReview = response.data.reviews.find(review =>
                                review.aspect === "booking");

                            $('#detail_rating').text(bookingReview ? bookingReview
                                .rating : 'Belum ada rating');
                            $('#detail_review').text(bookingReview ? bookingReview
                                .comment : 'Belum ada review');


                            $('#openReviewModal').attr('data-id', response.data.id);
                            $('#openReviewModal').attr('data-user-id', response.data
                                .user_id);

                            if (response.data.status === 'completed') {
                                $('#openReviewModal').removeClass('hidden');
                            } else {
                                $('#openReviewModal').addClass('hidden');
                            }

                            $('.tab-content').addClass('hidden');
                            $('#booking-tab').removeClass('hidden');
                            $('.tab-button').each(function() {
                                if ($(this).data('tab') === 'booking') {
                                    $(this)
                                        .removeClass('border-transparent')
                                        .addClass('border-blue-500');
                                } else {
                                    $(this)
                                        .removeClass('border-blue-500')
                                        .addClass('border-transparent');
                                }
                            });

                            $('#detailBookingModal').removeClass('hidden');
                        },
                        error: function() {
                            Swal.fire("Error!", "Gagal mengambil data booking.",
                                "error");
                        }
                    });
                });

                $('#closeDetailModal').click(function() {
                    $('#detailBookingModal').addClass('hidden');
                });
            });

            $(document).on('change', '.status-dropdown', function() {
                let bookingId = $(this).data('id');
                let newStatus = $(this).val();
                let dropdown = $(this);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mengubah status menjadi "' + newStatus + '"?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Ubah',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/dashboard/booking/updateStatus/${bookingId}`,
                            method: 'POST',
                            data: {
                                booking_id: bookingId,
                                status: newStatus,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                dropdown.replaceWith(
                                    '<span class="px-2 py-1 text-white rounded bg-green-500">Completed</span>'
                                );
                            },
                            error: function() {
                                Swal.fire('Error!',
                                    'Terjadi kesalahan saat mengubah status!',
                                    'error');
                            }
                        });
                    }
                });
            });

            $(document).ready(function() {
                let selectedFiles = [];

                // Open the review modal
                $('#openReviewModal').click(function() {
                    let bookingId = $(this).data('id');
                    let userId = $(this).attr('data-user-id');

                    $('#reviewModal').removeClass('hidden');
                    $('#reviewModal').attr('data-id', bookingId);
                    $('#reviewModal').attr('data-user-id', userId);
                });

                // Close the review modal
                $('#closeReviewModal').click(function() {
                    $('#reviewModal').addClass('hidden');
                    clearReviewForm();
                });

                $('#addToTable').click(function(e) {
                    e.preventDefault();

                    let customer_name = $('#customer_name').val();
                    let date = $('#date').val();
                    let time = $('#time').val();
                    let barberman_id = $('#barberman').val();
                    let hairstyle_id = $('#hairstyle').val();
                    let service_id = $('#service').val();
                    let deskripsi = $('#deskripsi').val();
                    let harga = parseInt($('#service option:selected').data('harga')) || 0;

                    if (!customer_name || !date || !time || !barberman_id || !hairstyle_id || !
                        service_id) {
                        Swal.fire("Peringatan!", "Harap isi semua kolom yang diperlukan!",
                            "warning");
                        return;
                    }

                    const barbermanText = $('#barberman option:selected').text();
                    const hairstyleText = $('#hairstyle option:selected').text();
                    const serviceText = $('#service option:selected').text();

                    let editIndex = $('#addToTable').data('edit-index');

                    if (editIndex !== undefined) {
                        const existingId = bookingList[editIndex]?.id || null;
                        // ✅ Mode Edit
                        bookingList[editIndex] = {
                            id: existingId,
                            customer_name,
                            date,
                            time,
                            barberman_id,
                            hairstyle_id,
                            service_id,
                            deskripsi,
                            harga,
                        };

                        // Perbarui baris di tabel
                        $('#bookingTableForm tbody tr[data-index="' + editIndex + '"]').html(`
                            <td class="border px-2 text-center">${parseInt(editIndex) + 1}</td>
                            <td class="border px-2">${customer_name}</td>
                            <td class="border px-2">${date} - ${time}</td>
                            <td class="border px-2">${serviceText} - ${barbermanText} - ${hairstyleText}</td>
                            <td class="border px-2">Rp. ${harga.toLocaleString()}</td>
                            <td class="border px-2">
                                <button type="button" class="text-blue-500 editBooking mr-2" data-index="${editIndex}">Edit</button>
                                <button type="button" class="text-red-500 deleteBooking" data-index="${editIndex}">Hapus</button>
                            </td>
                        `);

                        console.log('📝 Booking diubah pada index', editIndex, bookingList);

                        // Reset mode tambah
                        $('#addToTable').removeData('edit-index').text('Tambah');

                    } else {
                        // ➕ Mode Tambah
                        const rowIndex = bookingList.length;

                        bookingList.push({
                            customer_name,
                            date,
                            time,
                            barberman_id,
                            hairstyle_id,
                            service_id,
                            deskripsi,
                            harga
                        });

                        usedSlots.push({
                            time: time,
                            barberman_id: barberman_id
                        });

                        $('#bookingTableForm tbody').append(`
                            <tr data-index="${rowIndex}">
                                <td class="border px-2 text-center">${rowIndex + 1}</td>
                                <td class="border px-2">${customer_name}</td>
                                <td class="border px-2">${date} - ${time}</td>
                                <td class="border px-2">${serviceText} - ${barbermanText} - ${hairstyleText}</td>
                                <td class="border px-2">Rp. ${harga.toLocaleString()}</td>
                                <td class="border px-2">
                                    <button type="button" class="text-blue-500 editBooking mr-2" data-index="${rowIndex}">Edit</button>
                                    <button type="button" class="text-red-500 deleteBooking" data-index="${rowIndex}">Hapus</button>
                                </td>
                            </tr>
                        `);

                        console.log('💾 Booking ditambahkan:', bookingList);
                    }

                    // Hitung ulang total harga
                    let totalHarga = 0;
                    bookingList.forEach(item => totalHarga += item.harga);
                    $('#totalHarga').text(`Rp ${totalHarga.toLocaleString('id-ID')}`);

                    // Reset form
                    let savedDate = $('#date').val(); // supaya tidak hilang
                    $('#bookingForm')[0].reset();
                    $('#date').val(savedDate);
                    $('#time').val('');
                });

                $(document).on('click', '.deleteBooking', function() {
                    const index = $(this).data('index');
                    const row = $(this).closest('tr');

                    // Kurangi total harga
                    const removedBooking = bookingList[index];
                    if (removedBooking) {
                        totalHarga -= removedBooking.harga;
                        $('#totalHarga').text(`Rp ${totalHarga.toLocaleString()}`);

                        // Hapus dari usedSlots juga
                        usedSlots = usedSlots.filter(t => t !== removedBooking.time);

                        // Hapus dari bookingList
                        bookingList.splice(index, 1);

                        console.log('🗑️ Booking dihapus:', removedBooking);
                    }

                    // Hapus baris tabel
                    row.remove();

                    // Reset nomor urutan
                    $('#bookingTable tbody tr').each(function(i) {
                        $(this).find('td:first').text(i + 1);
                    });

                    // Kurangi count
                    count--;
                });

                // Tombol "Simpan" akhir
                $('#bookingForm').submit(function(e) {
                    e.preventDefault();

                    let bookingId = $('#bookingId').val();

                    if (bookingId) {
                        // ======== UPDATE (multiple booking) ========
                        let bookingData = {
                            date: $('#date').val(),
                            status: $('#status').val()
                        };

                        if (!bookingData.date || bookingList.length === 0) {
                            Swal.fire("Peringatan!", "Pastikan tanggal dan detail booking diisi!",
                                "warning");
                            return;
                        }

                        let requestPayload = {
                            booking: bookingData,
                            details: bookingList.map((item, index) => {
                                return {
                                    id: item.id,
                                    time: item.time,
                                    hairstyle_id: item.hairstyle_id,
                                    deskripsi: item.deskripsi,
                                    status: item.status
                                };
                            }),
                            _token: $('meta[name="csrf-token"]').attr('content')
                        };

                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: `/dashboard/booking/update/${bookingId}`,
                            method: 'POST',
                            data: requestPayload,
                            success: function() {
                                Swal.fire("Sukses!", "Booking berhasil diperbarui!",
                                    "success").then(() => {
                                    $('#bookingModal').addClass('hidden');
                                    $('#bookingForm')[0].reset();
                                    $('#bookingId').val('');
                                    location.reload();
                                });
                            },
                            error: function() {
                                Swal.fire("Error!",
                                    "Terjadi kesalahan saat mengupdate data!",
                                    "error").then(() => {
                                    location.reload();
                                });
                            }
                        });

                    } else {
                        if (bookingList.length === 0) {
                            Swal.fire("Peringatan!", "Belum ada data yang ditambahkan!", "warning");
                            return;
                        }

                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: '/dashboard/booking/store',
                            method: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                bookings: bookingList
                            },
                            success: function() {
                                Swal.fire("Sukses!", "Semua booking berhasil disimpan!",
                                    "success").then(() => {
                                    $('#bookingModal').addClass('hidden');
                                    $('#bookingTableList').DataTable().ajax
                                        .reload();
                                    bookingList = [];
                                    count = 0;
                                    $('#bookingTable tbody').empty();
                                    location.reload();
                                });
                            },
                            error: function() {
                                Swal.fire("Error!",
                                    "Terjadi kesalahan saat menyimpan data!",
                                    "error").then(() => {
                                    location.reload();
                                });
                            }
                        });
                    }
                });

                // Function to clear the review form
                function clearReviewForm() {
                    $('#review_aspect').val('');
                    $('#review_rating').val('');
                    $('#review_text').val('');
                    $('#review_images').val('');
                    $('#imagePreview').empty();
                    selectedFiles = [];
                }

                // Preview uploaded images with remove button
                $('#review_images').on('change', function() {
                    let files = this.files;

                    if (selectedFiles.length + files.length > 3) {
                        Swal.fire("Peringatan!", "Maksimal 3 gambar!", "warning");
                        return;
                    }

                    for (let i = 0; i < files.length; i++) {
                        let file = files[i];
                        let reader = new FileReader();

                        reader.onload = function(e) {
                            let imgId = selectedFiles.length;
                            selectedFiles.push(file);

                            let imgPreview = `
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

                // Hapus gambar saat tombol "X" diklik
                $(document).on('click', '.remove-img', function() {
                    let imgId = $(this).data('id');
                    selectedFiles.splice(imgId, 1);
                    $(this).parent().remove();
                });
            });
        });
    </script>
@endpush
