<?php
require_once '../../vendor/autoload.php'; // Ensure the autoloader is included
include '../../database/connection.php'; // Include the database connection

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

    // Initialize the QR code builder with the receipt number as data
    $builder = new Builder(
        writer: new PngWriter(),
        writerOptions: [],
        validateResult: false,
        data: $receiptNumber, // Set the QR code data to the latest receipt number
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::High, // High error correction
        size: 300, // Size of the QR code (pixels)
        margin: 10 // Margin around the QR code
    );

    // Build the QR code
    $result = $builder->build();

    // Output the QR code directly to the browser as a PNG image
    header('Content-Type: ' . $result->getMimeType());
    echo $result->getString(); // Directly output the generated QR code image
} catch (\Exception $e) {
    // Catch and display any errors that occur during QR code generation
    echo 'Error generating QR code: ' . $e->getMessage();
} finally {
    $conn1->close(); // Ensure the database connection is closed
}