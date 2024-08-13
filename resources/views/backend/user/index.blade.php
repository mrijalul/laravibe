@extends('backend.layouts.app')

@section('title','Users')

@section('pagetitle')
<div class="pagetitle">
    <h1>Users</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('lv-admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Users</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="col-lg-12">
    <div class="row">
        <div class="card info-card">


            <div class="card-body">
                <div class="text-start my-3">
                    <button class="btn btn-primary btn-sm" type="button" id="addData"><i class="bi bi-person-add"></i>
                        Users</button>
                </div>
                <table class="table table-responsive table-striped table-sm table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th>Fullname</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Last Updated</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>
</div>
@include('backend.user.modal')
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">

@endpush

@push('scripts')
<script src="https://cdn.datatables.net/2.1.3/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.3/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('input').on('input', function () {
            $(this).removeClass('is-invalid');
            $(this).next('.text-danger').remove();  // Menghapus pesan error di bawah input
        });


        function resetForm() {
            $('#userForm').trigger("reset");
            $('#user_id').val('');
            $('#modelHeading').html("");
            $('#saveBtn').html('Save changes');
        }

        $('#addData').click(function () {
            resetForm();
            $('#saveBtn').val("create-user");
            $('#user_id').val('');
            $('#modelHeading').html("Create New User");
            $('#ajaxModal').modal('show');
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('lv-admin.users.getdata') }}",
                type: "POST"
            },
            columns: [
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'roles', name: 'roles', orderable: false, searchable: false, title: 'Roles'},
                {data: 'updated_at', name: 'updated_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('body').on('click', '.editData', function () {
            var uuid = $(this).data('uuid');
            resetForm();

            $.ajax({
                url: "{{ route('lv-admin.users.user.edit', ':uuid') }}".replace(':uuid', uuid),
                type: "GET",
                success: function (data) {
                    $('#modelHeading').html("Edit User");
                    $('#saveBtn').val("edit-user");
                    $('#ajaxModal').modal('show');
                    $('#user_id').val(data.uuid);
                    $('.editName').val(data.name);
                    $('.editEmail').val(data.email);
                    $('#password').val('');
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        });


        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Sending..');

            $.ajax({
                data: $('#userForm').serialize(),
                url: "{{ route('lv-admin.users.user.store') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    resetForm();  // Reset form setelah data disimpan
                    
                    $('#ajaxModal').modal('hide');
                    
                    table.draw();

                    // Menghapus semua kelas is-invalid dan pesan error
                    $('input').removeClass('is-invalid');
                    $('.text-danger').remove();

                    $.toast({
                        heading: 'Success',
                        text: data.success,
                        showHideTransition: 'slide',
                        icon: 'success',
                        position: 'top-right'
                    });
                },
                error: function (data) {
                    $('#saveBtn').html('Save changes');
                    
                    // Clear previous error messages
                    $('.text-danger').remove();
                    
                    // Display error messages below each input
                    if (data.responseJSON && data.responseJSON.errors) {
                        $.each(data.responseJSON.errors, function (key, value) {
                            let input = $('input[name=' + key + ']');
                            input.addClass('is-invalid');
                            input.after('<div class="text-danger">' + value[0] + '</div>');
                        });
                    }

                    // Display toast notification
                    $.toast({
                        heading: 'Error',
                        text: 'Please fix the errors and try again.',
                        showHideTransition: 'fade',
                        icon: 'error',
                        position: 'top-right'
                    });
                }
            });
        });

        $('body').on('click', '.deleteData', function () {
            var uuid = $(this).data("uuid");

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda tidak akan bisa mengembalikan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('lv-admin.users.user.destroy', ':uuid') }}".replace(':uuid', uuid),
                        type: "DELETE",
                        success: function (data) {
                            table.draw();
                            Swal.fire(
                                'Deleted!',
                                'Data berhasil dihapus.',
                                'success'
                            );
                        },
                        error: function (data) {
                            console.log('Error:', data);
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus data.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

    })
</script>
@endpush