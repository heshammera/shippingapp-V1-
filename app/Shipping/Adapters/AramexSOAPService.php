<?php

namespace App\Shipping\Adapters;

use App\Models\Shipment;
use Illuminate\Support\Facades\Log;

class AramexSOAPService
{
    public static function buildCreateShipmentPayload(Shipment $shipment, array $config)
    {
        return [
            'ClientInfo' => [
                'UserName' => $config['username'],
                'Password' => $config['password'],
                'Version' => 'v1.0',
                'AccountNumber' => $config['account_number'],
                'AccountPin' => $config['account_pin'],
                'AccountEntity' => $config['account_entity'],
                'AccountCountryCode' => $config['account_country'],
            ],
            'Shipments' => [
                'Shipment' => [
                    'Shipper' => [
                        'Reference1' => $shipment->tracking_number,
                        'AccountNumber' => $config['account_number'],
                        'Contact' => [
                            'PersonName' => config('app.name'),
                            'CompanyName' => config('app.name'),
                            'PhoneNumber1' => '0123456789',
                        ],
                        'Address' => [
                            'Line1' => 'Head Office',
                            'City' => 'Cairo',
                            'CountryCode' => 'EG',
                        ],
                    ],
                    'Consignee' => [
                        'Reference1' => 'REF-' . $shipment->id,
                        'Contact' => [
                            'PersonName' => $shipment->customer_name,
                            'CompanyName' => $shipment->customer_name,
                            'PhoneNumber1' => $shipment->customer_phone,
                        ],
                        'Address' => [
                            'Line1' => $shipment->customer_address,
                            'City' => $shipment->governorate,
                            'CountryCode' => 'EG',
                        ],
                    ],
                    'Details' => [
                        'ActualWeight' => ['Value' => 1, 'Unit' => 'Kg'],
                        'NumberOfPieces' => 1,
                        'ProductGroup' => 'DOM', // Domestic
                        'ProductType' => 'ONP', // Overnight Parcel
                        'PaymentType' => 'P', // Prepaid or Collect
                        'DescriptionOfGoods' => 'Order #' . $shipment->tracking_number,
                        'CustomsValueAmount' => ['Value' => $shipment->total_amount, 'CurrencyCode' => 'EGP'],
                    ],
                ]
            ],
            'Transaction' => [
                'Reference1' => $shipment->tracking_number,
            ]
        ];
    }
}
