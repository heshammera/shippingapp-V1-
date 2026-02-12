<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentShipments extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static bool $shouldRegisterWidget = false;
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'آخر الشحنات';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Shipment::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('رقم التتبع'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('العميل'),
                Tables\Columns\TextColumn::make('status.name')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'New', 'جديد' => 'info',
                        'Delivered', 'تم التسليم' => 'success',
                        'Returned', 'مرتجع' => 'danger',
                        'Pending', 'قيد الانتظار' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('الإجمالي')
                    ->money('EGP'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('عرض')
                    ->url(fn (Shipment $record): string => route('filament.admin.resources.shipments.edit', $record)),
            ])
            ->paginated(false);
    }
}
