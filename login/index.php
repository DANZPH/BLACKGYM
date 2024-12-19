<?php
session_start();
$message = ''; // Initialize message variable
$alertType = ''; // Initialize alert type variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username/email and password are set
    if (isset($_POST["login"]) && isset($_POST["password"])) {
        $login = $_POST["login"]; // This can be either username or email
        $password = $_POST["password"];

        // Database credentials
        $host = "sql104.infinityfree.com"; // Change this to your database host
        $dbname = "if0_36048499_db_user"; // Change this to your database name
        $usernameDB = "if0_36048499"; // Change this to your database username
        $passwordDB = "LokK4Hhvygq"; // Change this to your database password

        // Database connection
        $conn = new mysqli($host, $usernameDB, $passwordDB, $dbname);

        if ($conn->connect_error) {
            $message = 'Connection failed: ' . $conn->connect_error;
            $alertType = 'error';
        } else {
            // Check if user exists by username or email
            $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ? OR Email = ?");
            $stmt->bind_param("ss", $login, $login); // Bind the same variable for both placeholders
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                // Verify password
                if (password_verify($password, $user['Password'])) {
                    // Password is correct, redirect to dashboard
                    $_SESSION['username'] = $user['Username']; // Store username in session
                    $message = 'Login successful! Redirecting to dashboard...';
                    $alertType = 'success';
                    echo '<script>setTimeout(function(){ window.location.href = "../dashboard/dashboard.php"; }, 2000);</script>'; // Redirect after 2 seconds
                } else {
                    $message = 'Invalid password.';
                    $alertType = 'error';
                }
            } else {
                $message = 'User not found.';
                $alertType = 'error';
            }
            $conn->close();
        }
    } else {
        $message = 'Login (username or email) and password are required.';
        $alertType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">User Login</div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="login">Username or Email:</label>
                                <input type="text" id="login" name="login" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">Login</button>
                            </div>
                        </form>
                        <div class="text-center">
                            <a href="register.php" class="btn btn-secondary btn-block">Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Check if there's a message and display it using SweetAlert
        <?php if ($message): ?>
            Swal.fire({
                icon: '<?php echo $alertType; ?>',
                title: '<?php echo $alertType === 'success' ? 'Success' : 'Error'; ?>',
                text: '<?php echo $message; ?>',
                confirmButtonText: 'Okay'
            });
        <?php endif; ?>
    </script>
</body>
</html>