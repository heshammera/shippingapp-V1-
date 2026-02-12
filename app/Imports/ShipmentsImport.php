<?php

namespace App\Imports;

use App\Models\Shipment;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ShipmentsImport implements ToCollection, WithHeadingRow
{
    private $shippingCompanyId;

    public function __construct($shippingCompanyId)
    {
        $this->shippingCompanyId = $shippingCompanyId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['tracking_number'])) {
                continue;
            }

            $shipment = Shipment::create([
                'tracking_number' => $row['tracking_number'],
                'customer_name' => $row['customer_name'] ?? 'Guest',
                'customer_phone' => $row['customer_phone'] ?? null,
                'customer_address' => $row['customer_address'] ?? null,
                'shipping_date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['shipping_date'] ?? now()),
                'notes' => $row['notes'] ?? null,
                'shipping_company_id' => $this->shippingCompanyId,
                'status_id' => 37, // "عُهدة" as per request default
                'total_amount' => 0, // Calculated below
                'shipping_price' => 60, // Default or need column
            ]);

            // Handle Product Logic (Simplified based on row data)
            // Assuming row has product details. If multiple products per shipment in Excel, 
            // the Logic needs to be more complex (Grouping by Tracking Number).
            // For now, simpler row-based import:
            
            $productName = $row['product_name'] ?? 'Product';
            $quantity = $row['quantity'] ?? 1;
            $price = $row['selling_price'] ?? 0;
            
            $product = Product::firstOrCreate(['name' => $productName], [
                'cost_price' => $row['cost_price'] ?? 0,
                'price' => $price
            ]);

            $shipment->products()->attach($product->id, [
                'quantity' => $quantity,
                'price' => $price,
                'color' => 'N/A',
                'size' => 'N/A'
            ]);

            // Update Totals
            $shipment->total_amount = ($quantity * $price) + $shipment->shipping_price;
            $shipment->save();
        }
    }
}
