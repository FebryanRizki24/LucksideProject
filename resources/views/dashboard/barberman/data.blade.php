@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('page-title', 'Data Barberman')

@section('toolbar')
    <div class="flex space-x-2">
        <button id="addNew" class="px-4 py-2 bg-blue-500 text-white rounded">Tambah</button>
        <button id="editSelected" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</button>
        <button id="deleteSelected" class="px-4 py-2 bg-red-500 text-white rounded">Hapus</button>
    </div>
@endsection

@section('content')
    <table id="dataTable" class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-4 py-2">No</th>
                <th class="border border-gray-300 px-4 py-2">Nama</th>
                <th class="border border-gray-300 px-4 py-2">No Hp</th>
                <th class="border border-gray-300 px-4 py-2">Status</th>
                <th class="border border-gray-300 px-4 py-2">Foto</th>
                <th class="border border-gray-300 px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <div id="barbermanModal"
        class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50 p-4">
        <div class="bg-white p-6 rounded-lg w-full max-w-md md:max-w-lg lg:max-w-xl">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Barberman</h2>
            <form id="barbermanForm">
                <input type="hidden" id="barbermanId">

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium">Nama</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border rounded">
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium">No HP</label>
                    <input type="text" id="phone" name="phone" class="w-full px-3 py-2 border rounded">
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border rounded">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="photo" class="block text-sm font-medium">Foto</label>

                    <div class="flex flex-col items-center">
                        <img id="photoPreview" class="w-24 h-24 rounded mt-2 hidden border border-gray-300 shadow-sm"
                            alt="Foto Barberman">
                    </div>

                    <input type="file" id="photo" name="photo"
                        class="w-full px-3 py-2 border rounded mt-2 cursor-pointer">
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="closeModal" class="px-4 py-2 bg-gray-500 text-white rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="reviewModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50 p-4">
        <div class="bg-white p-6 rounded-lg w-full max-w-md md:max-w-lg lg:max-w-xl">
            <h2 class="text-xl font-semibold mb-4">Review Barberman</h2>
            <div id="reviewList">
                <p class="text-gray-500 text-center" id="reviewLoading">Memuat review...</p>
            </div>
            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" id="closeReviewModal" class="px-4 py-2 bg-gray-500 text-white rounded">Tutup</button>
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

            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/dashboard/barberman/data/getData',
                order: [
                    [1, 'asc']
                ],
                columns: [{
                        data: null,
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        defaultContent: "-"
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: data => data ? 'Aktif' : 'Tidak Aktif'
                    },
                    {
                        data: 'photo',
                        name: 'photo',
                        render: data => data ?
                            `<img src="${data}" class="w-12 h-12 rounded-full" alt="Foto">` : '-',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ],
                dom: '<"flex flex-wrap justify-between items-center gap-2 text-sm mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>',
            });

            $('#dataTable tbody').css('cursor', 'pointer');

            $('#dataTable tbody').on('click', 'tr', function() {
                $('#dataTable tbody tr').removeClass('selected');
                $(this).addClass('selected');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#dataTable').length) {
                    $('#dataTable tbody tr').removeClass('selected');
                }
            });

            $('#addNew').click(function() {
                $('#modalTitle').text('Tambah Barberman');
                $('#barbermanId').val('');
                $('#name').val('');
                $('#status').val('');
                $('#photo').val('');
                $('#barbermanModal').removeClass('hidden');
            });

            $('#editSelected').click(function() {
                let selectedRow = table.row('.selected').data();

                if (!selectedRow) {
                    Swal.fire("Peringatan!", "Pilih satu data untuk diedit!", "warning");
                    return;
                }

                $('#modalTitle').text('Edit Barberman');
                $('#barbermanId').val(selectedRow.id);
                $('#name').val(selectedRow.name);
                $('#phone').val(selectedRow.phone);
                $('#status').val(selectedRow.status);

                if (selectedRow.photo) {
                    $('#photoPreview').attr('src', selectedRow.photo).removeClass('hidden');
                } else {
                    $('#photoPreview').addClass('hidden');
                }

                $('#barbermanModal').removeClass('hidden');
            });

            $('#photo').change(function(event) {
                let input = event.target;
                if (input.files && input.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#photoPreview').attr('src', e.target.result).removeClass('hidden');
                    };
                    reader.readAsDataURL(input.files[0]);
                }
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
                            url: `/dashboard/barberman/data/destroy/${selectedRow.id}`,
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

            $('#barbermanModal').click(function(e) {
                if (e.target === this) {
                    $(this).addClass('hidden');
                }
            });

            $('#closeModal').click(function() {
                $('#barbermanModal').addClass('hidden');
            });

            $('#barbermanForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                let id = $('#barbermanId').val();
                let url = id ? `/dashboard/barberman/data/update/${id}` : `/dashboard/barberman/data/store`;

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire("Berhasil!", response.message, "success");
                        $('#barbermanModal').addClass('hidden');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        // console.error(xhr.responseText);
                        let errorMessage = "Terjadi kesalahan, coba lagi.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage += "<br>" + xhr.responseJSON.message;
                        }

                        Swal.fire("Error!", errorMessage, "error");
                    }
                });
            });

            $('#dataTable tbody').on('click', '.btn-review', function() {
                let barbermanId = $(this).data('id');

                $('#reviewModal').removeClass('hidden');
                $('#reviewList').html('<p class="text-gray-500 text-center">Memuat review...</p>');

                $.ajax({
                    url: `/dashboard/barberman/data/reviews/${barbermanId}`,
                    method: 'GET',
                    success: function(response) {
                        let reviewsHtml = '';
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(data => {
                                reviewsHtml += `
                                    <div class="border p-4 rounded shadow mb-2">
                                        <p><strong>Rating:</strong> ${data.rating} ⭐</p>
                                        <p><strong>Review:</strong> ${data.review}</p>
                                        <p class="text-gray-500 text-sm">Dari: ${data.user_name}</p>
                                    </div>
                                `;
                            });
                        } else {
                            reviewsHtml = '<p class="text-gray-500">Belum ada review.</p>';
                        }
                        $('#reviewList').html(reviewsHtml);
                    },
                    error: function() {
                        $('#reviewList').html(
                            '<p class="text-red-500">Gagal memuat review.</p>');
                    }
                });
            });

            $('#reviewModal').click(function(e) {
                if (e.target === this) {
                    $(this).addClass('hidden');
                }
            });

            $('#closeReviewModal').click(function() {
                $('#reviewModal').addClass('hidden');
            });
        });
    </script>
@endpush
