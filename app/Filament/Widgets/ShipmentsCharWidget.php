<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Widgets\ChartWidget;


class ShipmentsCharWidget extends ChartWidget
{
    protected static ?string $heading = 'الشحنات خلال الأسبوع';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        // Manual aggregation for PostgreSQL without Trend package
        $data = Shipment::query()
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM-DD') as date, count(*) as aggregate")
            ->where('created_at', '>=', now()->subWeek())
            ->where('created_at', '<=', now())
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Fill missing days manually if needed, or just show available data
        // For line charts, it's better to show all days. 
        // Quick map to keyed array
        $dataMap = $data->pluck('aggregate', 'date');
        
        $labels = [];
        $values = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = $date;
            $values[] = $dataMap[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'عدد الشحنات',
                    'data' => $values,
                    'borderColor' => '#0d9488', // Teal
                    'backgroundColor' => 'rgba(13, 148, 136, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
