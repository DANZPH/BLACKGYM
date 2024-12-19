<?php
$visitor_ip = $_SERVER['REMOTE_ADDR'];
$log_file = '../visitor_logs.txt';
if (!file_exists($log_file) || filesize($log_file) == 0) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Server Error: Log file is empty or missing.";
    exit();
}
$log_contents = file_get_contents($log_file);
$logs = explode("\n", trim($log_contents)); 
$ip_found = false;
foreach ($logs as $log_entry) {
    $log_data = json_decode($log_entry, true);
    if ($log_data && isset($log_data['ip']) && $log_data['ip'] === $visitor_ip) {
        $ip_found = true;
        break;
    } 
    elseif (strpos($log_entry, $visitor_ip) !== false) {
        $ip_found = true;
        break;
    }
}
if (!$ip_found) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Server Error: Your Not verify.";
    exit();
}
echo "";
session_start();
include '../../database/connection.php';
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