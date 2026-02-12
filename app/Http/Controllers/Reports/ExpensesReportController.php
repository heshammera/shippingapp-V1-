<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpensesExport;

class ExpensesReportController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::with('user')
            ->when($request->date_from, fn($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('date', '<=', $request->date_to))
            ->get();

        $total_expenses = $expenses->sum('amount');

        return view('reports.expenses', compact('expenses', 'total_expenses'));
    }

    // 🔹 زر "تصدير PDF" (تحميل الملف)
    public function exportPdf(Request $request)
    {
        $expenses = Expense::with('user')
            ->when($request->date_from, fn($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('date', '<=', $request->date_to))
            ->get();

        $total_expenses = $expenses->sum('amount');
        $html = view('reports.expenses_pdf', compact('expenses', 'total_expenses'))->render();

        $mpdf = $this->initMpdf();
        $mpdf->WriteHTML($html);

        $filename = 'تقرير_المصاريف_' . now()->format('Ymd_His') . '.pdf';
        return $mpdf->Output($filename, 'D'); // تحميل الملف
    }

    // 🔹 زر "طباعة التقرير" (فتح في المتصفح + نافذة الطباعة)
    public function printPdf(Request $request)
    {
        $expenses = Expense::with('user')
            ->when($request->date_from, fn($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('date', '<=', $request->date_to))
            ->get();

        $total_expenses = $expenses->sum('amount');
        $html = view('reports.expenses_pdf', compact('expenses', 'total_expenses'))->render();

        $mpdf = $this->initMpdf();
        $mpdf->WriteHTML($html);

        // أمر الطباعة التلقائي
        $mpdf->SetJS('this.print();');

        $filename = 'تقرير_المصاريف_' . now()->format('Ymd_His') . '.pdf';
        return $mpdf->Output($filename, 'I'); // فتح الملف في المتصفح
    }

    // 🔹 تصدير Excel
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new ExpensesExport($request->date_from, $request->date_to),
            'تقرير_المصاريف_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    // 🔹 إعداد mPDF بخط Amiri ودعم RTL
    private function initMpdf()
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

        return $mpdf;
    }
}
