<?php

namespace App\Http\Controllers;

use App\Imports\ShipmentsImport;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ShipmentImportController extends Controller
{
    public function form()
    {
        $companies = ShippingCompany::all();
        return view('shipments.import', compact('companies'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'shipping_company_id' => 'required|exists:shipping_companies,id'
        ]);

        try {
            Excel::import(new ShipmentsImport($request->shipping_company_id), $request->file('file'));
            return redirect()->route('shipments.index')->with('success', 'تم استيراد الشحنات بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'خطأ أثناء الاستيراد: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\ShipmentTemplateExport, 'shipments_template.xlsx');
    }
}
