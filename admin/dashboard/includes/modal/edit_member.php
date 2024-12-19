<!-- Edit Member Modal -->
    <link rel="stylesheet" href="../../css/modal.css">
<div class="modal fade" id="editMemberModal" tabindex="-1" role="dialog" aria-labelledby="editMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editMemberForm" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Member</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editUsername">Username</label>
                        <input type="text" class="form-control" id="editUsername" name="editUsername" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="editEmail" required readonly>
                    </div>
<!--                    <div class="form-group">
                        <label for="editGender">Gender</label>
                        <select class="form-control" id="editGender" name="editGender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>-->
                    <div class="form-group">
                        <label for="editBalance">Balance</label> <!-- Balance Field -->
                        <input type="number" class="form-control" id="editBalance" name="editBalance" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="editAddress">Address</label>
                        <input type="text" class="form-control" id="editAddress" name="editAddress" required>
                    </div>
<!--                    <div class="form-group">
                        <label for="editMembershipStatus">Membership Status</label>
                        <select class="form-control" id="editMembershipStatus" name="editMembershipStatus" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Suspended">Suspended</option>
                        </select>
                    </div>-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
