@extends('layouts.app')

@section('title', 'تفاصيل التحصيل')

@section('actions')
    <a href="{{ route('collections.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-right"></i> العودة للتحصيلات
    </a>
    <a href="{{ route('collections.edit', $collection) }}" class="btn btn-sm btn-primary">
        <i class="bi bi-pencil"></i> تعديل
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">تفاصيل التحصيل</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">رقم التحصيل</th>
                        <td>{{ $collection->id }}</td>
                    </tr>
                    <tr>
                        <th>شركة الشحن</th>
                        <td>{{ $collection->shippingCompany->name ?? 'غير معروف' }}</td>

                    </tr>
                    <tr>
                        <th>المبلغ</th>
                        <td>{{ $collection->amount }} جنيه</td>
                    </tr>
                    <tr>
                        <th>تاريخ التحصيل</th>
                        <td>{{ $collection->collection_date->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <th>ملاحظات</th>
                        <td>{{ $collection->notes ?? 'لا توجد ملاحظات' }}</td>
                    </tr>
                    <tr>
                        <th>تم بواسطة</th>
                        <td>{{ $collection->createdBy->name }}</td>
                    </tr>
                    <tr>
                        <th>تاريخ الإنشاء</th>
                        <td>{{ $collection->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    <tr>
                        <th>آخر تحديث</th>
                        <td>{{ $collection->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">معلومات شركة الشحن</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">اسم الشركة</th>
                                <td>{{ $collection->shippingCompany->name ?? 'غير معروف' }}</td>
                            </tr>
                            <tr>
                                <th>جهة الاتصال</th>
                                <td>{{ $collection->shippingCompany->contact_person ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <th>رقم الهاتف</th>
                                <td>{{ $collection->shippingCompany->phone ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <th>البريد الإلكتروني</th>
                                <td>{{ $collection->shippingCompany->email ?? 'غير محدد' }}</td>
                            </tr>
                        </table>
                        
                        <div class="mt-3">
                            
                            @if ($collection->shippingCompany)
    <a href="{{ route('shipping-companies.show', $collection->shippingCompany->id) }}" class="btn btn-sm btn-outline-primary">
        {{ $collection->shippingCompany->name }}
        <i class="bi bi-eye"></i> عرض تفاصيل الشركة
    </a>
@else
    <span class="text-muted">لا توجد شركة شحن</span>
@endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
