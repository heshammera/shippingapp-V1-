<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ShipmentsExport;
use App\Exports\CollectionsExport;
use App\Exports\ExpensesExport;
use App\Exports\TreasuryReportExport;
use App\Models\ShippingCompany;
use App\Models\ShipmentStatus;
use App\Models\DeliveryAgent;
use App\Models\Shipment;
use App\Models\Collection;
use App\Models\Expense;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * عرض صفحة التقارير الرئيسية
     */
    public function index()
    {
        $shippingCompanies = ShippingCompany::all();
        $shipmentStatuses = ShipmentStatus::all();
        $deliveryAgents = DeliveryAgent::all();
        
        return view('reports.index', compact('shippingCompanies', 'shipmentStatuses', 'deliveryAgents'));
    }
    
    /**
     * تقرير الشحنات
     */
    public function shipments(Request $request)
    {
        $filters = $request->only(['shipping_company_id', 'status_id', 'delivery_agent_id', 'date_from', 'date_to']);
        
        $query = Shipment::with(['shippingCompany', 'deliveryAgent', 'status']);
        
        // تطبيق الفلاتر
        if ($request->has('shipping_company_id') && $request->shipping_company_id) {
            $query->where('shipping_company_id', $request->shipping_company_id);
        }
        
        if ($request->has('status_id') && $request->status_id) {
            $query->where('status_id', $request->status_id);
        }
        
        if ($request->has('delivery_agent_id') && $request->delivery_agent_id) {
            $query->where('delivery_agent_id', $request->delivery_agent_id);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->where('shipping_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->where('shipping_date', '<=', $request->date_to);
        }
        
        $shipments = $query->orderBy('shipping_date', 'desc')->paginate(15);
        
        // حساب الإحصائيات
        $totalShipments = $shipments->total();
        $totalCost = $shipments->sum('cost_price');
        $totalSelling = $shipments->sum('selling_price');
        $totalProfit = $totalSelling - $totalCost;
        
        $shippingCompanies = ShippingCompany::all();
        $shipmentStatuses = ShipmentStatus::all();
        $deliveryAgents = DeliveryAgent::all();
        
        return view('reports.shipments', compact(
            'shipments', 
            'shippingCompanies', 
            'shipmentStatuses', 
            'deliveryAgents',
            'totalShipments',
            'totalCost',
            'totalSelling',
            'totalProfit',
            'filters'
        ));
    }
    
    /**
     * تصدير تقرير الشحنات بصيغة Excel
     */
    public function exportShipmentsExcel(Request $request)
    {
        $filters = $request->only(['shipping_company_id', 'status_id', 'delivery_agent_id', 'date_from', 'date_to']);
        
        return Excel::download(new ShipmentsExport($filters), 'shipments_report.xlsx');
    }
    
    /**
     * تصدير تقرير الشحنات بصيغة PDF
     */
public function exportShipmentsPdf(Request $request)
{
    $filters = $request->only(['shipping_company_id', 'status_id', 'delivery_agent_id', 'date_from', 'date_to']);
    
    $query = Shipment::with(['shippingCompany', 'deliveryAgent', 'status']);
    
    // تطبيق الفلاتر
    if (!empty($filters['shipping_company_id'])) {
        $query->where('shipping_company_id', $filters['shipping_company_id']);
    }
    
    if (!empty($filters['status_id'])) {
        $query->where('status_id', $filters['status_id']);
    }
    
    if (!empty($filters['delivery_agent_id'])) {
        $query->where('delivery_agent_id', $filters['delivery_agent_id']);
    }
    
    if (!empty($filters['date_from'])) {
        $query->where('shipping_date', '>=', $filters['date_from']);
    }
    
    if (!empty($filters['date_to'])) {
        $query->where('shipping_date', '<=', $filters['date_to']);
    }
    
    $shipments = $query->orderBy('shipping_date', 'desc')->get();
    
    // حساب الإحصائيات
    $totalShipments = $shipments->count();
    $totalCost = $shipments->sum('cost_price');
    $totalSelling = $shipments->sum('selling_price');
    $totalProfit = $totalSelling - $totalCost;

    // تحميل الـ View مع إعدادات PDF
    $pdf = PDF::loadView('reports.shipments_pdf', compact(
            'shipments',
            'totalShipments',
            'totalCost',
            'totalSelling',
            'totalProfit',
            'filters'
        ))
        ->setPaper('a4', 'landscape') // الوضع Landscape
        ->setOptions([
            'defaultFont' => 'Cairo',   // اختيار فونت يدعم العربي
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

    return $pdf->download('shipments_report.pdf');
}

    
    /**
     * تصدير تقرير التحصيلات بصيغة Excel
     */
    public function exportCollectionsExcel(Request $request)
    {
        $filters = $request->only(['shipping_company_id', 'date_from', 'date_to']);
        
        return Excel::download(new CollectionsExport($filters), 'collections_report.xlsx');
    }
    
    /**
     * تصدير تقرير التحصيلات بصيغة PDF
     */
    public function exportCollectionsPdf(Request $request)
    {
        $filters = $request->only(['shipping_company_id', 'date_from', 'date_to']);
        
        $query = Collection::with(['shippingCompany', 'createdBy']);
        
        // تطبيق الفلاتر
        if (isset($filters['shipping_company_id']) && $filters['shipping_company_id']) {
            $query->where('shipping_company_id', $filters['shipping_company_id']);
        }
        
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->where('collection_date', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->where('collection_date', '<=', $filters['date_to']);
        }
        
        $collections = $query->orderBy('collection_date', 'desc')->get();
        $total = $collections->sum('amount');
        
        // تجميع البيانات حسب شركة الشحن
        $collectionsByCompany = $collections->groupBy('shipping_company_id')
            ->map(function ($items, $key) {
                $companyName = $items->first()->shippingCompany->name;
                $total = $items->sum('amount');
                return [
                    'company_name' => $companyName,
                    'total' => $total,
                    'count' => $items->count(),
                ];
            });
        
        $pdf = PDF::loadView('reports.collections_pdf', compact(
            'collections',
            'total',
            'collectionsByCompany',
            'filters'
        ));
        
        return $pdf->download('collections_report.pdf');
    }
    
    /**
     * تصدير تقرير المصاريف بصيغة Excel
     */
    public function exportExpensesExcel(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to']);
        
        return Excel::download(new ExpensesExport($filters), 'expenses_report.xlsx');
    }
    
    /**
     * تصدير تقرير المصاريف بصيغة PDF
     */
    public function exportExpensesPdf(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to']);
        
        $query = Expense::with(['createdBy']);
        
        // تطبيق الفلاتر
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->where('expense_date', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->where('expense_date', '<=', $filters['date_to']);
        }
        
        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $total = $expenses->sum('amount');
        
        // تجميع البيانات حسب الشهر
        $expensesByMonth = $expenses->groupBy(function ($item) {
            return $item->expense_date->format('Y-m');
        })->map(function ($items, $key) {
            $monthName = date('F Y', strtotime($key . '-01'));
            $total = $items->sum('amount');
            return [
                'month_name' => $monthName,
                'total' => $total,
                'count' => $items->count(),
            ];
        });
        
        $pdf = PDF::loadView('reports.expenses_pdf', compact(
            'expenses',
            'total',
            'expensesByMonth',
            'filters'
        ));
        
        return $pdf->download('expenses_report.pdf');
    }
    
    /**
     * تصدير تقرير الخزنة بصيغة Excel
     */
    public function exportTreasuryExcel(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to']);
        
        return Excel::download(new TreasuryReportExport($filters), 'treasury_report.xlsx');
    }
    
    /**
     * تصدير تقرير الخزنة بصيغة PDF
     */
    public function exportTreasuryPdf(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to']);
        
        // الفترة الزمنية
        $dateFrom = $filters['date_from'] ?? date('Y-m-01');
        $dateTo = $filters['date_to'] ?? date('Y-m-t');
        
        // التحصيلات
        $collections = Collection::with('shippingCompany')
            ->whereBetween('collection_date', [$dateFrom, $dateTo])
            ->orderBy('collection_date')
            ->get();
            
        // المصاريف
        $expenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->orderBy('expense_date')
            ->get();
            
        // إجماليات
        $totalCollections = $collections->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $balance = $totalCollections - $totalExpenses;
        
        // دمج التحصيلات والمصاريف وترتيبها حسب التاريخ
        $allTransactions = [];
        
        foreach ($collections as $collection) {
            $allTransactions[] = [
                'date' => $collection->collection_date->format('Y-m-d'),
                'type' => 'collection',
                'description' => 'تحصيل من ' . $collection->shippingCompany->name,
                'amount' => $collection->amount,
                'notes' => $collection->notes,
            ];
        }
        
        foreach ($expenses as $expense) {
            $allTransactions[] = [
                'date' => $expense->expense_date->format('Y-m-d'),
                'type' => 'expense',
                'description' => $expense->title,
                'amount' => $expense->amount,
                'notes' => $expense->notes,
            ];
        }
        
        // ترتيب المعاملات حسب التاريخ
        usort($allTransactions, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });
        
        // حساب الرصيد التراكمي
        $runningBalance = 0;
        foreach ($allTransactions as &$transaction) {
            if ($transaction['type'] == 'collection') {
                $runningBalance += $transaction['amount'];
            } else {
                $runningBalance -= $transaction['amount'];
            }
            $transaction['running_balance'] = $runningBalance;
        }
        
        $pdf = PDF::loadView('reports.treasury_pdf', compact(
            'allTransactions',
            'totalCollections',
            'totalExpenses',
            'balance',
            'dateFrom',
            'dateTo'
        ));
        
        return $pdf->download('treasury_report.pdf');
    }
}
