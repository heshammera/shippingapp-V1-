<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\ShippingCompany;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ShipmentsImport;
use Filament\Forms\Form;

class ImportShipments extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationLabel = 'استيراد شحنات (Excel)';
    protected static ?string $title = 'استيراد الشحنات من Excel';
    protected static ?string $slug = 'shipments/import-excel';
    protected static ?string $navigationGroup = '📦 العمليات';
    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = true;

    protected static string $view = 'filament.pages.import-shipments';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('shipping_company_id')
                    ->label('شركة الشحن')
                    ->options(ShippingCompany::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                
                FileUpload::make('file')
                    ->label('ملف Excel')
                    ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
                    ->required()
                    ->storeFiles(true), // We need the file stored to pass path to Maatwebsite
            ])
            ->statePath('data');
    }

    public function import()
    {
        $data = $this->form->getState();

        try {
            // Get proper path (Filament stores in public/storage usually, Maatwebsite needs absolute or storage path)
            // storage_path('app/public/' . $data['file']) might be needed
            $filePath = storage_path('app/public/' . $data['file']);
            
            Excel::import(new ShipmentsImport($data['shipping_company_id']), $filePath);

            Notification::make()
                ->title('تم الاستيراد بنجاح')
                ->success()
                ->send();

            return redirect()->route('filament.admin.resources.shipments.index');

        } catch (\Exception $e) {
            Notification::make()
                ->title('خطأ في الاستيراد')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
