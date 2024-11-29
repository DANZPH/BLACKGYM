<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/head.php'; ?>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="content-wrapper">
    <?php include 'includes/header.php'; ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg border-0 rounded">
                    <div class="card-header bg-gradient-primary text-white text-center py-4">
                        <h3 class="mb-0">User Registration</h3>
                        <p class="mb-0">Create your account with ease</p>
                    </div>
                    <div class="card-body p-4">
                        <form id="registerForm">
                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email (optional)">
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                            </div>

                            <!-- Gender -->
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select id="gender" name="gender" class="form-select">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <!-- Age -->
                            <div class="mb-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" id="age" name="age" class="form-control" placeholder="Enter your age" required>
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea id="address" name="address" class="form-control" rows="2" placeholder="Enter your address" required></textarea>
                            </div>

                            <!-- Membership Type -->
                            <div class="mb-3">
                                <label for="membershipType" class="form-label">Membership Type</label>
                                <select id="membershipType" name="membershipType" class="form-select" required>
                                    <option value="SessionPrice">Pay Per Session</option>
                                    <option value="Subscription">Subscription</option>
                                </select>
                            </div>

                            <!-- Subscription Options -->
                            <div class="mb-3" id="subscriptionOptions" style="display: none;">
                                <label for="subscriptionMonths" class="form-label">Number of Months</label>
                                <input type="number" id="subscriptionMonths" name="subscriptionMonths" class="form-control" min="1" max="12" placeholder="Enter subscription duration">
                            </div>

                            <!-- Session Price Options -->
                            <div class="mb-3" id="sessionPriceOptions" style="display: none;">
                                <label for="sessionPrice" class="form-label">Price per Session</label>
                                <input type="number" id="sessionPrice" name="sessionPrice" class="form-control" value="50" min="0">
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Membership Type Toggle
            $('#membershipType').change(function() {
                const membershipType = $(this).val();
                if (membershipType === 'Subscription') {
                    $('#subscriptionOptions').slideDown();
                    $('#sessionPriceOptions').slideUp();
                } else {
                    $('#sessionPriceOptions').slideDown();
                    $('#subscriptionOptions').slideUp();
                }
            });

            // Form Submission
            $('#registerForm').submit(function(e) {
                e.preventDefault();
                const otp = Math.floor(100000 + Math.random() * 900000);
                const otpExpiration = new Date(new Date().getTime() + 15 * 60000).toISOString();

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
                    success: function(response) {
                        if (response.trim() === "Email already registered.") {
                            Swal.fire({
                                icon: 'error',
                                title: 'Registration Failed',
                                text: 'This email is already registered.',
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Registration Successful',
                                text: 'A verification OTP has been sent to your email.',
                            }).then(() => {
                                window.location.href = 'otp.php?email=' + $('#email').val();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Unable to process your request. Please try again later.',
                        });
                    }
                });
            });
        });
    </script>

    <!-- Custom CSS -->
    <style>
        .card-header {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #0056b3;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
</body>
</html>