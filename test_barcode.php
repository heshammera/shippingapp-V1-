<?php
require __DIR__ . '/vendor/autoload.php';
try {
    $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
    $svg = $generator->getBarcode('13709', $generator::TYPE_CODE_128, 2, 30);
    echo "SVG Generated (len: " . strlen($svg) . ")\n";
    echo substr($svg, 0, 50) . "...\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
