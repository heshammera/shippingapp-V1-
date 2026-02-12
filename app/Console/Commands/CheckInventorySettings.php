<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ShipmentStatus;
use App\Models\Setting;

class CheckInventorySettings extends Command
{
    protected $signature = 'inventory:check-settings';
    protected $description = 'Check inventory settings and status IDs';

    public function handle()
    {
        $this->info('📊 Status Configuration Check');
        $this->newLine();
        
        $this->info('--- Available Statuses ---');
        ShipmentStatus::all(['id', 'name'])->each(fn($s) => 
            $this->line("ID: {$s->id} | Name: {$s->name}")
        );
        
        $this->newLine();
        $this->info('--- Settings Configuration ---');
        $settings = Setting::first();
        
        if (!$settings) {
            $this->error('❌ No Settings Found!');
            return 1;
        }
        
        $deliveredId = $settings->delivered_status_id;
        $returnedId = $settings->returned_status_id;
        
        $this->line("Delivered Status ID in Settings: " . ($deliveredId ?? 'NULL'));
        $this->line("Returned Status ID in Settings: " . ($returnedId ?? 'NULL'));
        
        $this->newLine();
        $this->info('--- Diagnosis ---');
        
        if (!$deliveredId) {
            $this->error('❌ Critical: "Delivered Status" is NOT set in Settings!');
            $this->warn('   → Go to Settings page and select which status represents "Delivered".');
        } else {
            $status = ShipmentStatus::find($deliveredId);
            if ($status) {
                $this->info("✅ Delivered ID matches: {$status->name}");
            } else {
                $this->error("❌ Delivered ID ({$deliveredId}) points to a non-existent status!");
            }
        }
    }
}
