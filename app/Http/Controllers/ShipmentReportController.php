<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment; // مثال، عدل حسب موديلك
use Maatwebsite\Excel\Facades\Excel; // لو مستخدم مكتبة Excel
use PDF; // لو مستخدم مكتبة DomPDF

class ShipmentReportController extends Controller
{
  public function index(Request $request)
{
    $query = Shipment::with(['shippingCompany', 'deliveryAgent', 'status', 'product']);

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
        $query->whereDate('shipping_date', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('shipping_date', '<=', $request->date_to);
    }

    $shipments = Shipment::with('product')
    ->paginate(20)
    ->appends($request->query());


    $totalShipments = $query->count();
    $totalCost = $query->sum('cost_price');
    $totalSelling = $query->sum('selling_price');
    $totalProfit = $totalSelling - $totalCost;

    $shippingCompanies = \App\Models\ShippingCompany::all();
    $shipmentStatuses = \App\Models\ShipmentStatus::all();
    $deliveryAgents = \App\Models\DeliveryAgent::all();

    return view('reports.shipments.index', compact(
        'shipments',
        'totalShipments',
        'totalCost',
        'totalSelling',
        'totalProfit',
        'shippingCompanies',
        'shipmentStatuses',
        'deliveryAgents'
    ));
}




protected function applyFilters($query, $filters)
{
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
        $query->whereDate('shipping_date', '>=', $filters['date_from']);
    }
    if (!empty($filters['date_to'])) {
        $query->whereDate('shipping_date', '<=', $filters['date_to']);
    }
    return $query;
}


    public function exportExcel(Request $request)
    {
        // هنا تستخدم مكتبة Excel مثل maatwebsite/excel 
        // تبني ملف Excel حسب الفلاتر ثم ترجع للتحميل

        $query = Shipment::query();
        // كرر نفس الفلاتر كما في index() أو عامل دالة مشتركة

        // مثال بسيط (تحتاج تهيئة Export class حقيقي)
        // return Excel::download(new ShipmentsExport($query), 'shipments.xlsx');

        return response('Excel export not implemented yet.', 501);
    }

public function print(Request $request)
{
    $filters = $request->all();

    $query = Shipment::with([
        'products', // مهم عشان pivot
        'shippingCompany',
        'deliveryAgent',
        'status'
    ]);

    // هنا لو عندك فلاتر طبقها
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
        $query->whereDate('shipping_date', '>=', $filters['date_from']);
    }
    if (!empty($filters['date_to'])) {
        $query->whereDate('shipping_date', '<=', $filters['date_to']);
    }

    $shipments = $query->get();

    return view('reports.shipments.print', compact('shipments', 'filters'));
}
public function exportPdf(Request $request)
{
    
        $viewPath = view()->getFinder()->find('reports.shipments_pdf');
    dd($viewPath); // ده هيطبع المسار الكامل للملف
    
    $filters = $request->all();

    $query = Shipment::with([
        'products',
        'shippingCompany',
        'deliveryAgent',
        'status'
    ]);

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
        $query->whereDate('shipping_date', '>=', $filters['date_from']);
    }
    if (!empty($filters['date_to'])) {
        $query->whereDate('shipping_date', '<=', $filters['date_to']);
    }

    $shipments = $query->get();

    // حساب إجمالي سعر الشحن
    $totalShippingCost = $shipments->sum(function($shipment) {
        return $shipment->shipping_price ?? 0;
    });

    // حساب إجمالي المبلغ (سعر الوحدة * الكمية + سعر الشحن)
    $totalAmount = $shipments->sum(function($shipment) {
        $unitPrice = $shipment->selling_price;
        $quantity = $shipment->quantity;
        $shippingCost = $shipment->shipping_price ?? 0;
        return ($unitPrice * $quantity) + $shippingCost;
    });

$pdf = PDF::loadView('reports.shipments_pdf', compact('shipments', 'filters', 'totalShippingCost', 'totalAmount'))
          ->setPaper('a4', 'landscape');


    return $pdf->download('shipments_report.pdf');
}




}
