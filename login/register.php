<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="../css/login.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <form action="action/register_process.php" method="POST" id="registerForm">
            <h1>User Registration</h1>

            <div class="input-box">
                <input type="text" id="username" name="username" placeholder="Username" required>
                <i class='bx bxs-user'></i>
            </div>

            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <i class='bx bx-envelope'></i>
            </div>

            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <!-- Gender, Age, and Address Fields -->
            <div class="input-box">
                <select id="gender" name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                <i class='bx bx-gender'></i>
            </div>

            <div class="input-box">
                <input type="number" id="age" name="age" placeholder="Age" required>
                <i class='bx bx-calendar'></i>
            </div>

            <div class="input-box">
                <input type="text" id="address" name="address" placeholder="Address" required>
                <i class='bx bx-map'></i>
            </div>

            <!-- Membership Option Fields -->
            <div class="input-box">
                <select id="membershipType" name="membershipType" required>
                    <option value="SessionPrice">Pay Per Session</option>
                    <option value="Subscription">Subscription</option>
                </select>
            </div>

            <div class="input-box" id="subscriptionOptions" style="display: none;">
                <input type="number" id="subscriptionMonths" name="subscriptionMonths" placeholder="Choose Number of Months" min="1" max="12">
            </div>

            <div class="input-box" id="sessionPriceOptions" style="display: none;">
                <input type="number" id="sessionPrice" name="sessionPrice" placeholder="Price per Session" value="50" min="0">
            </div>

            <button type="submit" class="btn">Register</button>

            <div class="register-link">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>

    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Toggle Subscription and SessionPrice options based on membership type
            document.getElementById('membershipType').addEventListener('change', function() {
                var membershipType = this.value;
                if (membershipType === 'Subscription') {
                    document.getElementById('subscriptionOptions').style.display = 'block';
                    document.getElementById('sessionPriceOptions').style.display = 'none';
                } else {
                    document.getElementById('sessionPriceOptions').style.display = 'block';
                    document.getElementById('subscriptionOptions').style.display = 'none';
                }
            });

            // Submit form via AJAX
            document.getElementById('registerForm').addEventListener('submit', function(e) {
                e.preventDefault();

                // Generate a random OTP for the user
                var otp = Math.floor(100000 + Math.random() * 900000);
                var otpExpiration = new Date(new Date().getTime() + 15 * 60000).toISOString();  // OTP expires in 15 minutes
                
                var formData = new FormData(this);
                formData.append("otp", otp);
                formData.append("otpExpiration", otpExpiration);

                fetch('send_otp.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(response => {
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
                                window.location.href = 'otp.php?email=' + document.getElementById('email').value;
                            }
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to send OTP. Please try again later.',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                });
            });
        });
    </script>
</body>
</html>
