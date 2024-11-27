<?php
error_reporting(E_ALL); ini_set('display_errors', 1); 
<?php

require_once '../../vendor/autoload.php'; // Ensure the autoloader is included
include '../../database/connection.php'; // Include database connection

use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\Writer\PngWriter;

try {
    // Fetch the latest receipt number from the database
    $query = "SELECT ReceiptNumber FROM Payments ORDER BY PaymentDate DESC LIMIT 1";
    $result = $conn1->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $receiptNumber = $row['ReceiptNumber'];
    } else {
        throw new Exception("No receipt numbers found in the database.");
    }

    // Create the QR code
    $qrCode = new QrCode($receiptNumber); // Use the receipt number from the database
    $qrCode->setSize(300);
    $qrCode->setMargin(10);
    $qrCode->setEncoding('UTF-8');
    $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));

    // Write the QR code as PNG
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    // Output the QR code directly to the browser
    header('Content-Type: ' . $result->getMimeType());
    echo $result->getString();
} catch (\Exception $e) {
    // Log and display the error
    error_log("QR Code Error: " . $e->getMessage());
    echo 'Error generating QR code: ' . $e->getMessage();
} finally {
    $conn1->close(); // Close the database connection
}