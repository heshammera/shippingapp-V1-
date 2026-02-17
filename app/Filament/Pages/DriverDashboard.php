<?php

namespace App\Filament\Pages;

use App\Models\Shipment;
use App\Models\ShipmentStatus;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;

class DriverDashboard extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'مهامي (للمناديب)';
    protected static ?string $title = 'مهام التوصيل';
    protected static ?string $navigationGroup = '📦 إدارة الشحنات';
    protected static string $view = 'filament.pages.driver-dashboard';

    public function table(Table $table): Table
    {
        $agent = Auth::user()->deliveryAgent;

        return $table
            ->query(
                Shipment::query()
                    ->when($agent, fn($q) => $q->where('delivery_agent_id', $agent->id))
                    ->whereHas('status', fn($q) => $q->whereIn('code', ['out_for_delivery', 'picked_up', 'pending']))
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('الزبون')
                    ->searchable()
                    ->description(fn($record) => $record->customer_phone),
                Tables\Columns\TextColumn::make('governorate')
                    ->label('المحافظة'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('المبلغ')
                    ->money('EGP'),
                Tables\Columns\TextColumn::make('status.name')
                    ->label('الحالة')
                    ->badge(),
            ])
            ->actions([
                Tables\Actions\Action::make('delivered')
                    ->label('توصيل')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('proof_photo')
                            ->label('صورة الإثبات')
                            ->image()
                            ->directory('delivery-proof'),
                        Forms\Components\ViewField::make('signature')
                            ->view('filament.forms.components.signature-pad'),
                        Forms\Components\Textarea::make('agent_notes')
                            ->label('ملاحظات'),
                        Forms\Components\Hidden::make('latitude')
                            ->extraAttributes(['id' => 'agent-lat']),
                        Forms\Components\Hidden::make('longitude')
                            ->extraAttributes(['id' => 'agent-lng']),
                        Forms\Components\Placeholder::make('geo_status')
                            ->label('تم تحديد الموقع')
                            ->content(fn() => new \Illuminate\Support\HtmlString('<span id="geo-indicator" class="text-xs text-gray-500">جاري جلب الموقع...</span>')),
                    ])
                    ->modalAlignment('center')
                    ->modalHeading('تأكيد التوصيل')
                    ->extraModalFooterActions([
                        Tables\Actions\Action::make('get_geo')
                            ->label('تحديث الموقع')
                            ->icon('heroicon-o-map-pin')
                            ->action(fn() => null)
                            ->extraAttributes([
                                'onclick' => "navigator.geolocation.getCurrentPosition(p => { 
                                    document.getElementById('agent-lat').value = p.coords.latitude;
                                    document.getElementById('agent-lng').value = p.coords.longitude;
                                    document.getElementById('geo-indicator').innerText = 'تم تحديد الموقع بنجاح: ' + p.coords.latitude.toFixed(4) + ',' + p.coords.longitude.toFixed(4);
                                    document.getElementById('geo-indicator').className = 'text-xs text-success-600';
                                })"
                            ])
                    ])
                    ->action(function (Shipment $record, array $data) {
                        $deliveredStatus = ShipmentStatus::where('code', 'delivered')->first();
                        $record->update([
                            'status_id' => $deliveredStatus?->id,
                            'proof_photo' => $data['proof_photo'] ?? null,
                            'signature' => $data['signature'] ?? null,
                            'latitude' => $data['latitude'] ?? null,
                            'longitude' => $data['longitude'] ?? null,
                            'agent_notes' => $data['agent_notes'] ?? null,
                            'delivered_at' => now(),
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تم التوصيل بنجاح')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('failed')
                    ->label('لم يتم')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Shipment $record) {
                        $failedStatus = ShipmentStatus::where('code', 'returned')->first(); // Or a custom code
                        $record->update(['status_id' => $failedStatus?->id]);
                    }),
            ]);
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('super_admin') || Auth::user()->deliveryAgent !== null;
    }
}
