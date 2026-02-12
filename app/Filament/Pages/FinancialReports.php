<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Services\FinancialStatementsService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Carbon\Carbon;

class FinancialReports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'التقارير المالية';
    protected static ?string $title = 'التقارير المالية المتقدمة';
    protected static ?string $navigationGroup = '💰 المالية';
    protected static ?int $navigationSort = 7;

    protected static string $view = 'filament.pages.financial-reports';

    public ?array $incomeStatement = null;
    public ?array $balanceSheet = null;

    // Filter Data
    public ?string $start_date = null;
    public ?string $end_date = null;

    public function mount()
    {
        $this->form->fill([
            'start_date' => Carbon::now()->startOfYear()->format('Y-m-d'),
            'end_date' => Carbon::now()->format('Y-m-d'),
        ]);

        $this->generateReports();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('start_date')
                    ->label('من تاريخ')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('إلى تاريخ')
                    ->required(),
            ])
            ->columns(2);
    }

    public function generateReports()
    {
        $data = $this->form->getState();
        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);

        $service = app(FinancialStatementsService::class);

        $this->incomeStatement = $service->getIncomeStatement($start, $end);
        $this->balanceSheet = $service->getBalanceSheet($end);
    }
}
