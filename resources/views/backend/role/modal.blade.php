<!-- Modal -->
<div class="modal fade" id="ajaxModal" tabindex="-1" aria-labelledby="ajaxModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="modelHeading">Add Role</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="roleForm" name="roleForm">
				<div class="modal-body">
					<input type="hidden" name="role_id" id="role_id">
					<div class="mb-3">
						<label for="name" class="col-sm-2 control-label">Name</label>
						<div class="col-sm-12">
							<input type="text" class="form-control editName" id="name" name="name"
								placeholder="Enter Role Name" value="" maxlength="50" required="">
							<div class="text-danger"></div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="saveBtn" value="create">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal Assign -->
<div class="modal fade" id="assignRoleModal" tabindex="-1" aria-labelledby="assignRoleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="assignRoleModalLabel">Assign Role to User</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="assignRoleForm" name="assignRoleForm">
				<div class="modal-body">
					<input type="hidden" name="role_id" id="assignRoleId">
					<div class="mb-3" id="labelUserID">
						<label for="user_id" class="col-sm-2 control-label">User</label>
						<select class="form-control" id="user_id" name="user_id[]" multiple="multiple" required>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" id="assignRoleBtn">Assign</button>
				</div>
			</form>
		</div>
	</div>
</div>