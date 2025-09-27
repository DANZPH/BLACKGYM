<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/BLACKGYM/database/connection.php';
if (isset($_SESSION['MemberID'])) {
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
    <link rel="stylesheet" href="../css/login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="wrapper">
        <form action="action/login_process.php" method="POST">
            <h1>Login</h1>

            <div class="input-box">
                <input type="text" id="email" name="email" placeholder="Username or Email" required>
                <i class='bx bxs-user'></i>
            </div>

            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <div class="remember-forgot">
                <label><input type="checkbox" name="remember"> Remember Me</label>
                <a href="../login/forgot_password.php">Forgot Password?</a>
            </div>

            <button type="submit" class="btn">Login</button>

            <div class="register-link">
                <p>Don't have an account? <a href="../login/register.php">Register</a></p>
            </div>
        </form>
    </div>

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