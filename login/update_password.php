<?php
// Assume connection is established with the database as above

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"]) && isset($_POST["token"]) && isset($_POST["password"])) {
    $email = $_POST["email"];
    $token = $_POST["token"];
    $password = $_POST["password"];

    // Hash the password before storing it
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Update the user's password in the database
    $stmt = $conn->prepare("UPDATE Users SET Password = ? WHERE Email = ? AND ResetToken = ?");
    $stmt->bind_param("sss", $hashedPassword, $email, $token);
    if ($stmt->execute()) {
        // Redirect back to the password reset page with a success status
        header("Location: reset_password.php?email=$email&token=$token&status=success");
    } else {
        // Redirect back to the password reset page with an error status
        header("Location: reset_password.php?email=$email&token=$token&status=error");
    }
    $stmt->close();
}

$conn->close();
?>