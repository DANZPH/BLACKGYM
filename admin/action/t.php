<?php
require_once '../../vendor/autoload.php'; // Ensure the autoloader is included
include '../../database/connection.php'; // Include the database connection

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

header('Content-Type: image/png'); // Ensure the header is set to output an image

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

    // Output the QR code directly to the browser
    echo $result->getString(); // Output the generated QR code as a PNG image
} catch (\Exception $e) {
    // Output a blank image with error text in case of failure
    $im = imagecreate(300, 300); // Create a blank image
    $bgColor = imagecolorallocate($im, 255, 255, 255); // White background
    $textColor = imagecolorallocate($im, 0, 0, 0); // Black text
    imagestring($im, 5, 50, 140, 'Error: ' . $e->getMessage(), $textColor);
    imagepng($im); // Output the blank image
    imagedestroy($im);
} finally {
    $conn1->close(); // Ensure the database connection is closed
}