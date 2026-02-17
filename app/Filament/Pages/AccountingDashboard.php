<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class AccountingDashboard extends Page
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'لوحة المالية';
    protected static ?string $navigationGroup = '💰 الإدارة المالية';
    protected static ?string $slug = 'accounting-dashboard';
    protected static ?string $title = 'الملخص المالي';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.accounting-dashboard';

    public function filters(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('من تاريخ'),
                        DatePicker::make('endDate')
                            ->label('إلى تاريخ'),
                    ])
                    ->columns(2),
            ]);
    }

    public function getDashboardWidgets(): array
    {
        return [
            \App\Filament\Widgets\FinanceStatsWidget::class,
            \App\Filament\Widgets\FinancialChart::class,
            \App\Filament\Widgets\ShippingCompanyBalancesWidget::class,
            \App\Filament\Widgets\RecentCollectionsWidget::class,
            \App\Filament\Widgets\RecentExpensesWidget::class,
        ];
    }
}
