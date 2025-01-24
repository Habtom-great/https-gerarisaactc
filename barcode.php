<?php
// Ensure the autoload file is included to load the library
require __DIR__ . '/vendor/autoload.php'; 

// Import the Barcode Generator class
use Picqer\Barcode\BarcodeGeneratorPNG;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];

    // Generate barcode for the payment amount
    $generator = new BarcodeGeneratorPNG();
    $barcode = $generator->getBarcode($amount, $generator::TYPE_CODE_128);

    // Display barcode image
    echo "<h2>Payment Barcode for Amount: $" . htmlspecialchars($amount) . "</h2>";
    echo '<img src="data:image/png;base64,' . base64_encode($barcode) . '" alt="Barcode">';
    echo '<br><a href="index.html">Go Back</a>';
} else {
    echo 'Invalid Request';
}

