<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShipmentsPrintExport;

class ShipmentExportController extends Controller
{
    public function export(Request $request)
    {
        $ids = explode(',', $request->ids);
        $shipments = Shipment::with(['status', 'deliveryAgent'])->whereIn('id', $ids)->get();
        $now = now()->format('Y-m-d_H-i-s');
        return Excel::download(new ShipmentsPrintExport($shipments), "جدول_الشحنات_{$now}.xlsx");
    }

    public function exportPrint(Request $request)
    {
        $ids = explode(',', $request->ids);
        $shipments = Shipment::with(['status', 'deliveryAgent'])->whereIn('id', $ids)->get();
        $filename = 'شحنات_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new ShipmentsPrintExport($shipments), $filename);
    }
}
