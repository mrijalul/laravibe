<!-- Modal -->
<div class="modal fade" id="ajaxModal" tabindex="-1" aria-labelledby="ajaxModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="ajaxModalLabel">Add Permission</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="permissionForm" name="permissionForm">
                <div class="modal-body">
                    <input type="hidden" name="permission_id" id="permission_id">
                    <div class="mb-3">
                        <label for="name" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control editName" id="name" name="name"
                                placeholder="Enter Permission Name" value="" required="">
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