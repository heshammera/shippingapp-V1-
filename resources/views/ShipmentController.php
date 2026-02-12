<?php



namespace App\Http\Controllers;



use App\Models\Shipment;

use App\Models\ShipmentStatus;

use App\Models\ShippingCompany;

use App\Models\DeliveryAgent;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;

use App\Imports\ShipmentsImport;

use Illuminate\Support\Str;

use App\Models\Product;

use App\Exports\ShipmentsPrintExport;

use App\Models\User;







class ShipmentController extends Controller

{

    



    



//public function exportPrintTable(Request $request)

//{

//    $ids = explode(',', $request->ids);

//    $shipments = \App\Models\Shipment::with(['status', 'deliveryAgent'])->whereIn('id', $ids)->get();

//

//    $now = now()->format('Y-m-d_H-i-s');

//    return \Maatwebsite\Excel\Facades\Excel::download(

//        new \App\Exports\ShipmentsPrintExport($shipments),

//        "جدول_الشحنات_{$now}.xlsx"

//    );

//    

//}

public function printTable(Request $request)

{

    $query = Shipment::with(['shippingCompany', 'deliveryAgent.user', 'status']);



    if ($request->filled('company')) {

        $query->where('shipping_company_id', $request->company);

    }



    if ($request->filled('status')) {

        $query->where('status_id', $request->status);

    }



    if ($request->filled('date_from')) {

        $query->whereDate('shipping_date', '>=', $request->date_from);

    }



    if ($request->filled('date_to')) {

        $query->whereDate('shipping_date', '<=', $request->date_to);

    }

    

    //if ($request->status_id == 1 && !$shipment->delivery_date) {

    //    $shipment->delivery_date = now();

    //}

    //

    //if ($request->status_id == 2 && !$shipment->return_date) {

    //    $shipment->return_date = now();

    //}



    if ($request->filled('search')) {

        $search = $request->search;

        $query->where(function ($q) use ($search) {

            $q->where('tracking_number', 'like', "%{$search}%")

              ->orWhere('customer_name', 'like', "%{$search}%")

              ->orWhere('phone', 'like', "%{$search}%")

              ->orWhere('product_name', 'like', "%{$search}%");

        });

    }

{

    $query = Shipment::with(['shippingCompany', 'deliveryAgent.user', 'status']); // ✅ أضف deliveryAgent هنا



    // باقي الفلاتر زي ما هي...



$shipments = $query->with('products')->paginate($paginationLimit);

    $statuses = ShipmentStatus::all();

    return view('shipments.print-table', compact('shipments', 'statuses'));

}

}  

    

    

    

public function printSelected(Request $request)

{

    $ids = explode(',', $request->query('ids'));



    $shipments = Shipment::with(['deliveryAgent', 'shippingCompany', 'status']) // ✅ مهم جداً

                         ->whereIn('id', $ids)

                         ->get();



    return view('shipments.print.table', compact('shipments'));

}





    

    

    

    

//    public function printSelected(Request $request)

//{

//    $ids = explode(',', $request->query('ids'));

//    $shipments = Shipment::whereIn('id', $ids)->get();

//    return view('shipments.print.table', compact('shipments'));

//    

//}







public function printInvoices(Request $request)

{

    $ids = explode(',', $request->query('ids'));



    // جلب الشحنات

    $shipments = Shipment::whereIn('id', $ids)->get();



    // تحديث حالة الطباعه

    Shipment::whereIn('id', $ids)->update(['is_printed' => true]);



    return view('shipments.print.invoices', compact('shipments'));

}









//public function printInvoices(Request $request)

//{

//    $ids = explode(',', $request->query('ids'));

//    $shipments = Shipment::whereIn('id', $ids)->get();

//    return view('shipments.print.invoices', compact('shipments'));

//}



    //public function printSelected(Request $request)

    //{

    //    $ids = explode(',', $request->ids);

    //    $shipments = Shipment::whereIn('id', $ids)->get();

    //    $statuses = ShipmentStatus::all();

//

    //    return view('shipments.print_selected', compact('shipments', 'statuses'));

    //}



    //public function printSelectedTable(Request $request)

    //{

    //    $ids = explode(',', $request->get('ids'));

    //    $shipments = Shipment::whereIn('id', $ids)->get();

    //    $statuses = ShipmentStatus::all();

//

