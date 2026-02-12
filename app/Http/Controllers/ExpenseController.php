<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * عرض قائمة المصاريف
     */
    public function index(Request $request)
    {
        $query = Expense::with(['createdBy']);
        
        // تصفية حسب التاريخ
        if ($request->has('date_from') && $request->date_from) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->where('expense_date', '<=', $request->date_to);
        }
        
        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15);
        
        return view('expenses.index', compact('expenses'));
    }

    /**
     * عرض نموذج إنشاء مصروف جديد
     */
    public function create()
    {


        return view('expenses.create');
    }

    /**
     * حفظ مصروف جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);
        
        return redirect()->route('expenses.index')
            ->with('success', 'تم إضافة المصروف بنجاح');
    }

    /**
     * عرض تفاصيل المصروف
     */
    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    /**
     * عرض نموذج تعديل المصروف
     */
    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    /**
     * تحديث المصروف
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        $expense->update([
            'title' => $request->title,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'notes' => $request->notes,
        ]);
        
        return redirect()->route('expenses.index')
            ->with('success', 'تم تحديث المصروف بنجاح');
    }

    /**
     * حذف المصروف
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();
        
        return redirect()->route('expenses.index')
            ->with('success', 'تم حذف المصروف بنجاح');
    }
    
    /**
     * عرض تقرير المصاريف
     */
    public function report(Request $request)
    {
        $query = Expense::with(['createdBy']);
        
        // تصفية حسب التاريخ
        if ($request->has('date_from') && $request->date_from) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->where('expense_date', '<=', $request->date_to);
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
        
        return view('expenses.report', compact('expenses', 'total', 'expensesByMonth'));
    }
}
