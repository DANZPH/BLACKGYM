

// Check if receiptNumber is available (from the PHP code passed to window object)
if (window.receiptNumber) {
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        text: window.receiptNumber,  // Set the text to the receipt number
        width: 128,                  // Width of the QR code
        height: 128,                 // Height of the QR code
        colorDark: "#000000",        // Dark color
        colorLight: "#ffffff",       // Light color
        correctLevel: QRCode.CorrectLevel.H // Error correction level
    });
} else {
    document.getElementById("qrcode").innerHTML = "No receipt number found.";
}

