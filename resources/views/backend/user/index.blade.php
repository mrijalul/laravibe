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
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/2.1.3/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.3/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
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
                {data: 'updated_at', name: 'updated_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('body').on('click', '.editData', function () {
            var uuid = $(this).data('uuid');
            console.log('user uuid : ', uuid);
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
                },
                error: function (data) {
                    console.log('Error:', data);
                    $('#saveBtn').html('Save changes');
                }
            });
        });

        $('body').on('click', '.deleteData', function () {
            var uuid = $(this).data("uuid");

            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                $.ajax({
                    url: "{{ route('lv-admin.users.user.destroy', ':uuid') }}".replace(':uuid', uuid),
                    type: "DELETE",
                    success: function (data) {
                        table.draw();
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

    })
</script>
@endpush