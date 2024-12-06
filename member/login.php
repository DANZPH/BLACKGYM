<?php
session_start();
include '../../database/connection.php';
if (isset($_SESSION['MemberID'])) {
    header('Location: dashboard/index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'dashboard/includes/head.php'; ?>
<link rel="stylesheet" href="dashboard/includes/Loginform.css">
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <img class="card-header h2 text-center" src="../img/logo.jpg" alt="Logo">
                    <div class="card-body">
                        <form action="action/login_process.php" method="POST">
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                                <a href="../login/forgot_password.php">Forgot password?</a>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                        <div class="mt-3 text-center">
                            <!-- Sign-up button -->
                            <a href="../login/register.php" class="btn btn-outline-secondary">Click Here To Enroll</a>
                        </div>
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