@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('page-title', 'Jadwal Barberman')

@section('toolbar')
    <div class="flex space-x-2">
        <button id="addNew" class="px-4 py-2 bg-blue-500 text-white rounded">Tambah</button>
        <button id="editSelected" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</button>
        <button id="deleteSelected" class="px-4 py-2 bg-red-500 text-white rounded">Hapus</button>
    </div>
@endsection

@section('content')
    <table id="scheduleTable" class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-4 py-2">No</th>
                <th class="border border-gray-300 px-4 py-2">Barberman</th>
                <th class="border border-gray-300 px-4 py-2">Jam Mulai</th>
                <th class="border border-gray-300 px-4 py-2">Jam Selesai</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <div id="scheduleModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden px-4">
        <div class="bg-white p-6 rounded-lg w-full sm:w-2/3 md:w-1/2 lg:w-1/3">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Jadwal Barberman</h2>
            <form id="scheduleForm">
                <input type="hidden" id="scheduleId">

                <div class="mb-4">
                    <label for="barberman_id" class="block text-sm font-medium">Barberman</label>
                    <select id="barberman_id" name="barberman_id" class="w-full px-3 py-2 border rounded">
                        @foreach ($barbermans as $barberman)
                            <option value="{{ $barberman->id }}">{{ $barberman->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="start_time" class="block text-sm font-medium">Jam Mulai</label>
                    <input id="start_time" name="start_time" class="w-full px-3 py-2 border rounded"
                        placeholder="Contoh: 08:00">
                </div>

                <div class="mb-4">
                    <label for="end_time" class="block text-sm font-medium">Jam Selesai</label>
                    <input id="end_time" name="end_time" class="w-full px-3 py-2 border rounded"
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

            let table = $('#scheduleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/dashboard/barberman/schedule/getData',
                order: [
                    [1, 'asc']
                ],
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'barberman_name',
                        name: 'barbermans.name'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    }
                ],
                dom: '<"flex flex-wrap justify-between items-center gap-2 text-sm mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>',
            });

            $('#scheduleTable tbody').css('cursor', 'pointer');

            $('#scheduleTable tbody').on('click', 'tr', function() {
                $('#scheduleTable tbody tr').removeClass('selected');
                $(this).addClass('selected');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#scheduleTable').length) {
                    $('#scheduleTable tbody tr').removeClass('selected');
                }
            });

            $('#addNew').click(function() {
                $('#modalTitle').text('Tambah Jadwal');
                $('#scheduleId').val('');
                $('#name').val('');
                $('#status').val('');
                $('#photo').val('');
                $('#scheduleModal').removeClass('hidden');
            });

            $('#editSelected').click(function() {
                let selectedRow = table.row('.selected').data();

                if (!selectedRow) {
                    Swal.fire("Peringatan!", "Pilih satu data untuk diedit!", "warning");
                    return;
                }

                $('#modalTitle').text('Edit Jadwal');
                $('#scheduleId').val(selectedRow.id);
                $('#barberman_id').val(selectedRow.barberman_id);
                $('#start_time').val(selectedRow.start_time);
                $('#end_time').val(selectedRow.end_time);

                $('#scheduleModal').removeClass('hidden');
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
                            url: `/dashboard/barberman/schedule/destroy/${selectedRow.id}`,
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

            $('#scheduleModal').click(function(e) {
                if (e.target === this) {
                    $(this).addClass('hidden');
                }
            });

            $('#closeModal').click(function() {
                $('#scheduleModal').addClass('hidden');
            });

            $('#scheduleForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                let id = $('#scheduleId').val();
                let url = id ? `/dashboard/barberman/schedule/update/${id}` :
                    `/dashboard/barberman/schedule/store`;

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
                        $('#scheduleModal').addClass('hidden');
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
        });
    </script>
@endpush
