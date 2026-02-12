<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Assets (الأصول)
        $assets = ChartOfAccount::create([
            'code' => '1000', 'name_ar' => 'الأصول', 'name_en' => 'Assets', 'type' => 'asset', 'nature' => 'debit', 'level' => 1
        ]);
            // 1100 Current Assets
            $currentAssets = ChartOfAccount::create([
                'code' => '1100', 'name_ar' => 'الأصول المتداولة', 'name_en' => 'Current Assets', 'parent_id' => $assets->id, 'type' => 'asset', 'nature' => 'debit', 'level' => 2
            ]);
                ChartOfAccount::create(['code' => '1101', 'name_ar' => 'النقدية بالخزينة', 'name_en' => 'Cash on Hand', 'parent_id' => $currentAssets->id, 'type' => 'asset', 'nature' => 'debit', 'level' => 3]);
                ChartOfAccount::create(['code' => '1102', 'name_ar' => 'الينك - جاري', 'name_en' => 'Bank - Current', 'parent_id' => $currentAssets->id, 'type' => 'asset', 'nature' => 'debit', 'level' => 3]);
                ChartOfAccount::create(['code' => '1103', 'name_ar' => 'العملاء (المدينون)', 'name_en' => 'Accounts Receivable', 'parent_id' => $currentAssets->id, 'type' => 'asset', 'nature' => 'debit', 'level' => 3]);
                ChartOfAccount::create(['code' => '1104', 'name_ar' => 'المخزون', 'name_en' => 'Inventory', 'parent_id' => $currentAssets->id, 'type' => 'asset', 'nature' => 'debit', 'level' => 3]);
                ChartOfAccount::create(['code' => '1105', 'name_ar' => 'أرصدة مدينة أخرى', 'name_en' => 'Other Receivables', 'parent_id' => $currentAssets->id, 'type' => 'asset', 'nature' => 'debit', 'level' => 3]);

            // 1200 Fixed Assets
            $fixedAssets = ChartOfAccount::create([
                'code' => '1200', 'name_ar' => 'الأصول الثابتة', 'name_en' => 'Fixed Assets', 'parent_id' => $assets->id, 'type' => 'asset', 'nature' => 'debit', 'level' => 2
            ]);
                ChartOfAccount::create(['code' => '1201', 'name_ar' => 'السيارات ووسائل النقل', 'name_en' => 'Vehicles', 'parent_id' => $fixedAssets->id, 'type' => 'asset', 'nature' => 'debit', 'level' => 3]);
                ChartOfAccount::create(['code' => '1202', 'name_ar' => 'الأثاث والمعدات', 'name_en' => 'Furniture & Equipment', 'parent_id' => $fixedAssets->id, 'type' => 'asset', 'nature' => 'debit', 'level' => 3]);

        // 2. Liabilities (الخصوم)
        $liabilities = ChartOfAccount::create([
            'code' => '2000', 'name_ar' => 'الخصوم', 'name_en' => 'Liabilities', 'type' => 'liability', 'nature' => 'credit', 'level' => 1
        ]);
            // 2100 Current Liabilities
            $currentLiabilities = ChartOfAccount::create([
                'code' => '2100', 'name_ar' => 'الخصوم المتداولة', 'name_en' => 'Current Liabilities', 'parent_id' => $liabilities->id, 'type' => 'liability', 'nature' => 'credit', 'level' => 2
            ]);
                ChartOfAccount::create(['code' => '2101', 'name_ar' => 'الموردين (الدائنون)', 'name_en' => 'Accounts Payable', 'parent_id' => $currentLiabilities->id, 'type' => 'liability', 'nature' => 'credit', 'level' => 3]);
                ChartOfAccount::create(['code' => '2102', 'name_ar' => 'ضريبة القيمة المضافة المستحقة', 'name_en' => 'VAT Payable', 'parent_id' => $currentLiabilities->id, 'type' => 'liability', 'nature' => 'credit', 'level' => 3]);

        // 3. Equity (حقوق الملكية)
        $equity = ChartOfAccount::create([
            'code' => '3000', 'name_ar' => 'حقوق الملكية', 'name_en' => 'Equity', 'type' => 'equity', 'nature' => 'credit', 'level' => 1
        ]);
            ChartOfAccount::create(['code' => '3100', 'name_ar' => 'رأس المال', 'name_en' => 'Capital', 'parent_id' => $equity->id, 'type' => 'equity', 'nature' => 'credit', 'level' => 2]);
            ChartOfAccount::create(['code' => '3200', 'name_ar' => 'الأرباح المحتجزة', 'name_en' => 'Retained Earnings', 'parent_id' => $equity->id, 'type' => 'equity', 'nature' => 'credit', 'level' => 2]);

        // 4. Revenue (الإيرادات)
        $revenue = ChartOfAccount::create([
            'code' => '4000', 'name_ar' => 'الإيرادات', 'name_en' => 'Revenue', 'type' => 'revenue', 'nature' => 'credit', 'level' => 1
        ]);
            ChartOfAccount::create(['code' => '4100', 'name_ar' => 'إيرادات المبيعات/الشحن', 'name_en' => 'Sales/Shipping Revenue', 'parent_id' => $revenue->id, 'type' => 'revenue', 'nature' => 'credit', 'level' => 2]);
            ChartOfAccount::create(['code' => '4200', 'name_ar' => 'إيرادات أخرى', 'name_en' => 'Other Revenue', 'parent_id' => $revenue->id, 'type' => 'revenue', 'nature' => 'credit', 'level' => 2]);

        // 5. Expenses (المصروفات)
        $expenses = ChartOfAccount::create([
            'code' => '5000', 'name_ar' => 'المصروفات', 'name_en' => 'Expenses', 'type' => 'expense', 'nature' => 'debit', 'level' => 1
        ]);
            $opex = ChartOfAccount::create([
                'code' => '5100', 'name_ar' => 'مصروفات التشغيل', 'name_en' => 'Operating Expenses', 'parent_id' => $expenses->id, 'type' => 'expense', 'nature' => 'debit', 'level' => 2
            ]);
                ChartOfAccount::create(['code' => '5101', 'name_ar' => 'الرواتب والأجور', 'name_en' => 'Salaries and Wages', 'parent_id' => $opex->id, 'type' => 'expense', 'nature' => 'debit', 'level' => 3]);
                ChartOfAccount::create(['code' => '5102', 'name_ar' => 'الإيجار', 'name_en' => 'Rent', 'parent_id' => $opex->id, 'type' => 'expense', 'nature' => 'debit', 'level' => 3]);
                ChartOfAccount::create(['code' => '5103', 'name_ar' => 'كهرباء ومياه', 'name_en' => 'Utilities', 'parent_id' => $opex->id, 'type' => 'expense', 'nature' => 'debit', 'level' => 3]);
                ChartOfAccount::create(['code' => '5104', 'name_ar' => 'صيانة واصلاح', 'name_en' => 'Repairs and Maintenance', 'parent_id' => $opex->id, 'type' => 'expense', 'nature' => 'debit', 'level' => 3]);
                
            $cogs = ChartOfAccount::create([
                'code' => '5200', 'name_ar' => 'تكلفة المبيعات', 'name_en' => 'Cost of Goods Sold', 'parent_id' => $expenses->id, 'type' => 'expense', 'nature' => 'debit', 'level' => 2
            ]);
    }
}
