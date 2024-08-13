@extends('backend.layouts.app')

@section('title','Roles')

@section('pagetitle')
<div class="pagetitle">
	<h1>Roles</h1>
	<nav>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="{{ route('lv-admin.dashboard') }}">Dashboard</a></li>
			<li class="breadcrumb-item active">Roles</li>
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
						Role</button>
				</div>
				<table class="table table-responsive table-striped table-sm table-hover" id="datatable">
					<thead>
						<tr>
							<th>Role</th>
							<th>Users</th>
							<th></th>
						</tr>
					</thead>
				</table>
			</div>

		</div>
	</div>
</div>
@include('backend.role.modal')
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet"
	href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/2.1.3/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.3/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
			$('#roleForm').trigger("reset");
			$('#role_id').val('');
			$('#modelHeading').html("");
			$('#saveBtn').html('Save changes');
		}

		$('#addData').click(function () {
			resetForm();
			$('#saveBtn').val("create-role");
			$('#role_id').val('');
			$('#modelHeading').html("Create New Role");
			$('#ajaxModal').modal('show');
		});

		var table = $('#datatable').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: "{{ route('lv-admin.roles.getdata') }}",
				type: "POST",
				data: {
					_token: "{{ csrf_token() }}"
				}
			},
			columns: [
				{ data: 'name', name: 'name' },
				{ data: 'users', name: 'users', orderable: false, searchable: false, title: 'Users' },
				{ data: 'action', name: 'action', orderable: false, searchable: false },
			]
		});

		$('body').on('click', '.editData', function () {
			var uuid = $(this).data('uuid');
			resetForm();

			$.ajax({
				url: "{{ route('lv-admin.roles.role.edit', ':uuid') }}".replace(':uuid', uuid),
				type: "GET",
				success: function (data) {
					$('#modelHeading').html("Edit User");
					$('#saveBtn').val("edit-user");
					$('#ajaxModal').modal('show');
					$('#role_id').val(data.uuid);
					$('.editName').val(data.name);
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
				data: $('#roleForm').serialize(),
				url: "{{ route('lv-admin.roles.role.store') }}",
				type: "POST",
				dataType: 'json',
				success: function (data) {
					resetForm();
					
					$('#ajaxModal').modal('hide');
					
					table.draw();

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
					
					$('.text-danger').remove();

					if (data.responseJSON && data.responseJSON.errors) {
						$.each(data.responseJSON.errors, function (key, value) {
							let input = $('input[name=' + key + ']');
							input.addClass('is-invalid');
							input.after('<div class="text-danger">' + value[0] + '</div>');
						});
					}

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

		function resetAssignRoleForm() {
			
			$('#assignRoleForm')[0].reset();

			$('#user_id').val(null).trigger('change');
		}

		$('body').on('click', '.assignRole', function () {
			var role_id = $(this).data('uuid');
			var role_name = $(this).data('rolename');
			$('#assignRoleId').val(role_id);
			$('#assignRoleModalLabel').html('Assign Role <b>' + role_name + '</b> to User');

			var assignedUserIds = [];

			$.ajax({
				url: "{{ route('lv-admin.roles.getassigned.users') }}",
				type: "POST",
				data: {
					_token: "{{ csrf_token() }}",
					role_id: role_id
				},
				success: function (data) {
					$('#user_id').empty();

					if (data.assignedUsers && data.assignedUsers.length > 0) {
						assignedUserIds = $.map(data.assignedUsers, function (user) {
							return user.uuid;
						});

						var assignedUsers = $.map(data.assignedUsers, function (user) {
							return {
								id: user.uuid,
								text: user.name
							};
						});

						$.each(assignedUsers, function (index, value) {
							var option = new Option(value.text, value.id, true, true);
							$('#user_id').append(option);
						});

						$('#user_id').trigger('change');
					}

					$('#user_id').select2({
						theme: 'bootstrap-5',
						ajax: {
							url: "{{ route('lv-admin.roles.getdata.user') }}",
							type: "POST",
							dataType: 'json',
							delay: 250,
							data: function (params) {
								return {
									_token: "{{ csrf_token() }}",
									search: params.term
								};
							},
							processResults: function (response) {
								return {
									results: $.map(response.users, function (user) {
										if (!assignedUserIds.includes(user.uuid)) {
											return {
												id: user.uuid,
												text: user.name
											};
										}
									})
								};
							},
							cache: true
						},
						minimumInputLength: 0,
						dropdownParent: $('#labelUserID'),
						placeholder: "Select User",
						multiple: true
					});
				},
				error: function (data) {
					console.log('Error:', data);
				}
			});

			$('#assignRoleModal').modal('show');
		});


		$('#assignRoleForm').submit(function (e) {
			e.preventDefault();
			var formData = $(this).serialize();
			var url = "{{ route('lv-admin.roles.assign') }}";

			$.ajax({
				data: formData,
				url: url,
				type: "POST",
				dataType: 'json',
				success: function (data) {
					$('#assignRoleModal').modal('hide');
					$('#rolesTable').DataTable().ajax.reload();
					$.toast({
						heading: 'Success',
						text: data.success,
						showHideTransition: 'slide',
						icon: 'success',
						position: 'top-right'
					});
					resetAssignRoleForm();
				},
				error: function (data) {
					console.log('Error:', data);
					$.toast({
						heading: 'Error',
						text: 'Terjadi kesalahan saat mengassign role.',
						showHideTransition: 'fade',
						icon: 'error',
						position: 'top-right'
					});
				}
			});
		});

	})
</script>
@endpush