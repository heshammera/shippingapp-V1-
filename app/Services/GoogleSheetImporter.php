<?php

namespace App\Services;

use Google_Client;
use Google_Service_Sheets;
use App\Models\Shipment;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class GoogleSheetImporter
{
    public function importOrders($spreadsheetId = null)
    {
        // 1. Load Settings
        $sheetId = $spreadsheetId ?: Setting::getValue('google_sheet_id');
        $tabName = Setting::getValue('google_sheet_tab_name', 'Sheet1');
        $jsonAuth = Setting::getValue('google_service_account_json');

        if (empty($sheetId)) {
            Log::error("Google Sheet ID is missing in settings.");
            return; // Or throw exception
        }

        if (empty($jsonAuth)) {
            Log::error("Google Service Account JSON is missing in settings.");
            return;
        }

        // 2. Setup Client
        $client = new Google_Client();
        $client->setApplicationName('Naseeg Orders Sync');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS_READONLY]);
        
        // Decode JSON content directly
        $authConfig = json_decode($jsonAuth, true);
        if (!$authConfig) {
             Log::error("Invalid JSON credentials.");
             return;
        }
        $client->setAuthConfig($authConfig);

        $service = new Google_Service_Sheets($client);

        // 3. Dynamic Range
        $range = "{$tabName}!A2:Z";

        $response = $service->spreadsheets_values->get($sheetId, $range);
        $rows = $response->getValues();

        $columns = [
            'tracking_number' => (int) Setting::getValue('column_index_tracking_number', 10),
            'customer_name'   => (int) Setting::getValue('column_index_customer_name', 0),
            'customer_phone'  => (int) Setting::getValue('column_index_customer_phone', 1),
            'alternate_phone' => (int) Setting::getValue('column_index_alternate_phone', 21),
            'governorate'     => (int) Setting::getValue('column_index_governorate', 14),
            'customer_address'=> (int) Setting::getValue('column_index_customer_address', 3),
            'unit_price'      => (int) Setting::getValue('column_index_unit_price', 5),
            'total_amount'    => (int) Setting::getValue('column_index_total_amount', 6),
            'product_name'    => (int) Setting::getValue('column_index_product_name', 23),
            'color_type'      => (int) Setting::getValue('column_index_color_type', 9),
        ];

        foreach ($rows as $row) {
            $customerName   = $row[$columns['customer_name']]     ?? '';
            $phone          = $row[$columns['customer_phone']]    ?? '';
            $alternatePhone = $row[$columns['alternate_phone']] ?? '';
            $address        = $row[$columns['customer_address']]  ?? '';
            $unitPriceRaw   = $row[$columns['unit_price']]        ?? '';
            $totalRaw       = $row[$columns['total_amount']]      ?? '';
            $productRaw     = $row[$columns['product_name']]      ?? '';
            $colorTypeRaw   = $row[$columns['color_type']]        ?? '';
            $governorate    = $row[$columns['governorate']]       ?? '';
            // Handle tracking number column properly
            $trackingNumber = isset($columns['tracking_number']) && isset($row[$columns['tracking_number']]) 
                ? $row[$columns['tracking_number']] 
                : uniqid('trk_');

            // Fallback for quantity if col 4 is hardcoded in original script
            $quantityRaw    = $row[4] ?? ''; 

            $quantities = preg_split('/\|\||\r\n|\n|\r|,/', $quantityRaw);
            $quantities = array_filter($quantities, fn($v) => is_numeric(trim($v)));
            $quantities = array_values($quantities);

            $productNameLines = preg_split('/\|\||\r\n|\n|\r|,/', $productRaw);
            $productNameLines = array_filter($productNameLines, fn($v) => !empty($v) && strtolower($v) !== 'غير محدد');
            $productNameLines = array_values($productNameLines);

            $unitPrices = preg_split('/\|\||\r\n|\n|\r|,/', $unitPriceRaw);
            $unitPrices = array_filter($unitPrices, fn($v) => is_numeric($v));
            $unitPrices = array_values($unitPrices);

            $colorAndSizeLines = $this->extractColorAndSize($colorTypeRaw);
            $colorAndSizeLines = array_filter($colorAndSizeLines, fn($v) => !empty($v['color']) || !empty($v['size']));
            $colorAndSizeLines = array_values($colorAndSizeLines);

            $count = max(count($productNameLines), count($unitPrices), count($colorAndSizeLines));

            if ($count === 0) {
                // Log::info("❌ لا يوجد بيانات كافية في السطر، تم التجاوز.");
                continue;
            }

            $totalAmount = is_numeric(str_replace(',', '', $totalRaw))
                ? floatval(str_replace(',', '', $totalRaw))
                : array_sum(array_map('floatval', $unitPrices)) + 60;

            $existing = Shipment::where('tracking_number', $trackingNumber)->first();
            if ($existing) {
                // Log::info("🚫 الشحنة برقم تتبع $trackingNumber موجودة بالفعل، تم التجاوز.");
                continue;
            }

            $shipment = Shipment::create([
                'tracking_number'      => $trackingNumber,
                'customer_name'        => $customerName,
                'customer_phone'       => $phone,
                'alternate_phone'      => $alternatePhone,
                'customer_address'     => $address,
                'governorate'          => $governorate,
                'shipping_price'       => 60,
                'total_amount'         => $totalAmount,
                'status_id'            => 37,
                'shipping_company_id'  => 6,
            ]);

            // Clean old products if any (redundant for new shipment but safe)
            $shipment->products()->detach();

            for ($i = 0; $i < $count; $i++) {
                $productName = $productNameLines[$i] ?? end($productNameLines) ?? 'غير محدد';
                $unitPrice = $unitPrices[$i] ?? end($unitPrices) ?? 0;
                $color = $colorAndSizeLines[$i]['color'] ?? end($colorAndSizeLines)['color'] ?? 'غير محدد';
                $size = $colorAndSizeLines[$i]['size'] ?? end($colorAndSizeLines)['size'] ?? 'غير محدد';
                $quantity = isset($quantities[$i]) ? intval($quantities[$i]) : 1;

                if (empty($productName) || strtolower($productName) === 'غير محدد') {
                    continue;
                }

                $product = Product::firstOrCreate(
                    ['name' => $productName],
                    ['cost_price' => 100, 'price' => floatval($unitPrice)]
                );

                // Update product colors/sizes metadata
                $currentColors = array_filter(array_map('trim', explode(',', $product->colors ?? '')));
                if ($color !== 'غير محدد' && $color !== '' && !in_array($color, $currentColors)) {
                    $currentColors[] = $color;
                    $product->colors = implode(', ', array_unique($currentColors));
                }

                $currentSizes = array_filter(array_map('trim', explode(',', $product->sizes ?? '')));
                if ($size !== 'غير محدد' && $size !== '' && !in_array($size, $currentSizes)) {
                    $currentSizes[] = $size;
                    $product->sizes = implode(', ', array_unique($currentSizes));
                }

                $product->save();

                $shipment->products()->attach($product->id, [
                    'color'    => $color,
                    'size'     => $size,
                    'quantity' => $quantity,
                    'price'    => $unitPrice,
                ]);
            }
        }
    }

    private function extractColorAndSize(string $rawText): array
    {
        $results = [];

        $lines = preg_split("/\r\n|\n|\r/", $rawText);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = preg_split('/\s*\|\|\s*/', $line);

            $color = 'غير محدد';
            $size = 'غير محدد';

            foreach ($parts as $part) {
                if (preg_match('/اللون\s*[-–:]?\s*(.+)/u', $part, $colorMatch)) {
                    $color = trim($colorMatch[1]);
                }

                if (preg_match('/(?:نوع البيزك|نوع|بيزك|مقاس)\s*[-–:]?\s*(.+)/u', $part, $sizeMatch)) {
                    $size = trim($sizeMatch[1]);
                }
            }

            $size = preg_replace('/^البيزك\s*/u', '', $size);
            $size = preg_replace('/^المقاس\s*-\s*/u', '', $size); 
            $size = preg_replace('/^\s*-\s*/', '', $size);
            $size = trim($size);

            if ($color === '' || strtolower($color) === 'غير محدد') {
                continue;
            }

            $results[] = [
                'color' => $color,
                'size'  => $size ?: 'غير محدد',
            ];
        }

        return $results;
    }
}
