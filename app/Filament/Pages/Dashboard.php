<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // Override the default columns to be a 3-column grid on desktop
    public function getColumns(): int | string | array
    {
        return [
            'md' => 1,
            'lg' => 2,
        ];
    }
}
