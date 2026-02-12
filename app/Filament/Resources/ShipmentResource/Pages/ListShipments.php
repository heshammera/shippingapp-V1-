<?php

namespace App\Filament\Resources\ShipmentResource\Pages;

use App\Filament\Resources\ShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShipments extends ListRecords
{
    protected static string $resource = ShipmentResource::class;

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50, 100, 250];
    }
    
    protected function getDefaultTableRecordsPerPage(): int
    {
        return (int) \App\Models\Setting::getValue('pagination_limit', 15);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('import_excel')
                ->label('استيراد Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->url(\App\Filament\Pages\ImportShipments::getUrl()),
            Actions\Action::make('google_sheet')
                ->label('استيراد Google Sheet')
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    app(\App\Services\GoogleSheetImporter::class)->importOrders();
                    \Filament\Notifications\Notification::make()
                        ->title('تم الاستيراد من Google Sheet بنجاح')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        $tabs = ['all' => \Filament\Resources\Pages\ListRecords\Tab::make('الكل')];

        $statuses = \App\Models\ShipmentStatus::orderBy('sort_order')->get();

        foreach ($statuses as $status) {
            $tabs[$status->id] = \Filament\Resources\Pages\ListRecords\Tab::make($status->name)
                ->badgeColor($status->color)
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('status_id', $status->id));
        }

        return $tabs;
    }
}
