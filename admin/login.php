<?php
// login.php
session_start();
include '../../database/connection.php';

if (isset($_SESSION['AdminID'])) {
    header('Location: dashboard/index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="dashboard/includes/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #2c3e50; /* Dark background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            width: 100%;
            max-width: 400px; /* Smaller width for the form */
            background-color: #333; /* Dark background for the card */
            border: none; /* Remove default border */
            border-radius: 10px; /* Rounded corners */
            color: #fff; /* White text */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5); /* Larger shadow for better visibility */
        }

        .card-header {
            background-color: #444; /* Slightly lighter background for the header */
            font-size: 1.5rem; /* Larger font size */
            font-weight: bold;
            text-align: center; /* Center-align the header */
            border-bottom: none; /* Remove bottom border */
        }

        .form-control {
            background-color: #444; /* Dark background for inputs */
            border: 1px solid #fff; /* White border for the grid effect */
            color: #fff; /* White text color */
            border-radius: 4px; /* Rounded corners */
        }

        .form-control:focus {
            background-color: #555; /* Slightly lighter background on focus */
            border-color: #007bff; /* Blue border on focus */
            box-shadow: none; /* Remove default focus shadow */
        }

        .btn-primary {
            background-color: #007bff; /* Blue button background */
            border: none; /* Remove border */
            color: #fff; /* White text */
            width: 100%; /* Full-width button */
            border-radius: 4px; /* Rounded corners */
            padding: 10px; /* Add padding */
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .forgot-password {
            display: block; /* Make it a block element */
            text-align: right; /* Align to the right */
            font-size: 0.9rem; /* Smaller font size */
            color: #fff; /* White text */
            text-decoration: none; /* Remove underline */
            margin-top: -10px; /* Adjust spacing */
        }

        .forgot-password:hover {
            text-decoration: underline; /* Add underline on hover */
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">Admin Login</div>
        <div class="card-body">
            <form action="action/login_process.php" method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>

                </div>
                                    <a href="../login/forgot_password.php" class="forgot-password">Forgot password?</a>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- SweetAlert for error handling -->
    <script>
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>
</html>