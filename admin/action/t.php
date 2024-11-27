<?php
require_once '../../vendor/autoload.php'; // Ensure the autoloader is included
include '../../database/connection.php'; // Include the database connection

use Endroid\QrCode\QrCode;
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

    // Generate the QR code
    $qrCode = QrCode::create($receiptNumber)
        ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
        ->setErrorCorrectionLevel(new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh())
        ->setSize(300)
        ->setMargin(10);

    // Write the QR code to a PNG image
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    // Output the QR code directly to the browser as a PNG image
    header('Content-Type: ' . $result->getMimeType());
    echo $result->getString();
} catch (\Exception $e) {
    // Catch and display any errors that occur during QR code generation
    echo 'Error generating QR code: ' . $e->getMessage();
} finally {
    $conn1->close(); // Ensure the database connection is closed
}