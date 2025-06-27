@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('page-title', 'Hairstyle')

@section('toolbar')
    <div class="flex space-x-2">
        @can('hairstyle-store')
            <button id="addNew" class="px-4 py-2 bg-blue-500 text-white rounded">Tambah</button>
        @endcan
        @can('hairstyle-update')
            <button id="editSelected" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</button>
        @endcan
        @can('hairstyle-destroy')
            <button id="deleteSelected" class="px-4 py-2 bg-red-500 text-white rounded">Hapus</button>
        @endcan
    </div>
@endsection

@section('content')
    <table id="hairstyleTable" class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-4 py-2">No</th>
                <th class="border border-gray-300 px-4 py-2">Nama</th>
                <th class="border border-gray-300 px-4 py-2">Kategori</th>
                <th class="border border-gray-300 px-4 py-2">Deskripsi</th>
                <th class="border border-gray-300 px-4 py-2">Gambar</th>
                <th class="border border-gray-300 px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div id="hairstyleModal"
        class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50 p-4">
        <div class="bg-white p-6 rounded-lg w-full max-w-md md:max-w-lg lg:max-w-xl">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Hairstyle</h2>
            <form id="hairstyleForm">
                <input type="hidden" id="hairstyleId">

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium">Nama</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border rounded">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Kategori</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($faceShapes as $shape)
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" name="category[]" value="{{ $shape->id }}"
                                    class="category-checkbox">
                                <span>{{ $shape->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="block text-sm font-medium">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="w-full px-3 py-2 border rounded"></textarea>
                </div>

                <div class="mb-4">
                    <label for="photo" class="block text-sm font-medium">Gambar</label>
                    <div class="flex flex-col items-center">
                        <img id="photoPreview" class="w-24 h-24 rounded mt-2 hidden border border-gray-300 shadow-sm"
                            alt="Foto Hairstyle">
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
            <h2 class="text-xl font-semibold mb-4">Review Hairstyle</h2>

            <div id="reviewList">
                <p class="text-gray-500 text-center" id="reviewLoading">Memuat review...</p>
            </div>

            {{-- Untuk daftar review panjang, aktifkan ini --}}
            {{-- <div id="reviewList" class="max-h-64 overflow-y-auto"></div> --}}

            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" id="closeReviewModal" class="px-4 py-2 bg-gray-500 text-white rounded">
                    Tutup
                </button>
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

            let table = $('#hairstyleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.hairstyle.getData') }}",
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
                        data: 'category',
                        name: 'category',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'photo',
                        name: 'photo',
                        orderable: false,
                        searchable: false,
                        render: data => data ?
                            `<img src="${data}" class="w-12 h-12 rounded-full" alt="Foto Hairstyle">` :
                            '-'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: '<"flex flex-wrap justify-between items-center gap-2 text-sm mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>',
            });

            $('#hairstyleTable tbody').css('cursor', 'pointer');

            $('#hairstyleTable tbody').on('click', 'tr', function() {
                $('#hairstyleTable tbody tr').removeClass('selected');
                $(this).addClass('selected');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#hairstyleTable').length) {
                    $('#hairstyleTable tbody tr').removeClass('selected');
                }
            });

            $('#addNew').click(function() {
                $('#modalTitle').text('Tambah Hairstyle');
                $('#hairstyleId').val('');
                $('#name').val('');
                $('#category').val('');
                $('#deskripsi').val('');
                $('#photo').val('');
                $('#hairstyleModal').removeClass('hidden');
            });

            $('#editSelected').click(function() {
                let selectedRow = table.row('.selected').data();
                // console.log("Selected Row Data:", selectedRow);

                if (!selectedRow) {
                    Swal.fire("Peringatan!", "Pilih satu data untuk diedit!", "warning");
                    return;
                }

                $('#modalTitle').text('Edit Hairstyle');
                $('#hairstyleId').val(selectedRow.id);
                $('#name').val(selectedRow.name);
                $('#deskripsi').val(selectedRow.deskripsi);

                $('.category-checkbox').prop('checked', false);

                if (selectedRow.category) {
                    let categories = selectedRow.category.split(',').map(cat => cat.trim());

                    // console.log("Parsed Categories:", categories);

                    categories.forEach(category => {
                        $('.category-checkbox').each(function() {
                            if ($(this).next('span').text().trim() === category) {
                                $(this).prop('checked', true);
                            }
                        });
                    });
                }

                if (selectedRow.photo) {
                    $('#photoPreview').attr('src', selectedRow.photo).removeClass('hidden');
                } else {
                    $('#photoPreview').addClass('hidden');
                }

                $('#hairstyleModal').removeClass('hidden');
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
                            url: `/dashboard/hairstyle/destroy/${selectedRow.id}`,
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

            $('#hairstyleModal').click(function(e) {
                if (e.target === this) {
                    $(this).addClass('hidden');
                }
            });

            $('#closeModal').click(function() {
                $('#hairstyleModal').addClass('hidden');
            });

            $('#hairstyleTable tbody').on('click', '.btn-review', function() {
                let hairstyleId = $(this).data('id');

                $('#reviewModal').removeClass('hidden');
                $('#reviewList').html('<p class="text-gray-500 text-center">Memuat review...</p>');

                $.ajax({
                    url: `/dashboard/hairstyle/reviews/${hairstyleId}`,
                    method: 'GET',
                    success: function(response) {
                        let reviewsHtml = '';
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(data => {
                                let photosHtml = '';
                                if (data.photos && data.photos.length > 0) {
                                    data.photos.forEach(photo => {
                                        photosHtml +=
                                            `<img src="${photo}" class="w-12 h-12 inline-block mr-2 rounded" alt="Review Photo">`;
                                    });
                                }

                                reviewsHtml += `
                        <div class="border p-4 rounded shadow mb-2">
                            <p><strong>Rating:</strong> ${data.rating} ⭐</p>
                            <p><strong>Review:</strong> ${data.review}</p>
                            <p class="text-gray-500 text-sm">Dari: ${data.user_name}</p>
                            ${photosHtml}
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

            $('#hairstyleForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                formData.delete('category[]');

                $('.category-checkbox:checked').each(function() {
                    formData.append('category[]', $(this).val());
                });

                let id = $('#hairstyleId').val();
                let url = id ? `/dashboard/hairstyle/update/${id}` : `/dashboard/hairstyle/store`;

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
                        $('#hairstyleModal').addClass('hidden');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        let errorMessage = "Terjadi kesalahan, coba lagi.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage += "<br>" + xhr.responseJSON.message;
                        }

                        Swal.fire("Error!", errorMessage, "error");
                    }
                });
            });
        });
    </script>
@endpush
