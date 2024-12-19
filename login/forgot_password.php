<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="wrapper">
        <form action="reset_password.php" method="POST">
            <h1>Forgot Your Password?</h1>

            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                <i class='bx bxs-envelope'></i>
            </div>

            <button type="submit" class="btn">Reset Password</button>

            <div class="footer">
                <p>Remembered your password? <a href="../member/login.php">Login</a></p>
            </div>
        </form>
    </div>
</body>

</html>
