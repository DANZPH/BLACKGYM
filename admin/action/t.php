<?php

require_once '../../vendor/autoload.php'; // Ensure the autoloader is included

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;


    // Initialize the builder (no logo or label)
    $builder = new Builder(
        writer: new PngWriter(),
        writerOptions: [],
        validateResult: false,
        data: 'Custom QR code contents', // Content of the QR code (this can be any string)
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::High, // High error correction
        size: 300, // Size of the QR code (pixels)
        margin: 10 // Margin around the QR code
    );

    // Build the QR code
    $result = $builder->build();

    // Output the QR code directly to the browser as a PNG image
    header('Content-Type: '.$result->getMimeType());
    echo $result->getString(); // Directly output the generated QR code image
    
?>