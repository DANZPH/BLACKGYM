<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/head.php'; ?>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="content-wrapper">
        <?php include 'includes/header.php'; ?>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-lg">
                        <div class="card-header bg-primary text-white text-center">
                            <h2>User Registration</h2>
                        </div>
                        <div class="card-body">
                            <form id="registerForm" class="needs-validation" novalidate>
                                <!-- Username -->
                                <div class="form-group mb-3">
                                    <label for="username" class="form-label">Username <span class="text-danger">*</span>:</label>
                                    <input type="text" id="username" name="username" class="form-control" required>
                                    <div class="invalid-feedback">Please enter a username.</div>
                                </div>

                                <!-- Email -->
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Optional">
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>

                                <!-- Password -->
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span>:</label>
                                    <input type="password" id="password" name="password" class="form-control" required>
                                    <div class="invalid-feedback">Password is required.</div>
                                </div>

                                <!-- Gender -->
                                <div class="form-group mb-3">
                                    <label for="gender" class="form-label">Gender:</label>
                                    <select id="gender" name="gender" class="form-select">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <!-- Age -->
                                <div class="form-group mb-3">
                                    <label for="age" class="form-label">Age <span class="text-danger">*</span>:</label>
                                    <input type="number" id="age" name="age" class="form-control" required>
                                    <div class="invalid-feedback">Please enter your age.</div>
                                </div>

                                <!-- Address -->
                                <div class="form-group mb-3">
                                    <label for="address" class="form-label">Address <span class="text-danger">*</span>:</label>
                                    <input type="text" id="address" name="address" class="form-control" required>
                                    <div class="invalid-feedback">Please provide your address.</div>
                                </div>

                                <!-- Membership Type -->
                                <div class="form-group mb-3">
                                    <label for="membershipType" class="form-label">Choose Membership Type <span class="text-danger">*</span>:</label>
                                    <select id="membershipType" name="membershipType" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="SessionPrice">Pay Per Session</option>
                                        <option value="Subscription">Subscription</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a membership type.</div>
                                </div>

                                <!-- Subscription Options -->
                                <div class="form-group mb-3" id="subscriptionOptions" style="display: none;">
                                    <label for="subscriptionMonths" class="form-label">Number of Subscription Months:</label>
                                    <input type="number" id="subscriptionMonths" name="subscriptionMonths" class="form-control" min="1" max="12">
                                </div>

                                <!-- Session Price Options -->
                                <div class="form-group mb-3" id="sessionPriceOptions" style="display: none;">
                                    <label for="sessionPrice" class="form-label">Price Per Session:</label>
                                    <input type="number" id="sessionPrice" name="sessionPrice" class="form-control" value="50" min="0">
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JS Libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Form Validation and AJAX -->
        <script>
            $(document).ready(function () {
                // Toggle subscription/session options
                $('#membershipType').change(function () {
                    var membershipType = $(this).val();
                    if (membershipType === 'Subscription') {
                        $('#subscriptionOptions').show();
                        $('#sessionPriceOptions').hide();
                    } else if (membershipType === 'SessionPrice') {
                        $('#sessionPriceOptions').show();
                        $('#subscriptionOptions').hide();
                    } else {
                        $('#subscriptionOptions').hide();
                        $('#sessionPriceOptions').hide();
                    }
                });

                // Form Submission with AJAX
                $('#registerForm').submit(function (e) {
                    e.preventDefault();
                    // Validate form fields
                    if (!this.checkValidity()) {
                        e.stopPropagation();
                        this.classList.add('was-validated');
                        return;
                    }

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
                        success: function (response) {
                            if (response.trim() === "Email already registered.") {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Email already registered.'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Registration Successful!',
                                    text: 'Verification OTP sent to your email.',
                                }).then(() => {
                                    window.location.href = 'otp.php?email=' + $('#email').val();
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Unable to send OTP. Please try again later.'
                            });
                        }
                    });
                });
            });
        </script>
    </div>
</body>
</html>