<?php
error_reporting(E_ALL); ini_set('display_errors', 1); 
include '../../database/connection.php';
require_once '../../vendor/autoload.php'; // Ensure the autoloader is included

use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

// Query the database to get the latest receipt number
$query = "SELECT ReceiptNumber FROM Payments ORDER BY PaymentDate DESC LIMIT 1";
$result = $conn1->query($query);
$row = $result->fetch_assoc();
$receiptNumber = $row['ReceiptNumber'];

// Initialize the builder
$builder = Builder::create()
    ->writer(new PngWriter())               // Writer (PNG)
    ->writerOptions([])                     // Writer options (optional)
    ->data($receiptNumber)                   // Data for the QR code
    ->encoding(new Encoding('UTF-8'))        // Encoding
    ->errorCorrectionLevel(ErrorCorrectionLevel::HIGH) // High error correction
    ->size(300)                             // QR code size in pixels
    ->margin(10);                           // Margin around the QR code

// Build the QR code
$qrCode = $builder->build();

// Output the QR code directly to the browser as a PNG image
header('Content-Type: ' . $qrCode->getMimeType());
echo $qrCode->getString(); // Directly output the generated QR code image
?>