    //    return view('shipments.print-table', compact('shipments', 'statuses'));

    //}



    //public function printInvoices(Request $request)

    //{

    //    $ids = explode(',', $request->ids);

    //    $shipments = Shipment::whereIn('id', $ids)->get();

    //    return view('shipments.print-invoices', compact('shipments'));

    //}

//

















public function index(Request $request)
{
    $query = Shipment::with(['shippingCompany', 'deliveryAgent', 'status', 'products']);

    if (auth()->user()->role === 'delivery_agent') {
        $query->where('delivery_agent_id', auth()->id());
    }

    if ($request->filled('company')) {
        $companyId = $request->company;
        $companyName = ShippingCompany::where('id', $companyId)->value('name');
        $query->where(function ($q) use ($companyId, $companyName) {
            $q->where('shipping_company_id', $companyId);
            if ($companyName) {
                $q->orWhere('shipping_company', 'like', "%{$companyName}%");
            }
        });
    }

    if ($request->filled('status')) {
        $query->where('status_id', $request->status);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('shipping_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('shipping_date', '<=', $request->date_to);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('tracking_number', 'like', "%{$search}%")
              ->orWhere('customer_name', 'like', "%{$search}%")
              ->orWhere('customer_phone', 'like', "%{$search}%")
              ->orWhere('product_name', 'like', "%{$search}%");
        });
    }

    if ($request->filled('agent')) {
        $query->where('delivery_agent_id', $request->agent);
    }

    if ($request->filled('printed')) {
        $query->where('is_printed', $request->printed);
    }

    // 🟢⬇️ خزن نسخة للكويري الأصلي قبل أي paginate
    $queryForTotals = (clone $query)->with('products');

    // 🟢⬇️ اعمل paginate للكويري الأصلي
    $paginationLimit = \App\Models\Setting::getValue('pagination_limit', $request->ajax() ? 100 : 15);
    $shipments = $query->with('products')->latest()->paginate($paginationLimit);

    // 🟢⬇️ احسب المجاميع من النسخة الأصلية
    $allFilteredShipments = $queryForTotals->get();

    $totalShipments = $allFilteredShipments->count();

    $totalPieces = $allFilteredShipments->sum(function ($shipment) {
        return $shipment->products->sum('pivot.quantity');
    });

    $totalAmountSum = $allFilteredShipments->sum(function ($shipment) {
        return $shipment->products->sum(function ($product) {
            return $product->pivot->quantity * $product->pivot->price;
        });
    });

    // لو الطلب Ajax (فلترة ديناميكية)
    if ($request->ajax()) {
        $tableHtml = view('shipments.partials.table', [
            'shipments' => $shipments,
            'statuses' => ShipmentStatus::all(),
            'shippingCompanies' => ShippingCompany::where('is_active', true)->get(),
            'deliveryAgents' => \App\Models\DeliveryAgent::where('is_active', true)->get(),
        ])->render();

        return response()->json([
            'success' => true,
            'table' => $tableHtml,
            'total_shipments' => $totalShipments,
            'total_pieces' => $totalPieces,
            'total_amount_sum' => number_format($totalAmountSum),
        ]);
    }

    // تحميل باقي البيانات
    $companies = ShippingCompany::where('is_active', true)->orderBy('name')->get();
    $shippingCompanies = $companies;
    $statuses = ShipmentStatus::orderBy('name')->get();
    $products = Product::all();
    $deliveryAgents = \App\Models\DeliveryAgent::where('is_active', true)->get();

    return view('shipments.index', compact(
        'shipments',
        'companies',
        'statuses',
        'products',
        'shippingCompanies',
        'deliveryAgents',
        'totalShipments',
        'totalPieces',
        'totalAmountSum'
    ));
}













































//public function index(Request $request)

//{

//    

//    //$query = Shipment::with(['shippingCompany', 'deliveryAgent', 'status']);

//

//    // فلترة حسب شركة الشحن

//    

//    $shipments = Shipment::with('shippingCompany')->latest()->paginate(20);

//    $shippingCompanies = \App\Models\ShippingCompany::where('is_active', true)->get();

//    $statuses = ShipmentStatus::all();

//    

//

//    $query = Shipment::query();

//

//    if (auth()->user()->role === 'delivery_agent') {

