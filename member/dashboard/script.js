<script type="text/javascript">
    // Generate the QR code if there's a receipt number
    var receiptNumber = "<?php echo $latestReceiptNumber; ?>";
    if (receiptNumber) {
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: receiptNumber,  // Set the text to the receipt number
            width: 128,           // Width of the QR code
            height: 128,          // Height of the QR code
            colorDark : "#000000", // Dark color
            colorLight : "#ffffff", // Light color
            correctLevel : QRCode.CorrectLevel.H // Error correction level
        });
    }
</script>