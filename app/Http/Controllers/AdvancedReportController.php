<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Shipment, Collection, Expense, ShippingCompany, ShipmentStatus, DeliveryAgent};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdvancedReportController extends Controller
{
    /**
     * صفحة التقارير المتقدمة الرئيسية
     */
    public function index()
    {
        return view('reports-v2.index');
    }
    
    /**
     * تقرير الشحنات المتقدم
     */
    public function shipments(Request $request)
    {
        $filters = $request->only(['shipping_company_id', 'status_id', 'delivery_agent_id', 'date_from', 'date_to']);
        
        $query = Shipment::with(['shippingCompany', 'deliveryAgent', 'status', 'products']);
        
        // تطبيق الفلاتر
        if ($request->filled('shipping_company_id')) {
            $query->where('shipping_company_id', $request->shipping_company_id);
        }
        
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }
        
        if ($request->filled('delivery_agent_id')) {
            $query->where('delivery_agent_id', $request->delivery_agent_id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('shipping_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('shipping_date', '<=', $request->date_to);
        }
        
        $shipments = $query->orderBy('shipping_date', 'desc')->paginate(20);
        
        // حساب الإحصائيات
        $allShipments = $query->get();
        $totalShipments = $allShipments->count();
        $totalCost = $allShipments->sum('cost_price');
        $totalSelling = $allShipments->sum('selling_price');
        $totalProfit = $totalSelling - $totalCost;
        
        // حساب المقارنة مع الفترة السابقة (للـ KPIs)
        $comparisonData = $this->getComparisonData($request, 'shipments');
        
        // بيانات الرسوم البيانية
        $chartData = $this->getShipmentsChartData($request);
        
        $shippingCompanies = ShippingCompany::all();
        $shipmentStatuses = ShipmentStatus::all();
        $deliveryAgents = DeliveryAgent::all();
        
        return view('reports-v2.shipments', compact(
            'shipments',
            'shippingCompanies',
            'shipmentStatuses',
            'deliveryAgents',
            'totalShipments',
            'totalCost',
            'totalSelling',
            'totalProfit',
            'filters',
            'comparisonData',
            'chartData'
        ));
    }
    
    /**
     * تقرير التحصيلات المتقدم
     */
    public function collections(Request $request)
    {
        $filters = $request->only(['shipping_company_id', 'date_from', 'date_to']);
        
        $query = Collection::with(['shippingCompany']);
        
        if ($request->filled('shipping_company_id')) {
            $query->where('shipping_company_id', $request->shipping_company_id);
        }
        
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->endOfMonth()->toDateString();
        
        $query->whereBetween('date', [$dateFrom, $dateTo]);
        
        $collections = $query->orderBy('date', 'desc')->paginate(20);
        $totalCollection = $query->sum('amount');
        
        // المقارنة مع الفترة السابقة
        $comparisonData = $this->getComparisonData($request, 'collections');
        
        // بيانات الرسوم البيانية
        $chartData = $this->getCollectionsChartData($request);
        
        // تحصيلات حسب الشركة
        $collectionsByCompany = Collection::whereBetween('date', [$dateFrom, $dateTo])
            ->with('shippingCompany')
            ->select('shipping_company_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('shipping_company_id')
            ->get();
        
        $shippingCompanies = ShippingCompany::all();
        
        return view('reports-v2.collections', compact(
            'collections',
            'shippingCompanies',
            'totalCollection',
            'filters',
            'comparisonData',
            'chartData',
            'collectionsByCompany'
        ));
    }
    
    /**
     * تقرير المصاريف المتقدم
     */
    public function expenses(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to']);
        
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->endOfMonth()->toDateString();
        
        $expenses = Expense::with('user')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->orderBy('expense_date', 'desc')
            ->paginate(20);
            
        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
        
        // المقارنة
        $comparisonData = $this->getComparisonData($request, 'expenses');
        
        // بيانات الرسوم البيانية
        $chartData = $this->getExpensesChartData($request);
        
        return view('reports-v2.expenses', compact(
            'expenses',
            'totalExpenses',
            'filters',
            'comparisonData',
            'chartData'
        ));
    }
    
    /**
     * تقرير الخزنة المتقدم
     */
    public function treasury(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->endOfMonth()->toDateString();
        
        // التحصيلات
        $collections = Collection::with('shippingCompany')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date')
            ->get();
            
        // المصاريف
        $expenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->orderBy('expense_date')
            ->get();
            
        // إجماليات
        $totalCollections = $collections->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $balance = $totalCollections - $totalExpenses;
        
        // المقارنة
        $comparisonData = $this->getComparisonData($request, 'treasury');
        
        // دمج كل المعاملات
        $allTransactions = [];
        
        foreach ($collections as $collection) {
            $allTransactions[] = [
                'date' => $collection->date,
                'type' => 'collection',
                'description' => 'تحصيل من ' . ($collection->shippingCompany->name ?? 'غير محدد'),
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
        
        // ترتيب
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
        
        // بيانات الرسوم البيانية
        $chartData = $this->getTreasuryChartData($request);
        
        return view('reports-v2.treasury', compact(
            'allTransactions',
            'totalCollections',
            'totalExpenses',
            'balance',
            'dateFrom',
            'dateTo',
            'comparisonData',
            'chartData'
        ));
    }
    
    /**
     * حساب بيانات المقارنة مع الفترة السابقة
     */
    private function getComparisonData($request, $type)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->endOfMonth()->toDateString();
        
        $currentStart = Carbon::parse($dateFrom);
        $currentEnd = Carbon::parse($dateTo);
        $daysDiff = $currentStart->diffInDays($currentEnd) + 1;
        
        $previousStart = $currentStart->copy()->subDays($daysDiff);
        $previousEnd = $currentEnd->copy()->subDays($daysDiff);
        
        $current = 0;
        $previous = 0;
        
        switch ($type) {
            case 'shipments':
                $current = Shipment::whereBetween('shipping_date', [$currentStart, $currentEnd])->count();
                $previous = Shipment::whereBetween('shipping_date', [$previousStart, $previousEnd])->count();
                break;
            case 'collections':
                $current = Collection::whereBetween('date', [$currentStart, $currentEnd])->sum('amount');
                $previous = Collection::whereBetween('date', [$previousStart, $previousEnd])->sum('amount');
                break;
            case 'expenses':
                $current = Expense::whereBetween('expense_date', [$currentStart, $currentEnd])->sum('amount');
                $previous = Expense::whereBetween('expense_date', [$previousStart, $previousEnd])->sum('amount');
                break;
            case 'treasury':
                $currentColl = Collection::whereBetween('date', [$currentStart, $currentEnd])->sum('amount');
                $currentExp = Expense::whereBetween('expense_date', [$currentStart, $currentEnd])->sum('amount');
                $current = $currentColl - $currentExp;
                
                $previousColl = Collection::whereBetween('date', [$previousStart, $previousEnd])->sum('amount');
                $previousExp = Expense::whereBetween('expense_date', [$previousStart, $previousEnd])->sum('amount');
                $previous = $previousColl - $previousExp;
                break;
        }
        
        $percentChange = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
        
        return [
            'current' => $current,
            'previous' => $previous,
            'percent_change' => round($percentChange, 1),
            'is_positive' => $percentChange >= 0
        ];
    }
    
    /**
     * بيانات رسم الشحنات
     */
    private function getShipmentsChartData($request)
    {
        $dateFrom = $request->date_from ?? now()->subMonths(6)->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();
        
        $shipments = Shipment::whereBetween('shipping_date', [$dateFrom, $dateTo])
            ->select(DB::raw('DATE(shipping_date) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return [
            'labels' => $shipments->pluck('date')->toArray(),
            'counts' => $shipments->pluck('count')->toArray(),
        ];
    }
    
    /**
     * بيانات رسم التحصيلات
     */
    private function getCollectionsChartData($request)
    {
        $dateFrom = $request->date_from ?? now()->subMonths(6)->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();
        
        $collections = Collection::whereBetween('date', [$dateFrom, $dateTo])
            ->select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return [
            'labels' => $collections->pluck('date')->toArray(),
            'amounts' => $collections->pluck('total')->toArray(),
        ];
    }
    
    /**
     * بيانات رسم المصاريف
     */
    private function getExpensesChartData($request)
    {
        $dateFrom = $request->date_from ?? now()->subMonths(6)->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();
        
        $expenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->select(DB::raw('DATE(expense_date) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return [
            'labels' => $expenses->pluck('date')->toArray(),
            'amounts' => $expenses->pluck('total')->toArray(),
        ];
    }
    
    /**
     * بيانات رسم الخزنة
     */
    private function getTreasuryChartData($request)
    {
        $dateFrom = $request->date_from ?? now()->subMonths(6)->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();
        
        $collections = Collection::whereBetween('date', [$dateFrom, $dateTo])
            ->select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');
        
        $expenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->select(DB::raw('DATE(expense_date) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');
        
        // دمج التواريخ
        $allDates = collect($collections->keys())->merge($expenses->keys())->unique()->sort()->values();
        
        $collectionsData = [];
        $expensesData = [];
        $balanceData = [];
        $runningBalance = 0;
        
        foreach ($allDates as $date) {
            $coll = $collections->get($date, 0);
            $exp = $expenses->get($date, 0);
            $runningBalance += ($coll - $exp);
            
            $collectionsData[] = $coll;
            $expensesData[] = $exp;
            $balanceData[] = $runningBalance;
        }
        
        return [
            'labels' => $allDates->toArray(),
            'collections' => $collectionsData,
            'expenses' => $expensesData,
            'balance' => $balanceData,
        ];
    }
}
