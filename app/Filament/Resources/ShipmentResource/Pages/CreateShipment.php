<?php

namespace App\Filament\Resources\ShipmentResource\Pages;

use App\Filament\Resources\ShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;

    protected $productsData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Handle legacy product_name column via repeater data
        if (isset($data['products']) && is_array($data['products'])) {
            $names = [];
            foreach ($data['products'] as $item) {
                if (!empty($item['product_id'])) {
                    $product = \App\Models\Product::find($item['product_id']);
                    if ($product) {
                        $names[] = $product->name;
                    }
                }
            }
            $data['product_name'] = !empty($names) ? implode(', ', $names) : 'Multiple Products';
            
            // Capture products data for manual saving
            $this->productsData = $data['products'];
            unset($data['products']); // Prevent model filling error

        } else {
            $data['product_name'] = 'Unknown Product';
        }

        // Set default status if not present (37 = غير محدد)
        if (empty($data['status_id'])) {
            $data['status_id'] = 37;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();
        
        foreach ($this->productsData as $item) {
            if (!empty($item['product_id'])) {
                $record->products()->attach($item['product_id'], [
                    'quantity' => $item['quantity'] ?? 1,
                    'price' => $item['price'] ?? 0,
                    'color' => $item['color'] ?? null,
                    'size'  => $item['size'] ?? null,
                ]);
            }
        }
    }
}
