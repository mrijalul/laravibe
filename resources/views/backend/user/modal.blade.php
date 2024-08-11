<!-- Modal -->
<div class="modal fade" id="ajaxModal" tabindex="-1" aria-labelledby="ajaxModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modelHeading">Add User</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm" name="userForm">
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="user_id">
                    <div class="mb-3">
                        <label for="name" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control editName" id="name" name="name"
                                placeholder="Enter Name" value="" maxlength="50" required="">
                            <div class="text-danger"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-12">
                            <input type="email" id="email" name="email" required="" placeholder="Enter Email"
                                class="form-control editEmail">
                            <div class="text-danger"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="col-sm-2 control-label">Password</label>
                        <div class="col-sm-12">
                            <input type="password" id="password" name="password" placeholder="Enter Password"
                                class="form-control" minlength="8">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
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