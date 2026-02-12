<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Expense;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CashboxExport;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


class CashboxReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $collections = Collection::when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->get();

        $expenses = Expense::when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->get();

        $totalCollections = $collections->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $balance = $totalCollections - $totalExpenses;

        $allTransactions = collect()
            ->merge($collections->map(fn($c) => [
                'date' => $c->date,
                'description' => 'تحصيل',
                'type' => 'collection',
                'amount' => $c->amount,
                'notes' => $c->notes,
            ]))
            ->merge($expenses->map(fn($e) => [
                'date' => $e->date,
                'description' => 'مصروف',
                'type' => 'expense',
                'amount' => $e->amount,
                'notes' => $e->notes,
            ]))
            ->sortBy('date');

        return view('reports.cashbox', compact(
            'dateFrom', 'dateTo', 'totalCollections', 'totalExpenses', 'balance', 'allTransactions'
        ));
    }

public function printPdf(Request $request)
{
    $data = $this->getData($request);

    $html = view('reports.cashbox_pdf', $data)->render();

$defaultConfig = (new ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];

$defaultFontConfig = (new FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];

$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font' => 'amiri',
    'fontDir' => array_merge($fontDirs, [storage_path('fonts')]),
    'fontdata' => $fontData + [
        'amiri' => [
            'R' => 'Amiri-Regular.ttf',
            'B' => 'Amiri-Bold.ttf',
        ]
    ],
]);


    $mpdf->autoScriptToLang = true;
    $mpdf->autoLangToFont = true;
    $mpdf->SetDirectionality('rtl');
    $mpdf->WriteHTML($html);

    return $mpdf->Output('تقرير_الخزنة.pdf', 'I');
}



  public function exportPdf(Request $request)
{
    $defaultConfig = (new ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font' => 'amiri',
        'fontDir' => array_merge($fontDirs, [storage_path('fonts')]),
        'fontdata' => $fontData + [
            'amiri' => [
                'R' => 'Amiri-Regular.ttf',
                'B' => 'Amiri-Bold.ttf',
            ]
        ],
    ]);

    $mpdf->autoScriptToLang = true;
    $mpdf->autoLangToFont = true;
    $mpdf->SetDirectionality('rtl');

    $html = view('reports.cashbox_pdf', $this->getData($request))->render();
    $mpdf->WriteHTML($html);
    return $mpdf->Output('تقرير_الخزنة.pdf', 'D'); // تحميل
}

    public function exportExcel(Request $request)
    {
        return Excel::download(new CashboxExport($request->date_from, $request->date_to), 'تقرير_الخزنة.xlsx');
    }

    private function getData(Request $request)
    {
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $collections = Collection::when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->get();

        $expenses = Expense::when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->get();

        $totalCollections = $collections->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $balance = $totalCollections - $totalExpenses;

        $allTransactions = collect()
            ->merge($collections->map(fn($c) => [
                'date' => $c->date,
                'description' => 'تحصيل',
                'type' => 'collection',
                'amount' => $c->amount,
                'notes' => $c->notes,
            ]))
            ->merge($expenses->map(fn($e) => [
                'date' => $e->date,
                'description' => 'مصروف',
                'type' => 'expense',
                'amount' => $e->amount,
                'notes' => $e->notes,
            ]))
            ->sortBy('date');

        return compact('dateFrom', 'dateTo', 'totalCollections', 'totalExpenses', 'balance', 'allTransactions');
    }
}
