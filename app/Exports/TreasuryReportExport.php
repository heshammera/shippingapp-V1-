<?php

namespace App\Exports;

use App\Models\Shipment;
use App\Models\Collection;
use App\Models\Expense;
use Illuminate\Support\Collection as IlluminateCollection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class TreasuryReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // الفترة الزمنية
        $dateFrom = $this->filters['date_from'] ?? date('Y-m-01');
        $dateTo = $this->filters['date_to'] ?? date('Y-m-t');
        
        // التحصيلات
        $collections = Collection::with('shippingCompany')
            ->whereBetween('collection_date', [$dateFrom, $dateTo])
            ->orderBy('collection_date')
            ->get()
            ->map(function ($collection) {
                return [
                    'date' => $collection->collection_date->format('Y-m-d'),
                    'type' => 'collection',
                    'description' => 'تحصيل من ' . $collection->shippingCompany->name,
                    'amount' => $collection->amount,
                    'notes' => $collection->notes,
                ];
            });
            
        // المصاريف
        $expenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->orderBy('expense_date')
            ->get()
            ->map(function ($expense) {
                return [
                    'date' => $expense->expense_date->format('Y-m-d'),
                    'type' => 'expense',
                    'description' => $expense->title,
                    'amount' => $expense->amount,
                    'notes' => $expense->notes,
                ];
            });
        
        // دمج التحصيلات والمصاريف
        $allTransactions = $collections->concat($expenses)->sortBy('date');
        
        // حساب الرصيد التراكمي
        $runningBalance = 0;
        $result = new IlluminateCollection();
        
        foreach ($allTransactions as $transaction) {
            if ($transaction['type'] == 'collection') {
                $runningBalance += $transaction['amount'];
            } else {
                $runningBalance -= $transaction['amount'];
            }
            
            $transaction['running_balance'] = $runningBalance;
            $result->push((object)$transaction);
        }
        
        return $result;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'التاريخ',
            'البيان',
            'الإيرادات',
            'المصروفات',
            'الرصيد',
            'ملاحظات'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($transaction): array
    {
        return [
            $transaction->date,
            $transaction->description,
            $transaction->type == 'collection' ? $transaction->amount : '',
            $transaction->type == 'expense' ? $transaction->amount : '',
            $transaction->running_balance,
            $transaction->notes ?? ''
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // تنسيق الصف الأول (العناوين)
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF']
                ],
            ],
        ];
    }
    
    /**
     * @return string
     */
    public function title(): string
    {
        return 'تقرير الخزنة';
    }
}
