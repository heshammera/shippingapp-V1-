<?php

namespace App\Http\Controllers;

use App\Exports\ShipmentsExport;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ShipmentActionController extends Controller
{
    public function printInvoices(Request $request)
    {
        // Filter logic can be added here if needed, currently assumes all or handled via query string
        $query = Shipment::query();

        if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            
            // Mark as printed only for Invoices
            Shipment::whereIn('id', $ids)->update([
                'is_printed' => true,
                'print_date' => now(),
            ]);
            
            $query->whereIn('id', $ids);
        } else {
             // If no specific IDs, maybe apply current filters from session or request? 
             // For now, let's just get latest 50 if no filter to avoid crash, or all?
             // User usually filters then clicks action.
             // If this is called from header action without selection, it might mean "All filtered"
             // But Filament actions usually pass context.
             
             // Simplest approach: If called via Filament Action, we might pass IDs or apply filters.
             // Let's assume we might receive a query string.
        }
        
        // Eager load for performance
        $shipments = $query->with(['products', 'shippingCompany', 'deliveryAgent', 'status'])
                           ->latest()
                           ->get();

        return view('print.invoice', compact('shipments'));
    }

    public function printTable(Request $request)
    {
        $query = Shipment::query();

         if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }

        $shipments = $query->with(['products', 'shippingCompany', 'deliveryAgent', 'status'])
                           ->latest()
                           ->get();

        return view('print.table', compact('shipments'));
    }

    public function printThermal(Request $request)
    {
        $query = Shipment::query();

         if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            
            // Mark as printed
            Shipment::whereIn('id', $ids)->update([
                'is_printed' => true,
                'print_date' => now(),
            ]);

            $query->whereIn('id', $ids);
        }

        $shipments = $query->with(['products', 'shippingCompany', 'deliveryAgent'])
                           ->latest()
                           ->get();

        return view('print.thermal_label', compact('shipments'));
    }

    public function export(Request $request)
    {
        $fileName = 'shipments_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new ShipmentsExport($request), $fileName);
    }
}
