@extends('backend.layouts.app')

@section('title','Permission')

@section('pagetitle')
<div class="pagetitle">
	<h1>Permission</h1>
	<nav>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="{{ route('lv-admin.dashboard') }}">Dashboard</a></li>
			<li class="breadcrumb-item active">Permission</li>
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
						Permission</button>
				</div>
				<table class="table table-responsive table-striped table-sm table-hover" id="datatable">
					<thead>
						<tr>
							<th>Permission</th>
							<th></th>
						</tr>
					</thead>
				</table>
			</div>

		</div>
	</div>
</div>
@include('backend.permission.modal')
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

		var table = $('#datatable').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: "{{ route('lv-admin.permissions.getdata') }}",
				type: "POST",
				data: {
					_token: "{{ csrf_token() }}"
				}
			},
			columns: [
				{ data: 'name', name: 'name' },
				{ data: 'action', name: 'action', orderable: false, searchable: false }
			]
		});

		$('input').on('input', function () {
			$(this).removeClass('is-invalid');
			$(this).next('.text-danger').remove();
		});

		function resetForm() {
			$('#permissionForm').trigger('reset');
			$('#permission_id').val('');
			$('#name').removeClass('is-invalid');
			$('.text-danger').html('');
		}

		$('#addData').click(function () {
			resetForm();
			$('#ajaxModalLabel').html('Add New Permission');
			$('#saveBtn').val('create');
			$('#ajaxModal').modal('show');
		});

		$('#saveBtn').click(function() {
			var formData = new FormData($('#permissionForm')[0]);

			$.ajax({
				url: "{{ route('lv-admin.permissions.permission.store') }}",
				type: "POST",
				data: formData,
				processData: false,
				contentType: false,
				success: function(data) {
					resetForm();

					$('#ajaxModal').modal('hide');
					
					table.draw();

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

		$('body').on('click', '.editData', function () {
			var uuid = $(this).data('uuid');
			resetForm();

			$.ajax({
				url: "{{ route('lv-admin.permissions.permission.edit', ':uuid') }}".replace(':uuid', uuid),
				type: "GET",
				success: function (data) {
					$('#ajaxModalLabel').html('Edit Permission');
					$('#saveBtn').val('edit');
					$('#permission_id').val(data.uuid);
					$('.editName').val(data.name);
					$('#ajaxModal').modal('show');
				},
				error: function (data) {
					console.log('Error:', data);
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
						url: "{{ route('lv-admin.permissions.permission.destroy', ':uuid') }}".replace(':uuid', uuid),
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

	});
</script>
@endpush