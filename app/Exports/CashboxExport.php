<?php

namespace App\Exports;

use App\Models\Collection;
use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CashboxExport implements FromCollection, WithHeadings
{
    protected $dateFrom;
    protected $dateTo;

    public function __construct($dateFrom = null, $dateTo = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        // اجمع التحصيلات والمصروفات في مجموعة واحدة كما في تقريرك
        $collections = Collection::when($this->dateFrom, fn($q) => $q->whereDate('date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('date', '<=', $this->dateTo))
            ->get()
            ->map(fn($c) => [
                'date' => $c->date,
                'description' => 'تحصيل',
                'amount' => $c->amount,
                'type' => 'collection',
                'notes' => $c->notes,
            ]);

        $expenses = Expense::when($this->dateFrom, fn($q) => $q->whereDate('date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('date', '<=', $this->dateTo))
            ->get()
            ->map(fn($e) => [
                'date' => $e->date,
                'description' => 'مصروف',
                'amount' => $e->amount,
                'type' => 'expense',
                'notes' => $e->notes,
            ]);

        $all = $collections->merge($expenses)->sortBy('date')->values();

        return collect($all);
    }

    public function headings(): array
    {
        return [
            'التاريخ',
            'البيان',
            'المبلغ',
            'النوع',
            'ملاحظات',
        ];
    }
}
