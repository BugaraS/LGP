<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Scan QR/Barcode</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #f9f9f9; }
        #reader { width: 400px; margin: 40px auto; }
        h2 { color: #003366; }
    </style>
</head>
<body>
    <h2>📷 Scan QR Code or Barcode</h2>
    <div id="reader"></div>
    <p id="result">Waiting for scan...</p>

    <script>
    function onScanSuccess(decodedText, decodedResult) {
        // ✅ Redirect to profile page
        document.getElementById("result").innerHTML = "Scanned: " + decodedText;
        window.location.href = decodedText; 
    }

    function onScanFailure(error) {
        // optional: console.log(`Scan error: ${error}`);
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>
</body>
</html>
