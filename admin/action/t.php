<?php
require_once '../../vendor/autoload.php'; // Ensure the autoloader is included
include '../../database/connection.php'; // Include the database connection

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

header('Content-Type: image/png'); // Ensure proper header for image output

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

    // Build the QR code
    $result = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($receiptNumber)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::HIGH)
        ->size(300)
        ->margin(10)
        ->build();

    // Output the QR code image
    echo $result->getString();
} catch (\Exception $e) {
    // Log the error and output a placeholder image
    error_log("QR Code Generation Error: " . $e->getMessage());

    // Create a blank image with an error message
    $im = imagecreate(300, 300);
    $bgColor = imagecolorallocate($im, 255, 255, 255); // White background
    $textColor = imagecolorallocate($im, 0, 0, 0); // Black text
    imagestring($im, 5, 10, 140, 'Error: ' . $e->getMessage(), $textColor);
    imagepng($im);
    imagedestroy($im);
} finally {
    $conn1->close(); // Ensure the database connection is closed
}