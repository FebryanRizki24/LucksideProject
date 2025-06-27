@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('page-title', 'Jam Operasional')

@section('toolbar')
    <div class="flex space-x-2">
        @can('operationalHour-store')
            <button id="addNew" class="px-4 py-2 bg-blue-500 text-white rounded">Tambah</button>
        @endcan
        @can('operationalHour-update')
            <button id="editSelected" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</button>
        @endcan
        @can('operationalHour-destroy')
            <button id="deleteSelected" class="px-4 py-2 bg-red-500 text-white rounded">Hapus</button>
        @endcan
    </div>
@endsection

@section('content')
    <table id="operationalHourTable" class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-4 py-2">No</th>
                <th class="border border-gray-300 px-4 py-2">Jam Buka</th>
                <th class="border border-gray-300 px-4 py-2">Jam Tutup</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div id="operationalHourModal"
        class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50 p-4">
        <div class="bg-white p-6 rounded-lg w-full max-w-md md:max-w-lg lg:max-w-xl">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Jam Operasional</h2>
            <form id="operationalHourForm">
                <input type="hidden" id="operationalHourId">

                <div class="mb-4">
                    <label for="open_time" class="block text-sm font-medium">Jam Buka</label>
                    <input id="open_time" name="open_time" class="w-full px-3 py-2 border rounded"
                        placeholder="Contoh: 08:00">
                </div>

                <div class="mb-4">
                    <label for="close_time" class="block text-sm font-medium">Jam Tutup</label>
                    <input id="close_time" name="close_time" class="w-full px-3 py-2 border rounded"
                        placeholder="Contoh: 23:00">
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

            let table = $('#operationalHourTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.operationalHour.getData') }}",
                // order: [[1, 'asc']],
                columns: [{
                        data: null,
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'open_time',
                        name: 'open_time'
                    },
                    {
                        data: 'close_time',
                        name: 'close_time'
                    }
                ],
                dom: '<"flex flex-wrap justify-between items-center gap-2 text-sm mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>',
            });

            $('#operationalHourTable tbody').css('cursor', 'pointer');

            $('#operationalHourTable tbody').on('click', 'tr', function() {
                $('#operationalHourTable tbody tr').removeClass('selected');
                $(this).addClass('selected');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#operationalHourTable').length) {
                    $('#operationalHourTable tbody tr').removeClass('selected');
                }
            });

            $('#addNew').click(function() {
                $('#modalTitle').text('Tambah Jam Operasional');
                $('#operationalHourId').val('');
                $('#open_time').val('');
                $('#close_time').val('');
                $('#operationalHourModal').removeClass('hidden');
            });

            function formatTimeToHHMM(timeStr) {
                if (!timeStr) return '';
                let parts = timeStr.split(':');
                return `${parts[0]}:${parts[1]}`;
            }

            $('#editSelected').click(function() {
                let selectedRow = table.row('.selected').data();
                console.log(selectedRow);

                if (!selectedRow) {
                    Swal.fire("Peringatan!", "Pilih satu data untuk diedit!", "warning");
                    return;
                }

                $('#modalTitle').text('Edit Jam Operasional');
                $('#operationalHourId').val(selectedRow.id);
                $('#open_time').val(formatTimeToHHMM(selectedRow.open_time));
                $('#close_time').val(formatTimeToHHMM(selectedRow.close_time));

                $('#operationalHourModal').removeClass('hidden');
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
                            url: `/dashboard/operationalHour/destroy/${selectedRow.id}`,
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

            $('#operationalHourModal').click(function(e) {
                if (e.target === this) {
                    $(this).addClass('hidden');
                }
            });

            $('#closeModal').click(function() {
                $('#operationalHourModal').addClass('hidden');
            });

            $('#operationalHourForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                let id = $('#operationalHourId').val();
                let url = id ? `/dashboard/operationalHour/update/${id}` :
                    `/dashboard/operationalHour/store`;

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
                        $('#operationalHourModal').addClass('hidden');
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
