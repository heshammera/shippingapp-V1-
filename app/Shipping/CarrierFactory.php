<?php

namespace App\Shipping;

use App\Models\ShippingCompany;
use Exception;

class CarrierFactory
{
    public static function make(ShippingCompany $company): CarrierInterface
    {
        $type = $company->integration_type;

        return match ($type) {
            'aramex' => new \App\Shipping\Adapters\AramexAdapter($company),
            'dhl' => new \App\Shipping\Adapters\DHLAdapter($company),
            'internal' => new \App\Shipping\Adapters\InternalAdapter($company),
            default => throw new Exception("Unsupported integration type: {$type}"),
        };
    }
}
