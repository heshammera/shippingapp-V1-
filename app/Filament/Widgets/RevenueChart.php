<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'الإيرادات خلال 30 يوم';
    
    protected static bool $shouldRegisterWidget = false;

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Shipment::select(DB::raw('DATE(created_at) as date'), DB::raw('sum(total_amount) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات',
                    'data' => $data->pluck('total')->toArray(),
                    'borderColor' => '#10b981',
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
