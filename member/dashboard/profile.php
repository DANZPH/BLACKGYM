<?php
session_start();
if (!isset($_SESSION['MemberID'])) {
    header('Location: ../login.php');
    exit();
}
include '../../database/connection.php';

$memberID = $_SESSION['MemberID'];

// Fetch current user data
$sql = "SELECT Users.Username, Users.Email, Members.Address 
        FROM Users 
        JOIN Members ON Users.UserID = Members.UserID 
        WHERE Members.MemberID = ?";
$stmt = $conn1->prepare($sql);
$stmt->bind_param("i", $memberID);
$stmt->execute();
$stmt->bind_result($username, $email, $address);
$stmt->fetch();
$stmt->close();

// Fetch latest receipt number for QR code
$receipt_query = $conn1->prepare("SELECT ReceiptNumber FROM Payments WHERE MemberID = ? ORDER BY PaymentDate DESC LIMIT 1");
$receipt_query->bind_param("i", $memberID);
$receipt_query->execute();
$receipt_result = $receipt_query->get_result();

if ($receipt_result->num_rows > 0) {
    $receipt = $receipt_result->fetch_assoc();
    $latestReceiptNumber = $receipt['ReceiptNumber'];
} else {
    $latestReceiptNumber = null;
}

// Handle form submission (same as before)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['username']);
    $newEmail = trim($_POST['email']);
    $newAddress = trim($_POST['address']);
    $newPassword = trim($_POST['password']);
    
    $errors = [];
    
    // Validate inputs
    if (empty($newUsername) || empty($newEmail)) {
        $errors[] = "Username and Email are required.";
    }
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (!empty($newPassword) && strlen($newPassword) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    // If no errors, update the database (same as before)
    if (empty($errors)) {
        $updateSql = "UPDATE Users 
                      JOIN Members ON Users.UserID = Members.UserID 
                      SET Users.Username = ?, Users.Email = ?, Members.Address = ?";
        $params = [$newUsername, $newEmail, $newAddress];
        $types = "sss";
        
        if (!empty($newPassword)) {
            $updateSql .= ", Users.Password = ?";
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $params[] = $hashedPassword;
            $types .= "s";
        }

        $updateSql .= " WHERE Members.MemberID = ?";
        $params[] = $memberID;
        $types .= "i";

        $updateStmt = $conn1->prepare($updateSql);
        $updateStmt->bind_param($types, ...$params);
        
        if ($updateStmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Profile updated successfully',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'edit_profile.php';
                });
            </script>";
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }

        $updateStmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="includes/styles.css">
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <title>Edit Profile - BLACKGYM</title>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <!-- CONTENT -->
    <section id="content">
        <?php include 'includes/navbar.php'; ?>
        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Edit Profile</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Edit Profile</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-pic">
                        <img src="https://via.placeholder.com/100" alt="Profile Picture">
                    </div>
                    <h2><?php echo htmlspecialchars($username); ?></h2>
                    <p class="email"><?php echo htmlspecialchars($email); ?></p>
                </div>

                <div class="profile-details">
                  <div class="form-group"><div id="qrcode" style="width: 180px; height: 180px; margin: 10px auto;"></div>
                        </div>
                    <h3>Profile Information</h3>
                    <form method="POST" action="../action/edit_profile.php">
                        <div class="form-group">
                           <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>">
                        </div>

                        <div class="form-group">
                            <label for="password">New Password <small>(optional)</small></label>
                            <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-save">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script src="includes/script.js"></script>
    <script type="text/javascript">
        // Generate QR Code for Receipt Number
        var receiptNumber = "<?php echo $latestReceiptNumber; ?>";
        if (receiptNumber) {
            var qrcode = new QRCode(document.getElementById("qrcode"), {
                text: receiptNumber,  // Set the text to the receipt number
                width: 180,           // Width of the QR code
                height: 180,          // Height of the QR code
                colorDark: "#000000", // Dark color
                colorLight: "#ffffff", // Light color
                correctLevel: QRCode.CorrectLevel.H // Error correction level
            });
        }
    </script>
</body>
</html>
