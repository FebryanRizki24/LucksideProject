    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin Dashboard')</title>

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}">
        </script>

        @livewireStyles
    </head>

    <body class="bg-gray-100" x-data="{ sidebarOpen: false }">

        <div class="flex min-h-screen">

            <!-- Sidebar pakai component -->
            <x-sidebar />

            <!-- Main Content -->
            <div class="flex-1 flex flex-col">
                <x-navbar />

                <!-- Main Section -->
                <main class="p-6 space-y-6">
                    <div class="flex flex-col gap-2">
                        <h1 class="text-2xl font-bold">@yield('page-title', 'Dashboard')</h1>
                        @yield('filter')
                        <div class="ml-auto">
                            @yield('toolbar')
                        </div>
                    </div>
                    @yield('content')
                </main>

            </div>

        </div>

        <div id="notifModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                <h2 id="notifModalTitle" class="text-xl font-bold mb-4">Judul Notifikasi</h2>
                <div class="space-y-2">
                    <p><strong>Barbershop:</strong> <span id="notifBarbershop"></span></p>
                    <p><strong>Detail Booking:</strong> <span id="notifBookingDetail"></span></p>
                    <p><strong>Status Pembayaran:</strong> <span id="notifPaymentStatus"></span></p>
                    <div id="notifPaymentDetail" class="hidden">
                        <p><strong>Metode:</strong> <span id="notifPaymentMethod"></span></p>
                        <p><strong>Tanggal Bayar:</strong> <span id="notifPaymentDate"></span></p>
                    </div>
                    <div id="notifPayBtnWrapper" class="mt-2 hidden">
                        <button id="notifPayBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Bayar Sekarang
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mt-4"><strong>Catatan:</strong> <span id="notifNotes"></span></p>
                </div>
                <button onclick="document.getElementById('notifModal').classList.add('hidden')"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">
                    &times;
                </button>
            </div>
        </div>

        {{-- @stack('modal') --}}
        @stack('scripts')
        @livewireScripts
        <script src="https://js.pusher.com/8.3.0/pusher.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.3/echo.iife.js"></script>
        <script>
            $(document).ready(function() {
                const bell = $('#notification-bell');
                const panel = $('#notification-panel');
                const indicator = $('#notif-indicator');
                const notifList = $('#notification-panel .max-h-48');

                // Toggle panel saat ikon diklik
                bell.on('click', function(e) {
                    e.stopPropagation();
                    panel.toggleClass('hidden');
                });

                // Tutup panel jika klik di luar
                $(document).on('click', function() {
                    panel.addClass('hidden');
                });

                panel.on('click', function(e) {
                    e.stopPropagation();
                });

                loadNotifications();

                function loadNotifications() {
                    $.get('/dashboard/notification/', function(data) {
                        notifList.empty();
                        let unreadCount = 0;

                        data.forEach(n => {
                            const item = document.createElement('div');
                            item.className =
                                "flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 group relative cursor-pointer";

                            if (!n.is_read) unreadCount++;

                            const messageSpan = document.createElement('span');
                            messageSpan.className = "flex-1";
                            messageSpan.innerText = n.message || 'Booking baru diterima';

                            // Tombol hapus
                            const deleteBtn = document.createElement('button');
                            deleteBtn.innerHTML =
                                `<span class="material-icons text-red-500">delete</span>`;
                            deleteBtn.className =
                                "ml-2 opacity-0 group-hover:opacity-100 transition-opacity";
                            deleteBtn.addEventListener('click', function(e) {
                                e.stopPropagation();
                                Swal.fire({
                                    title: 'Yakin ingin hapus notifikasi ini?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Ya, hapus',
                                    cancelButtonText: 'Batal'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            url: `/dashboard/notification/destroy/${n.id}`, // Pastikan URL-nya benar
                                            method: 'DELETE',
                                            data: {
                                                _token: $(
                                                        'meta[name="csrf-token"]'
                                                        ).attr(
                                                    'content') // Kirim CSRF Token
                                            },
                                            success: function(response) {
                                                if (response.success) {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Berhasil dihapus',
                                                        text: 'Notifikasi telah dihapus.',
                                                        timer: 1500,
                                                        showConfirmButton: false
                                                    }).then(() => {
                                                        $(item)
                                                            .slideUp(
                                                                300,
                                                                function() {
                                                                    $(this)
                                                                        .remove();
                                                                    loadNotifications
                                                                        (); // Memperbarui tampilan setelah penghapusan
                                                                });
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Gagal',
                                                        text: 'Notifikasi gagal dihapus.',
                                                    });
                                                }
                                            },
                                            error: function(xhr, status,
                                            error) {
                                                console.error(
                                                    'Terjadi kesalahan:',
                                                    error);
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Gagal',
                                                    text: 'Terjadi kesalahan saat menghapus notifikasi.',
                                                });
                                            }
                                        });
                                    }
                                });
                            });

                            // Update status "dibaca" segera setelah item diklik tanpa perlu reload
                            item.addEventListener('click', function() {
                                markAsRead(n.id); // Tandai sebagai sudah dibaca

                                // Update status UI
                                item.classList.add(
                                    'read'
                                ); // Misalnya, bisa beri class "read" untuk notifikasi yang dibaca

                                $('#notifModalTitle').text(n.title || 'Notifikasi Booking');
                                $('#notifBarbershop').text(n.barbershop_name || '-');
                                $('#notifBookingDetail').text(n.booking_detail || '-');
                                $('#notifPaymentStatus').text(n.payment_status || '-');
                                $('#notifNotes').text(n.notes || '-');

                                const isAdminOrBarberman = {!! json_encode(auth()->user()?->hasRole('admin') || auth()->user()?->hasRole('barberman')) !!};

                                if (n.payment_status === 'Sudah Dibayar') {
                                    $('#notifPaymentDetail').removeClass('hidden');
                                    $('#notifPaymentMethod').text(n.payment_method || '-');
                                    $('#notifPaymentDate').text(n.payment_date || '-');
                                    $('#notifPayBtnWrapper').addClass('hidden');
                                } else {
                                    $('#notifPaymentDetail').addClass('hidden');
                                    if (!isAdminOrBarberman) {
                                        $('#notifPayBtnWrapper').removeClass('hidden');
                                        $('#notifPayBtn').off('click').on('click', function() {
                                            window.snap.pay(n.snap_token);
                                        });
                                    } else {
                                        $('#notifPayBtnWrapper').addClass('hidden');
                                    }
                                }

                                if (isAdminOrBarberman) {
                                    $('#notifNotes').addClass('hidden');
                                } else {
                                    $('#notifNotes').removeClass('hidden');
                                }

                                $('#notifModal').removeClass('hidden');
                            });

                            item.appendChild(messageSpan);
                            item.appendChild(deleteBtn);
                            notifList.prepend(item);
                        });

                        if (unreadCount > 0) {
                            indicator.removeClass('hidden');
                        } else {
                            indicator.addClass('hidden');
                        }
                    });
                }

                function markAsRead(id) {
                    $.post(`/dashboard/notification/${id}/read`, {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }).done(function() {
                        loadNotifications(); // Memperbarui tampilan setelah menandai sebagai dibaca
                    });
                }

                const currentUserId = {!! json_encode(auth()->user()?->id) !!};
                const roles = {!! json_encode(auth()->user()?->getRoleNames()) !!};

                if (currentUserId) {
                    if (roles.includes('admin')) {
                        Echo.private('admin-channel')
                            .listen('.booking.created', (e) => {
                                console.log("Admin received booking:", e);
                                loadNotifications();
                            });
                    }

                    if (roles.includes('barberman') || roles.includes('user')) {
                        Echo.private(`user.${currentUserId}`)
                            .listen('.booking.created', (e) => {
                                console.log("User/Barberman received booking:", e);
                                loadNotifications();
                            });
                    }
                }
            });
        </script>
    </body>

    </html>
