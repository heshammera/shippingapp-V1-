<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use PDF; // Barryvdh\DomPDF\Facade
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
        
        $pdf = PDF::loadView('reports.expenses_pdf', compact('expenses', 'total_expenses'));
        
        // Optional: Set paper size if needed
        // $pdf->setPaper('a4', 'portrait');

        $filename = 'تقرير_المصاريف_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    // 🔹 زر "طباعة التقرير" (فتح في المتصفح)
    public function printPdf(Request $request)
    {
        $expenses = Expense::with('user')
            ->when($request->date_from, fn($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('date', '<=', $request->date_to))
            ->get();

        $total_expenses = $expenses->sum('amount');
        
        $pdf = PDF::loadView('reports.expenses_pdf', compact('expenses', 'total_expenses'));

        $filename = 'تقرير_المصاريف_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->stream($filename);
    }

    // 🔹 تصدير Excel
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new ExpensesExport($request->date_from, $request->date_to),
            'تقرير_المصاريف_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
