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
  <?php include '../includes/head.php'; ?>
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
            background-color: #000; /* Black background */
            color: #fff; /* White text */
        }
        .card {
            background-color: #333; /* Dark gray card background */
            color: #fff; /* White text for card */
            border: none; /* Remove border */
        }
        .card-header {
            background-color: #000; /* Black header */
            border-bottom: 1px solid #fff; /* White border */
        }
        .btn-primary {
            background-color: #fff; /* White button background */
            color: #000; /* Black text on button */
            border: 1px solid #fff; /* White border */
        }
        .btn-primary:hover {
            background-color: #444; /* Darker gray on hover */
            color: #fff; /* White text on hover */
        }
        a {
            color: #aaa; /* Light gray links */
        }
        a:hover {
            color: #fff; /* White links on hover */
        }
        input.form-control {
            background-color: #222; /* Dark gray input background */
            color: #fff; /* White text for input */
            border: 1px solid #444; /* Light gray border */
        }
        input.form-control::placeholder {
            color: #777; /* Gray placeholder text */
        }
        input.form-control:focus {
            background-color: #333; /* Slightly lighter gray on focus */
            border-color: #fff; /* White border on focus */
            color: #fff; /* Keep text white on focus */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header h2 text-center">Admin Login</div>
                    <div class="card-body">
                        <form action="action/login_process.php" method="POST">
                            <div class="form-group">
                                <label for="email">Username or Email:</label>
                                <input type="text" id="email" name="email" class="form-control" placeholder="Username or email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="*******" required>
                                <a href="../login/forgot_password.php">Forgot password?</a>
                            </div>
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary btn-block">Login</button>
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

    <!-- SweetAlert -->
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