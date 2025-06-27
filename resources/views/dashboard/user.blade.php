@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('page-title', 'User')

@section('toolbar')
    <div class="flex space-x-2">
        @can('user-store')
            <button id="addNew" class="px-4 py-2 bg-blue-500 text-white rounded">Tambah</button>
        @endcan
        @can('user-update')
            <button id="editSelected" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</button>
        @endcan
        @can('user-destroy')
            <button id="deleteSelected" class="px-4 py-2 bg-red-500 text-white rounded">Hapus</button>
        @endcan
    </div>
@endsection

@section('content')
    <table id="userTable" class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-4 py-2">No</th>
                <th class="border border-gray-300 px-4 py-2">Nama</th>
                <th class="border border-gray-300 px-4 py-2">Email</th>
                <th class="border border-gray-300 px-4 py-2">No Hp</th>
                <th class="border border-gray-300 px-4 py-2">Role</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <div id="userModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50 p-4">
        <div class="bg-white p-6 rounded-lg w-full max-w-md md:max-w-lg lg:max-w-xl">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah User</h2>
            <form id="userForm">
                <input type="hidden" id="userId">

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium">Nama</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border rounded">
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded">
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium">No HP</label>
                    <input type="text" id="phone" name="phone" class="w-full px-3 py-2 border rounded">
                </div>

                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium">Role</label>
                    <select id="role" name="role" class="w-full px-3 py-2 border rounded">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
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

            let table = $('#userTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.user.getData') }}",
                order: [
                    [4, 'asc']
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
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        defaultContent: "-"
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                ],
                dom: '<"flex flex-wrap justify-between items-center gap-2 text-sm mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>',
            });

            $('#userTable tbody').css('cursor', 'pointer');

            $('#userTable tbody').on('click', 'tr', function() {
                $('#userTable tbody tr').removeClass('selected');
                $(this).addClass('selected');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#userTable').length) {
                    $('#userTable tbody tr').removeClass('selected');
                }
            });

            $('#addNew').click(function() {
                $('#modalTitle').text('Tambah User');
                $('#userId').val('');
                $('#name').val('');
                $('#email').val('');
                $('#phone').val('');
                $('#userModal').removeClass('hidden');
            });

            $('#editSelected').click(function() {
                let selectedRow = table.row('.selected').data();

                if (!selectedRow) {
                    Swal.fire("Peringatan!", "Pilih satu data untuk diedit!", "warning");
                    return;
                }

                $('#modalTitle').text('Edit User');
                $('#userId').val(selectedRow.id);
                $('#name').val(selectedRow.name);
                $('#phone').val(selectedRow.phone);
                $('#email').val(selectedRow.email);

                $('#userModal').removeClass('hidden');
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
                            url: `/dashboard/user/destroy/${selectedRow.id}`,
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

            $('#userModal').click(function(e) {
                if (e.target === this) {
                    $(this).addClass('hidden');
                }
            });

            $('#closeModal').click(function() {
                $('#userModal').addClass('hidden');
            });

            $('#userForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                let id = $('#userId').val();
                let url = id ? `/dashboard/user/update/${id}` : `/dashboard/user/store`;

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
                        $('#userModal').addClass('hidden');
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