//        // يعرض فقط شحنات هذا المندوب

//        $query->where('delivery_agent_id', auth()->id());

//    }

//

//

//// فلترة شركة الشحن

//if ($request->has('company')) {

//    if ($request->company === 'null') {

//        $query->whereNull('shipping_company_id');

//    } elseif ($request->company !== '') {

//        $query->where('shipping_company_id', $request->company);

//    }

//}

//

//// فلترة الحالة

//if ($request->has('status')) {

//    if ($request->status === 'null') {

//        $query->whereNull('status_id');

//    } elseif ($request->status !== '') {

//        $query->where('status_id', $request->status);

//    }

//}

//

//

//        // فلترة حسب هل تم الطباعة أم لا

//if ($request->filled('printed')) {

//    $query->where('is_printed', $request->printed);

//}

//    

//

//

//    // فلترة حسب التاريخ

//    if ($request->filled('date_from')) {

//        $query->whereDate('shipping_date', '>=', $request->date_from);

//    }

//

//    if ($request->filled('date_to')) {

//        $query->whereDate('shipping_date', '<=', $request->date_to);

//    }

//

//if ($request->filled('search')) {

//    $search = $request->search;

//    $query->where(function ($q) use ($search) {

//        $q->where('tracking_number', 'like', "%{$search}%")

//          ->orWhere('customer_name', 'like', "%{$search}%")

//          ->orWhere('customer_phone', 'like', "%{$search}%") // ✅ بحث برقم التليفون

//          ->orWhere('product_name', 'like', "%{$search}%");

//    });

//}

//

//

//$paginationLimit = \App\Models\Setting::getValue('pagination_limit', 15);

//$shipments = $query->latest()->paginate($paginationLimit);

//

//

//    $companies = ShippingCompany::where('is_active', true)->orderBy('name')->get();

//    $statuses = ShipmentStatus::orderBy('name')->get();

//    $products = Product::all();

//    $shippingCompanies = $companies;

//    $shippingCompaniesList = $companies->pluck('name')->toArray();

//

//    if ($request->ajax()) {

//        $totalPieces = $query->sum('quantity'); // مجموع الكميات

//$totalShipments = $query->count();      // عدد الشحنات بعد الفلترة

//

//return response()->json([

//    'table' => view('shipments.partials.table', compact('shipments', 'statuses', 'shippingCompanies'))->render(),

//    'total_shipments' => $totalShipments,

//    'total_pieces' => $totalPieces,

//]);

//

//

//    }

//$deliveryAgents = User::where('role', 'delivery_agent')->get();

//

//    return view('shipments.index', compact(

//       'shipments', 'companies', 'statuses', 'products', 'shippingCompanies', 'shippingCompaniesList', 'statuses', 'deliveryAgents'

//    ));

//}











    public function create()

    {

        $products = Product::all();

        $trackingNumber = $this->generateTrackingNumber();

        $companies = ShippingCompany::where('is_active', true)->get();

        $agents = DeliveryAgent::where('is_active', true)->get();

        $statuses = ShipmentStatus::all();

        

    $governorates = [

        'القاهرة',

        'الجيزة',

        'القليوبية',

        'الإسكندرية',

        'الإسماعيلية',

        'السويس',

        'الغربية',

        'الشرقية',

        'الدقهلية',

        'المنوفية',

        'الفيوم',

        'بني سويف',

        'أسيوط',

        'سوهاج',

        'قنا',

        'الأقصر',

        'أسوان',

        'البحر الأحمر',

        'مطروح',

        'دمياط',

        'بورسعيد',

        'شمال سيناء',

        'جنوب سيناء',

        'كفر الشيخ',

        'المنيا',

        'الوادي الجديد',

        'البحيرة'



    ];





        return view('shipments.create', compact('trackingNumber', 'companies', 'agents', 'statuses', 'products', 'governorates'));

    }











public function store(Request $request)

