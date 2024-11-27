<?php
include '../../database/connection.php';
require_once '../../vendor/autoload.php'; // Ensure the autoloader is included

use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;

// Ensure the database connection is valid
if (!$conn1) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to fetch the latest receipt number
$query = "SELECT ReceiptNumber FROM Payments ORDER BY PaymentDate DESC LIMIT 1";
$result = $conn1->query($query);

if (!$result) {
    die("Query failed: " . $conn1->error);
}

// Fetch the result
$row = $result->fetch_assoc();
if (!$row) {
    die("No receipt found.");
}

$receiptNumber = $row['ReceiptNumber'];

// Create a new QR code instance
$qrCode = new QrCode($receiptNumber);
$qrCode->setSize(300); // Set the size of the QR code
$qrCode->setMargin(10); // Set the margin around the QR code
$qrCode->setEncoding(new Encoding('UTF-8')); // Set the encoding
$qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH); // Set error correction level

// Create a writer instance
$writer = new PngWriter();

// Output the QR code directly to the browser as a PNG image
header('Content-Type: ' . $writer->getMimeType());
echo $writer->writeString($qrCode); // Directly output the generated QR code image
?>