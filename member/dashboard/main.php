<?php
session_start();
if (!isset($_SESSION['MemberID'])) {
    header('Location: login.php');
    exit();
}
include '../../database/connection.php';
$memberID = $_SESSION['MemberID'];

// Fetch the latest receipt number from the database
$sql = "SELECT ReceiptNumber FROM Payments WHERE MemberID = ? ORDER BY PaymentDate DESC LIMIT 1";
$stmt = $conn1->prepare($sql);
$stmt->bind_param("d", $memberID);
$stmt->execute();
$stmt->bind_result($latestReceiptNumber);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script src="script.js"></script> <!-- Your external JS file -->
</head>
<body>
    <!-- QR Code Container -->
    <div id="qrcode"></div>

    <script type="text/javascript">
        // Passing the receipt number to JavaScript via a global variable
        window.latestReceiptNumber = "<?php echo $latestReceiptNumber; ?>";
    </script>
</body>
</html>