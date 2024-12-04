/*add member*/
    $(document).ready(function() {
        // Toggle membership options
        $('#membershipType').change(function() {
            const type = $(this).val();
            $('#subscriptionOptions').toggle(type === 'Subscription');
            $('#sessionPriceOptions').toggle(type === 'SessionPrice');
        });

        // Initialize DataTable
        $('#membersTable').DataTable({ scrollX: true });
    });
        $(document).ready(function(){
            $('#membershipType').change(function() {
                var membershipType = $(this).val();
                if (membershipType === 'Subscription') {
                    $('#subscriptionOptions').show();
                    $('#sessionPriceOptions').hide();
                } else {
                    $('#sessionPriceOptions').show();
                    $('#subscriptionOptions').hide();
                }
            });

            // Submit form via AJAX
            $('#registerForm').submit(function(e){
                e.preventDefault();
                var otp = Math.floor(100000 + Math.random() * 900000);
                var otpExpiration = new Date(new Date().getTime() + 15 * 60000).toISOString(); 
                
                $.ajax({
                    type: "POST",
                    url: "../action/add_member_process.php",
                    data: {
                        username: $('#username').val(),
                        email: $('#email').val(),
                        password: $('#password').val(),
                        gender: $('#gender').val(),
                        age: $('#age').val(),
                        address: $('#address').val(),
                        membershipType: $('#membershipType').val(),
                        subscriptionMonths: $('#subscriptionMonths').val(),
                        sessionPrice: $('#sessionPrice').val(),
                        otp: otp,
                        otpExpiration: otpExpiration
                    },
                    success: function(response){
                        if (response.trim() === "Email already registered.") {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Email already registered.',
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Registration Successful!',
                                text: 'Verification OTP sent to your email.',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'otp.php?email=' + $('#email').val();
                                }
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Unable to send OTP. Please try again later.',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
/*Update member*/
    $(document).ready(function() {
        // Initialize DataTable
        $('#membersTable').DataTable();

 $(document).ready(function() {
    // Edit button click event
    $('.editBtn').click(function() {
        var memberID = $(this).data('id');
        $.ajax({
            url: '../action/fetch_member.php',
            method: 'GET',
            data: { memberID: memberID },
            success: function(response) {
                var data = JSON.parse(response);
                $('#editUsername').val(data.Username);
                $('#editEmail').val(data.Email);
                $('#editGender').val(data.Gender);
                $('#editAge').val(data.Age);
                $('#editAddress').val(data.Address);
                $('#editMembershipStatus').val(data.MembershipStatus);
                $('#editMemberForm').attr('action', '../action/edit_member.php?MemberID=' + memberID);
                $('#editMemberModal').modal('show');
            }
        });
    });

    // Delete button click event
    $('.deleteBtn').click(function() {
        var memberID = $(this).data('id');
        
        // SweetAlert confirmation for deletion
        Swal.fire({
            title: 'Are you sure?',
            text: "You will not be able to recover this member!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform the delete action if confirmed
                window.location.href = 'delete_member.php?MemberID=' + memberID;
            }
        });
    });
});


    });