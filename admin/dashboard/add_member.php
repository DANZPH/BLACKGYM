<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin/dashboard/includes/styles.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header h2">User Registration</div>
                    <div class="card-body">
                        <form id="registerForm">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Optional">
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>

                            <!-- Gender, Age, and Address Fields -->
                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select id="gender" name="gender" class="form-control" >
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="age">Age:</label>
                                <input type="number" id="age" name="age" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" id="address" name="address" class="form-control" required>
                            </div>

                            <!-- Membership Option Fields -->
                            <div class="form-group">
                                <label for="membershipType">Choose Membership Type:</label>
                                <select id="membershipType" name="membershipType" class="form-control" required>
                                    <option value="SessionPrice">Pay Per Session</option>
                                    <option value="Subscription">Subscription</option>
                                </select>
                            </div>

                            <div class="form-group" id="subscriptionOptions" style="display: none;">
                                <label for="subscriptionMonths">Choose Number of Months:</label>
                                <input type="number" id="subscriptionMonths" name="subscriptionMonths" class="form-control" min="1" max="12">
                            </div>

                            <div class="form-group" id="sessionPriceOptions" style="display: none;">
                                <label for="sessionPrice">Price per Session:</label>
                                <input type="number" id="sessionPrice" name="sessionPrice" class="form-control" value="50" min="0">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function(){
            // Toggle Subscription and SessionPrice options based on membership type
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

                // Generate a random OTP for the user
                var otp = Math.floor(100000 + Math.random() * 900000);
                var otpExpiration = new Date(new Date().getTime() + 15 * 60000).toISOString();  // OTP expires in 15 minutes
                
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
    </script>
</body>
</html>