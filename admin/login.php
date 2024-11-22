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
        /* Make card with white text and white grid lines */
        .card {
            background-color: #333; /* Dark background for the card */
            border: 2px solid #fff; /* White border around the card */
            box-shadow: none; /* No shadow */
            color: #fff; /* White text color */
        }

        /* Form input fields with white text and white grid lines */
        .form-control {
            background-color: #444; /* Dark background for form inputs */
            border: 2px solid #fff; /* White grid-like border */
            color: #fff; /* White text color */
            border-radius: 4px; /* Slightly rounded corners */
        }

        .form-control:focus {
            border-color: #fff; /* White border on focus */
            background-color: #555; /* Darker background on focus */
            box-shadow: none; /* Remove focus shadow */
        }

        /* Card header with white text and white border */
        .card-header {
            background-color: #333; /* Dark background */
            border-bottom: 2px solid #fff; /* White border at the bottom */
            font-weight: bold;
            color: #fff; /* White text color */
        }

        /* Button with white borders and text */
        .btn-primary {
            background-color: transparent; /* Transparent background */
            border: 2px solid #fff; /* White border */
            color: #fff; /* White text */
        }

        .btn-primary:hover {
            background-color: #fff; /* White background on hover */
            color: #333; /* Dark text on hover */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
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
                                <a href="../login/forgot_password.php" style="color: #fff;">Forgot password</a>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Login</button>
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

    <!-- Check if there's an error message in the session and display it using SweetAlert -->
    <script>
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['error']); // Clear the error message after displaying it ?>
        <?php endif; ?>
    </script>
</body>
</html>