{

    $validated = $request->validate([

        'shipping_company_id' => 'nullable|exists:shipping_companies,id',

        'customer_name' => 'required|string|max:255',

        'customer_phone' => 'nullable|string|max:255',

        'customer_address' => 'required|string|max:255',

        'governorate' => 'required|string|max:255',

        'shipping_price' => 'required|numeric|min:0',

        'total_amount' => 'required|numeric|min:0',

        'notes' => 'nullable|string',



        // منتجات متعددة

        'products' => 'required|array|min:1',

        'products.*.product_id' => 'required|exists:products,id',

        'products.*.color' => 'required|string|max:255',

        'products.*.size' => 'required|string|max:255',

        'products.*.quantity' => 'required|integer|min:1',

        'products.*.price' => 'required|numeric|min:0',

    ]);



    $companyName = $validated['shipping_company_id']

        ? ShippingCompany::find($validated['shipping_company_id'])?->name

        : null;



    // إنشاء الشحنة الأساسية

    $shipment = Shipment::create([

        'tracking_number' => $this->generateTrackingNumber(),

        'customer_name' => $validated['customer_name'],

        'customer_phone' => $validated['customer_phone'],

        'customer_address' => $validated['customer_address'],

        'governorate' => $validated['governorate'],

        'shipping_price' => $validated['shipping_price'],

        'total_amount' => $validated['total_amount'],

        'status_id' => 34,

        'notes' => $validated['notes'],

        'shipping_company_id' => $validated['shipping_company_id'],

        'shipping_company' => $companyName,

    ]);



    // حفظ كل منتج مرتبط بالشحنة

    foreach ($validated['products'] as $product) {

        $shipment->products()->attach($product['product_id'], [

            'color' => $product['color'],

            'size' => $product['size'],

            'quantity' => $product['quantity'],

            'price' => $product['price'],

        ]);

    }



    if (auth()->user()->role == 'moderator') {

        return view('shipments.moderator-success');

    }



    return redirect()->route('shipments.index')->with('success', 'تم إضافة الشحنة بنجاح!');

}











    protected function generateTrackingNumber()

    {

        do {

            $trackingNumber = strtoupper('TRK' . Str::random(8));

        } while (Shipment::where('tracking_number', $trackingNumber)->exists());



        return $trackingNumber;

    }









    public function show(Shipment $shipment)

    {

        //$shipment = Shipment::findOrFail($id);

        $shipment->load('products'); // لو عامل علاقة products() في Shipment

        $statuses = ShipmentStatus::all();

        $companies = ShippingCompany::all();

        return view('shipments.show', compact('shipment', 'statuses', 'companies'));

    }







public function edit(Shipment $shipment)

{

    $products = Product::all();

    $statuses = ShipmentStatus::all();

    $companies = ShippingCompany::where('is_active', true)->get();

    $deliveryAgents = DeliveryAgent::where('is_active', 1)->get();



    

    $governorates = [

        'القاهرة', 'الجيزة', 'القليوبية', 'الإسكندرية', 'الإسماعيلية', 'السويس',

        'الغربية', 'الشرقية', 'الدقهلية', 'المنوفية', 'الفيوم', 'بني سويف',

        'أسيوط', 'سوهاج', 'قنا', 'الأقصر', 'أسوان', 'البحر الأحمر', 'مطروح',

        'دمياط', 'بورسعيد', 'شمال سيناء', 'جنوب سيناء', 'كفر الشيخ', 'المنيا', 'الوادي الجديد', 'البحيرة'



    ];

   // 🟢 ضيف السطر هنا

    $shipment->load('products');

    

    return view('shipments.edit', compact('shipment', 'products', 'statuses', 'companies', 'governorates', 'deliveryAgents'));

}





  



public function updateShippingCompany(Request $request, Shipment $shipment)

{

    $request->validate([

        'shipping_company_id' => 'nullable|exists:shipping_companies,id',

    ]);



    $company = ShippingCompany::find($request->shipping_company_id);



    if (!$company) {

        return response()->json(['success' => false, 'message' => 'شركة الشحن غير موجودة']);

    }



    // إذا كانت شركة الشحن قد تغيرت، قم بتعيين المندوب إلى "غير محدد"

    $shipment->shipping_company = $company->name;

    $shipment->shipping_company_id = $request->shipping_company_id;

    $shipment->delivery_agent_id = null;  // تعيين المندوب إلى "غير محدد"

    $shipment->delivery_agent_name = null; // تعيين المندوب إلى "غير محدد"



    $shipment->save();



    return response()->json([

        'success' => true,

        'message' => 'تم تحديث شركة الشحن بنجاح',

        'shipping_company' => $company->name

    ]);

}

















