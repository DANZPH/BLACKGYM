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
        /* Transparent background for the card */
        .card {
            background-color: rgba(255, 255, 255, 0.8); /* White background with transparency */
            border: none; /* Remove the border if needed */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Optional: Add shadow for better visibility */
        }

        /* Make form inputs transparent */
        .form-control {
            background-color: rgba(255, 255, 255, 0.7); /* Transparent background for form inputs */
            border-color: rgba(0, 0, 0, 0.1); /* Light border color */
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.9); /* Slightly less transparent on focus */
            border-color: #007bff; /* Bootstrap blue color on focus */
        }

        /* Optional: Style the card header */
        .card-header {
            background-color: rgba(0, 0, 0, 0.1); /* Light transparent background */
            color: #333;
            font-weight: bold;
        }
        
        /* Optional: Style the button */
        .btn-primary {
            background-color: rgba(0, 123, 255, 0.8); /* Transparent button background */
            border-color: rgba(0, 123, 255, 0.8); /* Button border with transparency */
        }

        .btn-primary:hover {
            background-color: rgba(0, 123, 255, 1); /* Solid color on hover */
            border-color: rgba(0, 123, 255, 1); /* Solid border on hover */
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