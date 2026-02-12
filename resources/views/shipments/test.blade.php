@extends('layouts.app')

@section('title', 'جدول الشحنات (نسخة جديدة)')

@section('content')
<div style="width: 100%; overflow-x: auto;">
    <table class="table table-bordered table-striped align-middle text-center" style="min-width: 1800px;">
        <thead class="table-light">
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>رقم التتبع</th>
                <th>العميل</th>
                <th>رقم الهاتف</th>
                <th>المحافظة</th>
                <th>العنوان</th>
                <th>المنتجات</th>
                <th>الإجمالي</th>
                <th>شركة الشحن</th>
                <th>المندوب</th>
                <th>الحالة</th>
                <th>تاريخ الشحن</th>
                <th>تاريخ الإرجاع</th>
                <th>باركود</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipments as $shipment)
                <tr>
                    <td><input type="checkbox" name="select[]" value="{{ $shipment->id }}"></td>
                    <td>{{ $shipment->order_code }}</td>
                    <td>{{ $shipment->customer_name }}</td>
                    <td>{{ $shipment->phone }}</td>
                    <td>{{ $shipment->governorate }}</td>
                    <td>{{ $shipment->address }}</td>
                    <td>
                        @foreach($shipment->products as $product)
                            <div>{{ $product->name }} (x{{ $product->pivot->quantity }})</div>
                        @endforeach
                    </td>
                    <td>{{ number_format($shipment->total, 2) }} ج.م</td>
                    <td>{{ optional($shipment->shippingCompany)->name ?? '-' }}</td>
                    <td>{{ optional($shipment->deliveryAgent)->name ?? '-' }}</td>
                    <td>{{ optional($shipment->status)->name ?? '-' }}</td>
                    <td>{{ $shipment->shipping_date ?? '-' }}</td>
                    <td>{{ $shipment->return_date ?? '-' }}</td>
                    <td>
                        @if($shipment->order_code)
                            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($shipment->order_code, 'C128') }}" height="30">
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('shipments.show', $shipment) }}" class="btn btn-sm btn-primary">عرض</a>
                        <a href="{{ route('shipments.edit', $shipment) }}" class="btn btn-sm btn-warning">تعديل</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
