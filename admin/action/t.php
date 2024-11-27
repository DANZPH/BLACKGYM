<?php
error_reporting(E_ALL); ini_set('display_errors', 1); 
require_once '../../vendor/autoload.php'; // Ensure the autoloader is included
include '../../database/connection.php'; // Include database connection

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
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
    $result = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($receiptNumber) // Use the receipt number from the database
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::HIGH)
        ->size(300)
        ->margin(10)
        ->build();

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