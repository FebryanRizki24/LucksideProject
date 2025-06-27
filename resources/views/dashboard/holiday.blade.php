@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('page-title', 'Hari Libur')

@section('toolbar')
    <div class="flex space-x-2">
        @can('holiday-store')
            <button id="addNew" class="px-4 py-2 bg-blue-500 text-white rounded">Tambah</button>
        @endcan
        @can('holiday-update')
            <button id="editSelected" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</button>
        @endcan
        @can('holiday-destroy')
            <button id="deleteSelected" class="px-4 py-2 bg-red-500 text-white rounded">Hapus</button>
        @endcan
    </div>
@endsection

@section('content')
    <table id="holidayTable" class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-4 py-2">No</th>
                <th class="border border-gray-300 px-4 py-2">Tanggal</th>
                <th class="border border-gray-300 px-4 py-2">Deskripsi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div id="holidayModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50 p-4">
        <div class="bg-white p-6 rounded-lg w-full max-w-md md:max-w-lg lg:max-w-xl">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Hari Libur</h2>
            <form id="holidayForm">
                <input type="hidden" id="holidayId">

                <div class="mb-4">
                    <label for="date" class="block text-sm font-medium">Tanggal</label>
                    <input type="text" id="date" name="date" class="w-full px-3 py-2 border rounded">
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium">Deskripsi</label>
                    <input type="text" id="description" name="description" class="w-full px-3 py-2 border rounded">
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            flatpickr("#date", {
                dateFormat: "Y-m-d",
                defaultDate: new Date(),
                minDate: "today",
                disableMobile: true
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content')
                }
            });

            let table = $('#holidayTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.holiday.getData') }}",
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
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                ],
                dom: '<"flex flex-wrap justify-between items-center gap-2 text-sm mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>',
            });

            $('#holidayTable tbody').css('cursor', 'pointer');

            $('#holidayTable tbody').on('click', 'tr', function() {
                $('#holidayTable tbody tr').removeClass('selected');
                $(this).addClass('selected');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#holidayTable').length) {
                    $('#holidayTable tbody tr').removeClass('selected');
                }
            });

            $('#addNew').click(function() {
                $('#modalTitle').text('Tambah Hari Libur');
                $('#holidayId').val('');
                $('#date').val('');
                $('#description').val('');
                $('#holidayModal').removeClass('hidden');
            });

            $('#editSelected').click(function() {
                let selectedRow = table.row('.selected').data();
                console.log(selectedRow);

                if (!selectedRow) {
                    Swal.fire("Peringatan!", "Pilih satu data untuk diedit!", "warning");
                    return;
                }

                $('#modalTitle').text('Edit Hari Libur');
                $('#holidayId').val(selectedRow.id);
                $('#date').val(selectedRow.date);
                $('#description').val(selectedRow.description);

                $('#holidayModal').removeClass('hidden');
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
                            url: `/dashboard/holiday/destroy/${selectedRow.id}`,
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

            $('#holidayModal').click(function(e) {
                if (e.target === this) {
                    $(this).addClass('hidden');
                }
            });

            $('#closeModal').click(function() {
                $('#holidayModal').addClass('hidden');
            });

            $('#holidayForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                let id = $('#holidayId').val();
                let url = id ? `/dashboard/holiday/update/${id}` : `/dashboard/holiday/store`;

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
                        $('#holidayModal').addClass('hidden');
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