public function update(Request $request, Shipment $shipment)

{

    $validated = $request->validate([

        'tracking_number' => 'required|string|max:255',

        'shipping_company_id' => 'required|exists:shipping_companies,id',

        'customer_name' => 'required|string|max:255',

        'customer_phone' => 'nullable|string|max:255',

        'customer_address' => 'required|string|max:255',

        'product_description' => 'nullable|string',

        'status_id' => 'required|exists:shipment_statuses,id',

        'shipping_price' => 'required|numeric|min:0',

        'delivery_date' => 'nullable|date',

        'return_date' => 'nullable|date',

        'shipping_date' => 'nullable|date',

        'delivery_agent_id' => 'nullable|exists:delivery_agents,id',

        'notes' => 'nullable|string',

        'agent_notes' => 'nullable|string',

        'governorate' => 'required|string|max:255',

        'total_amount' => 'required|numeric|min:0',



        // ✅ المنتجات المتعددة

        'products' => 'required|array',

        'products.*.product_id' => 'required|exists:products,id',

        'products.*.color' => 'nullable|string',

        'products.*.size' => 'nullable|string',

        'products.*.quantity' => 'required|integer|min:1',

        'products.*.price' => 'required|numeric|min:0',

    ]);



    $company = \App\Models\ShippingCompany::findOrFail($validated['shipping_company_id']);

    $companyName = $company->name;



    $shipment->update([

        'tracking_number'       => $validated['tracking_number'],

        'customer_name'         => $validated['customer_name'],

        'customer_phone'        => $validated['customer_phone'] ?? null,

        'customer_address'      => $validated['customer_address'],

        'governorate'           => $validated['governorate'],

        'shipping_price'        => $validated['shipping_price'],

        'total_amount'          => $validated['total_amount'],

        'notes'                 => $validated['notes'] ?? null,

        'agent_notes'           => $validated['agent_notes'] ?? null,

        'shipping_company_id'   => $validated['shipping_company_id'],

        'shipping_company'      => $companyName,

        'status_id'             => $validated['status_id'],

        'delivery_agent_id'     => $validated['delivery_agent_id'] ?? null,

        'delivery_date'         => $validated['delivery_date'] ?? null,

        'return_date'           => $validated['return_date'] ?? null,

        'shipping_date'         => $validated['shipping_date'] ?? null,

    ]);



    // 🧨 احذف القديم

    $shipment->products()->detach();



    // 🔁 أضف الجديد

    foreach ($validated['products'] as $product) {

        $shipment->products()->attach($product['product_id'], [

            'color' => $product['color'],

            'size' => $product['size'],

            'quantity' => $product['quantity'],

            'price' => $product['price'],

        ]);

    }



    return redirect()->route('shipments.index')->with('success', 'تم تحديث الشحنة بنجاح!');

}

















public function quickUpdate(Request $request, Shipment $shipment)

{

    $request->validate([

        'field' => 'required|string',

        'value' => 'nullable',

    ]);



    try {

        $shipment->{$request->field} = $request->value;

        $shipment->save();



        return response()->json([

            'success' => true,

            'color' => $shipment->status->color ?? 'table-secondary',

            'label' => $shipment->status->name ?? 'غير محدد',

        ]);



    } catch (\Exception $e) {

        return response()->json([

            'success' => false,

            'message' => $e->getMessage(),

        ], 500);

    }

}









    // ... باقي الدوال كما هي



public function destroyQuick(Shipment $shipment)

{

    $shipment->delete();

    return redirect()->route('shipments.index')->with('success', 'تم حذف الشحنة بنجاح');

}





    public function destroy(Shipment $shipment)

    {

        $shipment->delete();

        return redirect()->route('shipments.index')->with('success', 'تم حذف الشحنة بنجاح');

    }



    public function importForm()

    {

        $companies = ShippingCompany::where('is_active', true)->get();

        return view('shipments.import', compact('companies'));

    }



    public function import(Request $request)

