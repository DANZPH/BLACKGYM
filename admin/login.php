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
        /* Make card with full opacity but show grid lines */
        .card {
            background-color: #fff; /* Full white background */
            border: 2px solid #ddd; /* Light gray border around the card (grid lines) */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Optional: Add shadow for better visibility */
        }

        /* Form input fields with full opacity and grid lines */
        .form-control {
            background-color: #fff; /* Full white background */
            border: 2px solid #ddd; /* Light gray border for grid effect */
            border-radius: 4px; /* Slightly rounded corners */
        }

        .form-control:focus {
            border-color: #007bff; /* Blue border on focus */
            box-shadow: none; /* Remove focus shadow */
        }

        /* Optional: Style the card header */
        .card-header {
            background-color: #fff; /* White background */
            border-bottom: 2px solid #ddd; /* Light gray border at the bottom */
            font-weight: bold;
            color: #333;
        }

        /* Optional: Style the button with full opacity and grid lines */
        .btn-primary {
            background-color: #007bff; /* Solid blue background */
            border: 2px solid #007bff; /* Blue border */
            color: white; /* White text */
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Darker blue background on hover */
            border-color: #0056b3; /* Darker blue border on hover */
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
                                <a href="../login/forgot_password.php">Forgot password</a>
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