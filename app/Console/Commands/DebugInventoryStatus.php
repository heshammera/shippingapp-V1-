<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\ShipmentStatus;
use App\Models\Shipment;

class DebugInventoryStatus extends Command
{
    protected $signature = 'debug:inventory-status';
    protected $description = 'Debug inventory status settings and logic';

    public function handle()
    {
        $this->info('🔍 Debugging Inventory Status Logic');
        $this->newLine();

        // 1. Check Raw Settings (DB)
        $this->info('1️⃣  Checking Database Settings:');
        $deliveredVal = Setting::where('key', 'delivered_status_id')->value('value');
        $returnedVal = Setting::where('key', 'returned_status_id')->value('value');

        $this->line("   - delivered_status_id (Raw): " . var_export($deliveredVal, true));
        $this->line("   - returned_status_id (Raw): " . var_export($returnedVal, true));

        // 2. Test Parsing Logic (Simulating Observer)
        $this->newLine();
        $this->info('2️⃣  Testing Observer Parsing Logic:');
        
        $deliveredIds = $this->parseIds($deliveredVal);
        $returnedIds = $this->parseIds($returnedVal);
        
        $this->line("   - Parsed Delivered IDs: " . json_encode($deliveredIds));
        $this->line("   - Parsed Returned IDs: " . json_encode($returnedIds));

        // 3. List All Statuses
        $this->newLine();
        $this->info('3️⃣  Available Statuses:');
        $statuses = ShipmentStatus::all();
        foreach ($statuses as $status) {
            $isDelivered = in_array($status->id, $deliveredIds) ? '✅ (Triggers Deduct)' : '';
            $isReturned = in_array($status->id, $returnedIds) ? '✅ (Triggers Return)' : '';
            $this->line("   [{$status->id}] {$status->name}  {$isDelivered}{$isReturned}");
        }

        // 4. Check Logs for recent errors
        $this->newLine();
        $this->info('4️⃣  Checking Logs for Deduction Errors:');
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            $errors = [];
            $lines = explode("\n", $logs);
            foreach (array_reverse($lines) as $line) {
                if (strpos($line, 'Failed to deduct stock') !== false || strpos($line, 'inventory') !== false) {
                    $errors[] = substr($line, 0, 150) . '...';
                    if (count($errors) >= 5) break; 
                }
            }
            if (empty($errors)) {
                $this->info("   No recent 'Failed to deduct stock' errors found in logs.");
            } else {
                foreach ($errors as $error) {
                    $this->error("   " . $error);
                }
            }
        }
    }

    private function parseIds($val)
    {
        // Logic from ShipmentObserver
        $decoded = json_decode($val, true);
        if (is_array($decoded)) {
            return array_map('intval', $decoded);
        }
        return $val ? [(int)$val] : [];
    }
}
