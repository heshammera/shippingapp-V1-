<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Expense;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // فوق في أعلى الملف تأكد تضيف use Carbon

class AccountingController extends Controller
{
    /**
     * عرض لوحة تحكم الحسابات (الخزنة)
     */
    public function index(Request $request)
    {
        // الفترة الزمنية الافتراضية (الشهر الحالي)
        $dateFrom = $request->date_from ?? date('Y-m-01');
        $dateTo = $request->date_to ?? date('Y-m-t');
        
        // إجمالي التحصيلات
        $totalCollections = Collection::whereBetween('collection_date', [$dateFrom, $dateTo])
            ->sum('amount');
            
        // إجمالي المصاريف
        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->sum('amount');
            
        // رصيد الخزنة
        $balance = $totalCollections - $totalExpenses;
        
        // التحصيلات حسب شركة الشحن
        $collectionsByCompany = Collection::whereBetween('collection_date', [$dateFrom, $dateTo])
            ->select('shipping_company_id', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('shipping_company_id')
            ->with('shippingCompany')
            ->get();
            
        // أحدث التحصيلات
        $latestCollections = Collection::with('shippingCompany')
            ->orderBy('collection_date', 'desc')
            ->limit(5)
            ->get();
            
        // أحدث المصاريف
        $latestExpenses = Expense::orderBy('expense_date', 'desc')
            ->limit(5)
            ->get();
            
        // بيانات الرسم البياني للتحصيلات والمصاريف الشهرية
        $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));
        

$collectionsRaw = Collection::where('collection_date', '>=', $sixMonthsAgo)
    ->select('collection_date', 'amount')
    ->orderBy('collection_date')
    ->get();



        
        
        $monthlyCollections = $collectionsRaw->groupBy(function ($item) {
    return Carbon::parse($item->collection_date)->format('Y-m');
})->map(function ($group) {
    return $group->sum('amount');
})->toArray();
            
        $expensesRaw = Expense::where('expense_date', '>=', $sixMonthsAgo)
    ->select('expense_date', 'amount')
    ->orderBy('expense_date')
    ->get();

$monthlyExpenses = $expensesRaw->groupBy(function ($item) {
    return Carbon::parse($item->expense_date)->format('Y-m');
})->map(function ($group) {
    return $group->sum('amount');
})->toArray();

            
        // إنشاء مصفوفة الأشهر الستة الماضية
        $months = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[] = $month;
            $monthLabels[] = date('M Y', strtotime("$month-01"));
        }
        
        // تجهيز بيانات الرسم البياني
        $chartData = [
            'labels' => $monthLabels,
            'collections' => [],
            'expenses' => [],
            'balance' => []
        ];
        
        foreach ($months as $month) {
            $collectionAmount = $monthlyCollections[$month] ?? 0;
            $expenseAmount = $monthlyExpenses[$month] ?? 0;
            $monthBalance = $collectionAmount - $expenseAmount;
            
            $chartData['collections'][] = $collectionAmount;
            $chartData['expenses'][] = $expenseAmount;
            $chartData['balance'][] = $monthBalance;
        }
        
        return view('accounting.index', compact(
            'totalCollections',
            'totalExpenses',
            'balance',
            'collectionsByCompany',
            'latestCollections',
            'latestExpenses',
            'chartData',
            'dateFrom',
            'dateTo'
        ));
    }
    
    /**
     * عرض تقرير الخزنة
     */
    public function treasuryReport(Request $request)
    {
        // الفترة الزمنية
        $dateFrom = $request->date_from ?? date('Y-m-01');
        $dateTo = $request->date_to ?? date('Y-m-t');
        
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
        
        // تجميع البيانات حسب اليوم
        $dailyData = [];
        $runningBalance = 0;
        
        // دمج التحصيلات والمصاريف وترتيبها حسب التاريخ
        $allTransactions = [];
        
        foreach ($collections as $collection) {
            $allTransactions[] = [
                'date' => $collection->collection_date->format('Y-m-d'),
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
        
        // ترتيب المعاملات حسب التاريخ
        usort($allTransactions, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });
        
        // حساب الرصيد التراكمي
        foreach ($allTransactions as &$transaction) {
            if ($transaction['type'] == 'collection') {
                $runningBalance += $transaction['amount'];
            } else {
                $runningBalance -= $transaction['amount'];
            }
            $transaction['running_balance'] = $runningBalance;
        }
        
        return view('accounting.treasury_report', compact(
            'allTransactions',
            'totalCollections',
            'totalExpenses',
            'balance',
            'dateFrom',
            'dateTo'
        ));
    }
}
