<?php

namespace App\Http\Controllers;

use App\Models\ShipmentStatus;
use Illuminate\Http\Request;

class ShipmentStatusController extends Controller
{
    public function index()
    {
        $statuses = ShipmentStatus::orderBy('sort_order')->get();
        return view('shipment_statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('shipment_statuses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'required|integer',
        ]);


ShipmentStatus::create([
    'name' => $request->name,
    'sort_order' => $request->sort_order,
    'color' => $request->color,
]);
        //ShipmentStatus::create($request->only(['name', 'sort_order']));

        return redirect()->route('shipment-statuses.index')->with('success', 'تمت إضافة الحالة بنجاح');
    }

public function edit($id)
{
        $shipmentStatus = \App\Models\ShipmentStatus::findOrFail($id);

    // متغير الألوان اللي هيتبعت للعرض
$availableColors = [
    'table-success',
    'table-warning',
    'table-danger',
    'table-primary',
    'table-info',
    'table-secondary',
    'table-light',
    'table-dark',
    'table-pink',
    'table-orange',
    'table-purple',
];


    return view('shipment_statuses.edit', compact('shipmentStatus', 'availableColors'));
}


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'required|integer',
            'color' => 'nullable|string|max:50',
        ]);

        $status = ShipmentStatus::findOrFail($id);
        $status->update($request->only(['name', 'sort_order', 'color']));

        return redirect()->route('shipment-statuses.index')->with('success', 'تم تحديث الحالة بنجاح');
    }

    public function destroy($id)
    {
        $status = ShipmentStatus::findOrFail($id);
        $status->delete();

        return redirect()->route('shipment-statuses.index')->with('success', 'تم حذف الحالة');
    }
}