{

    $request->validate([

        'file' => 'required|file|mimes:xlsx,xls,csv',

        'shipping_company_id' => 'required|exists:shipping_companies,id',

    ]);



    try {

        Excel::import(new ShipmentsImport($request->shipping_company_id), $request->file('file'));



        return redirect()->route('shipments.index')->with('success', 'تم استيراد الشحنات بنجاح!');

    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

        $failures = $e->failures();

        $message = 'حدث خطأ في بعض الصفوف:';

        foreach ($failures as $failure) {

            $message .= "<br>الصف {$failure->row()}: " . implode(', ', $failure->errors());

        }



        return redirect()->back()->with('error', $message);

    } catch (\Exception $e) {

        return redirect()->back()->with('error', 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage());

    }

}

public function updateReturnDate(Request $request, Shipment $shipment)

{

    $shipment->return_date = $request->return_date;

    $shipment->save();



    return response()->json(['success' => true]);

}



    public function updateStatus(Request $request, Shipment $shipment)

    {

        $request->validate([

            'status_id' => 'required|exists:shipment_statuses,id',

            'agent_notes' => 'nullable|string',

        ]);



        $shipment->edit_count += 1;

        $shipment->status_id = $request->status_id;

        



        if ($request->filled('agent_notes')) {

            $shipment->agent_notes = $request->agent_notes;

        }



        if ($request->status_id == 1) {

            $shipment->delivery_date = now();

        } elseif ($request->status_id == 2) {

            $shipment->return_date = now();

        }

if (!$request->isMethod('put') && !$request->isMethod('post')) {

    abort(405, 'Method Not Allowed');

}





        $shipment->save();



        return redirect()->back()->with('success', 'تم تحديث حالة الشحنة بنجاح');

    }

    

    

    

    

    public function exportPrint(Request $request)

{

    $ids = explode(',', $request->ids);



    $shipments = Shipment::with(['products', 'shippingCompany', 'deliveryAgent', 'status'])

                         ->whereIn('id', $ids)

                         ->get();



    $now = now()->format('Y_m_d_H_i_s');



    $filename = 'شحنات_' . now()->format('Y_m_d_H_i_s') . '.xlsx';



    return \Maatwebsite\Excel\Facades\Excel::download(

        new \App\Exports\ShipmentsPrintExport($shipments),

        $filename

    );

}









public function updateDelivery(Request $request, Shipment $shipment)

{

    if (auth()->user()->id !== $shipment->delivery_agent_id) {

        abort(403);

    }



    $shipment->update($request->only(['status_id', 'delivered_at', 'agent_notes']));

    return redirect()->back()->with('success', 'تم التحديث بنجاح');

}

public function updateDeliveryDetails(Request $request, Shipment $shipment)

{

    if (auth()->user()->role !== 'delivery_agent') {

        abort(403);

    }



    $shipment->update([

        'status_id' => $request->status_id,

        'delivery_date' => $request->delivery_date,

        'agent_notes' => $request->agent_notes,

    ]);

    $shipment->update($validated); // أو تحديث يدوي للحقول



 // 🧨 احذف المنتجات القديمة

    $shipment->products()->detach();



    // 🔁 اربط المنتجات الجديدة

    foreach ($validated['products'] as $item) {

        $shipment->products()->attach($item['product_id'], [

            'color' => $item['color'],

            'size' => $item['size'],

            'quantity' => $item['quantity'],

            'price' => $item['price'],

        ]);

    }

    return redirect()->route('shipments.show', $shipment)->with('success', 'تم تحديث بيانات الشحنة.');

}

public function bulkDelete(Request $request)

{

    $ids = explode(',', $request->ids);



    if (empty($ids)) {

        return redirect()->back()->with('error', 'لم يتم تحديد أي شحنات للحذف.');

    }



    try {

        \App\Models\Shipment::whereIn('id', $ids)->delete();

        return redirect()->route('shipments.index')->with('success', 'تم حذف الشحنات المحددة بنجاح.');

    } catch (\Exception $e) {

        return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());

    }

}



public function assignAgent(Request $request, Shipment $shipment)

{

    $request->validate([

        'delivery_agent_id' => 'nullable|exists:delivery_agents,id',

    ]);



    $shipment->delivery_agent_id = $request->delivery_agent_id;



    // ✅ جلب اسم المندوب لو تم اختياره

    if ($request->delivery_agent_id) {

        $agent = \App\Models\DeliveryAgent::find($request->delivery_agent_id);

        $shipment->delivery_agent_name = $agent?->name;

    } else {

        $shipment->delivery_agent_name = null;

    }



    $shipment->save();



    return response()->json(['success' => true]);

}





















    

}





