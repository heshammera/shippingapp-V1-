<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\Shipment;
use App\Services\InventoryServiceV2;

class InventoryBackfill extends Command
{
    protected $signature = 'inventory:backfill {--dry : Preview only, do not write stock}';
    protected $description = 'Backfill inventory movements based on current shipments state (safe with shadow mode)';

public function handle(): int
{
    $dry = (bool) $this->option('dry');

    $settings = \App\Models\Setting::pluck('value','key')->toArray();
    if (empty($settings)) {
        $this->error('App settings not found. Configure /settings first.');
        return 1;
    }

    $svc = app(\App\Services\InventoryServiceV2::class);

    $deliveredId = (int)($settings['delivered_status_id'] ?? 0);
    $returnedId  = (int)($settings['returned_status_id'] ?? 0);
    $defaultCompanyId = (int)($settings['default_shipping_company_id'] ?? 0);

    $count = 0;

    \App\Models\Shipment::with(['products','shippingCompany'])->chunkById(500, function($chunk) use (&$count, $svc, $defaultCompanyId, $deliveredId, $returnedId, $dry) {
        foreach ($chunk as $s) {
            $affects = optional($s->shippingCompany)->affects_inventory ?? true;

            if ($affects && $defaultCompanyId && (int)$s->shipping_company_id !== $defaultCompanyId) {
                if (is_null($s->inventory_reserved_at)) {
                    if (! $dry) $svc->reserveForShipment($s, 'reserve_backfill_company_mismatch');
                    $count++;
                }
            }

            if ($returnedId && (int)$s->status_id === $returnedId) {
                if (is_null($s->inventory_returned_at)) {
                    if (! $dry) $svc->returnToStockOnReturn($s, 'return_backfill_status_returned');
                    $count++;
                }
            }

            if ($deliveredId && (int)$s->status_id === $deliveredId && is_null($s->delivered_at)) {
                if (! $dry) $s->forceFill(['delivered_at'=>now()])->saveQuietly();
            }
        }
    });

    $this->info(($dry ? '[DRY] ' : '').'Backfill processed entries: '.$count);
    return 0;
}

}
