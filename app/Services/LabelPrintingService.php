<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Str;

class LabelPrintingService
{
    /**
     * Generate ZPL code for a product label.
     * Dimensions: 2 inch x 1 inch (approx 400x200 dots at 203 DPI)
     * 
     * @param ProductVariant $variant
     * @return string ZPL Code
     */
    public function generateZpl(ProductVariant $variant): string
    {
        $name = Str::limit($variant->full_name, 25);
        $price = number_format($variant->product->price, 2) . ' SAR';
        $barcode = $variant->barcode;

        // ^XA: Start Format
        // ^FO: Field Origin (x,y)
        // ^ADN: Font (Height, Width)
        // ^BC: Code 128 Barcode
        // ^XZ: End Format

        return "^XA
^FO50,20^ADN,36,20^FD{$name}^FS
^FO50,60^ADN,24,12^FDPrice: {$price}^FS
^FO50,100^BY2,2,60^BCN,60,N,N,N^FD{$barcode}^FS
^XZ";
    }

    /**
     * Print label (Simulated).
     * In a real app, this would send raw data to a network printer IP (port 9100).
     */
    public function printLabel(ProductVariant $variant, string $printerIp)
    {
        $zpl = $this->generateZpl($variant);
        
        // Example: Send to printer
        try {
            $fp = fsockopen($printerIp, 9100, $errno, $errstr, 10);
            if ($fp) {
                fwrite($fp, $zpl);
                fclose($fp);
                return true;
            }
        } catch (\Exception $e) {
            // Log error
        }
        
        return false;
    }
}
