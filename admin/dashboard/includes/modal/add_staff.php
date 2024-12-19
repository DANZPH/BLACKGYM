    <link rel="stylesheet" href="../../css/modal.css">
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body">
                <form id="registerStaffForm">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <!-- Staff-specific Job Title -->
                    <div class="form-group">
                        <label for="jobTitle">Job Title:</label>
                        <select id="jobTitle" name="jobTitle" class="form-control" required>
                            <option value="Trainer">Trainer</option>
                            <option value="Cashier">Cashier</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <!-- Register button styled like the login button -->
                        <button type="submit" class="btn btn-primary btn-block">Register Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>