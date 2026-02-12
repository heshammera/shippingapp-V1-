<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Milon\Barcode\DNS1D;
use Illuminate\Support\Str;

class BarcodeService
{
    /**
     * Generate a globally unique barcode.
     * 
     * @param string $prefix Optional prefix (e.g. 'P' for product, though numeric is better for scanning)
     * @param int $length Total length of the barcode
     * @return string
     */
    public function generateUniqueBarcode(int $length = 12): string
    {
        $barcode = $this->generateRandomString($length);

        // Ensure uniqueness
        while ($this->exists($barcode)) {
            $barcode = $this->generateRandomString($length);
        }

        return $barcode;
    }

    /**
     * Check if barcode exists in products or variants.
     */
    protected function exists(string $barcode): bool
    {
        return Product::where('barcode', $barcode)->exists() 
            || ProductVariant::where('barcode', $barcode)->exists();
    }

    /**
     * Generate random numeric string.
     */
    protected function generateRandomString(int $length): string
    {
        // Use numbers only for broader scanner compatibility (Code 128 / EAN)
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return (string) mt_rand($min, $max);
    }

    /**
     * Get Barcode as Base64 Image (PNG) for printing/PDF.
     */
    public function getBarcodeImage(string $code, string $type = 'C128', int $apiWidth = 2, int $apiHeight = 30)
    {
        return 'data:image/png;base64,' . (new DNS1D)->getBarcodePNG($code, $type, $apiWidth, $apiHeight);
    }

    /**
     * Get Barcode as SVG (Better for HTML display).
     */
    public function getBarcodeHtml(string $code, string $type = 'C128', int $apiWidth = 2, int $apiHeight = 30)
    {
        return (new DNS1D)->getBarcodeSVG($code, $type, $apiWidth, $apiHeight);
    }
}
