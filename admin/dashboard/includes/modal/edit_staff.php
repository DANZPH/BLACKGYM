<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" role="dialog" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStaffModalLabel">Edit Staff</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editStaffForm" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editUsername">Username</label>
                        <input type="text" class="form-control" id="editUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="editJobTitle">Job Title</label>
                        <select class="form-control" id="editJobTitle" name="jobTitle" required>
                            <option value="">Select Job Title</option>
                            <option value="Trainer">Trainer</option>
                            <option value="Cashier">Cashier</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editPassword">New Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" id="editPassword" name="password" placeholder="Enter new password or leave blank">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle Edit Staff Form Submission
    $('#editStaffForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: {
                username: $('#editUsername').val(),
                email: $('#editEmail').val(),
                jobTitle: $('#editJobTitle').val(),
                password: $('#editPassword').val()
            },
            success: function(response) {
                if (response.trim() === "Staff updated successfully.") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Staff updated successfully!',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); // Reload the page to show updated data
                        }
                    });
                    $('#editStaffModal').modal('hide');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating staff.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
</script>