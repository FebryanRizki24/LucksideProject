@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('page-title', 'Gallery')

@section('toolbar')
    <div class="flex space-x-2">
        @can('gallery-store')
            <button id="addNew" class="px-4 py-2 bg-blue-500 text-white rounded">Tambah</button>
        @endcan
        @can('gallery-update')
            <button id="editSelected" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</button>
        @endcan
        @can('gallery-destroy')
            <button id="deleteSelected" class="px-4 py-2 bg-red-500 text-white rounded">Hapus</button>
        @endcan
    </div>
@endsection

@section('content')
    <table id="galleryTable" class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-4 py-2">No</th>
                <th class="border border-gray-300 px-4 py-2">Gambar</th>
                <th class="border border-gray-300 px-4 py-2">Tampilkan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div id="galleryModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50 p-4">
        <div class="bg-white p-6 rounded-lg w-full max-w-md md:max-w-lg lg:max-w-xl">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Gambar</h2>
            <form id="galleryForm">
                <input type="hidden" id="galleryId">

                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium">Gambar</label>

                    <!-- Preview Gambar -->
                    <div class="flex flex-col items-center">
                        <img id="imagePreview" class="w-24 h-24 rounded mt-2 hidden border border-gray-300 shadow-sm"
                            alt="Foto gallery">
                    </div>

                    <!-- Input File -->
                    <input type="file" id="image" name="image"
                        class="w-full px-3 py-2 border rounded mt-2 cursor-pointer">
                </div>

                <div class="mb-4">
                    <label for="is_visible" class="block text-sm font-medium">Tampilkan</label>
                    <select id="is_visible" name="is_visible" class="w-full px-3 py-2 border rounded mt-2">
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="closeModal" class="px-4 py-2 bg-gray-500 text-white rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Simpan</button>
                </div>
            </form>
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

            let table = $('#galleryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.gallery.getData') }}",
                order: [
                    [2, 'asc']
                ],
                columns: [{
                        data: null,
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false,
                        render: data => data ?
                            `<img src="${data}" class="w-12 h-12" alt="Foto gallery">` : '-'
                    },
                    {
                        data: 'is_visible',
                        name: 'is_visible',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: '<"flex flex-wrap justify-between items-center gap-2 text-sm mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>',
            });

            $('#galleryTable tbody').css('cursor', 'pointer');

            $('#galleryTable tbody').on('click', 'tr', function() {
                $('#galleryTable tbody tr').removeClass('selected');
                $(this).addClass('selected');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#galleryTable').length) {
                    $('#galleryTable tbody tr').removeClass('selected');
                }
            });

            $(document).on('change', '.toggle-visible', function() {
                const id = $(this).data('id');
                const isVisible = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: `/dashboard/gallery/toggle-visible/${id}`,
                    method: 'POST',
                    data: {
                        is_visible: isVisible
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire("Berhasil!", response.message, "success");
                        table.ajax.reload(null, false);
                    },
                    error: function() {
                        Swal.fire("Error!", "Gagal mengubah status tampil.", "error");
                    }
                });
            });

            $('#addNew').click(function() {
                $('#modalTitle').text('Tambah Foto Gallery');
                $('#galleryId').val('');
                $('#image').val('');
                $('#is_visible').val('');
                $('#galleryModal').removeClass('hidden');
            });

            $('#editSelected').click(function() {
                let selectedRow = table.row('.selected').data();
                // console.log("Selected Row Data:", selectedRow);

                if (!selectedRow) {
                    Swal.fire("Peringatan!", "Pilih satu data untuk diedit!", "warning");
                    return;
                }

                $('#modalTitle').text('Edit Foto Gallery');
                $('#galleryId').val(selectedRow.id);

                if (selectedRow.image) {
                    $('#imagePreview').attr('src', selectedRow.image).removeClass('hidden');
                } else {
                    $('#imagePreview').addClass('hidden');
                }

                $('#is_visible').val(selectedRow.is_visible_value);

                $('#galleryModal').removeClass('hidden');
            });

            $('#image').change(function(event) {
                let input = event.target;
                if (input.files && input.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).removeClass('hidden');
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
                            url: `/dashboard/gallery/destroy/${selectedRow.id}`,
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

            $('#galleryModal').click(function(e) {
                if (e.target === this) {
                    $(this).addClass('hidden');
                }
            });

            $('#closeModal').click(function() {
                $('#galleryModal').addClass('hidden');
            });

            $('#galleryForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                let id = $('#galleryId').val();
                let url = id ? `/dashboard/gallery/update/${id}` : `/dashboard/gallery/store`;

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
                        $('#galleryModal').addClass('hidden');
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
