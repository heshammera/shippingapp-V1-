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
use Illuminate\Validation\ValidationException;

use App\Models\Setting;
use App\Models\Inventory;




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

    // تحقق من صحة $ids
    if (empty($ids)) {
        return redirect()->back()->with('error', 'لم يتم تحديد شحنات للطباعة.');
    }

    // جلب الشحنات للطباعة
    $shipments = Shipment::whereIn('id', $ids)->get();

    // تحديث حالة الطباعة مع تاريخ الطباعة مرة واحدة فقط
    Shipment::whereIn('id', $ids)->update([
        'is_printed' => true,
        'print_date' => now(),
    ]);

    return view('shipments.print.invoices', compact('shipments'));
}


public function index(Request $request)
{
    
    // sanitizing dates (strip time if exists) because HTML5 date inputs fail with time
    if ($request->filled('date_from')) {
        $request->merge(['date_from' => substr($request->date_from, 0, 10)]);
    }
    if ($request->filled('date_to')) {
        $request->merge(['date_to' => substr($request->date_to, 0, 10)]);
    }
$user = auth()->user();
$query = \App\Models\Shipment::with(['shippingCompany', 'deliveryAgent', 'status', 'products']);

if ($user->role === 'shipping_agent') {
    // فلترة شحنات وكيل الشحن بحيث يشوف بس شحنات شركته
    $query->where('shipping_company_id', $user->shipping_company_id);

} elseif ($user->role === 'delivery_agent') {
    $agent = \App\Models\DeliveryAgent::where('user_id', $user->id)->first();
    if (!$agent) {
        $query->whereRaw('0=1');
    } else {
        $query->where('delivery_agent_id', $agent->id)
              ->where('shipping_company_id', $agent->shipping_company_id);
    }
}


    // فلترة حسب شركة الشحن (بالمفتاح الأجنبي فقط)
if ($request->filled('company')) {
    $companyId = $request->company;
    if ($companyId === 'null') {
        // فلترة الشحنات التي لا تحتوي على شركة شحن
        $query->whereNull('shipping_company_id');
    } else {
        $query->where('shipping_company_id', $companyId);
    }
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
          ->orWhereHas('products', function ($q2) use ($search) {
              $q2->where('name', 'like', "%{$search}%");
          });
    });
}


    if ($request->filled('agent')) {
        $query->where('delivery_agent_id', $request->agent);
    }

    if ($request->filled('printed')) {
        $query->where('is_printed', $request->printed);
    }

    if ($request->filled('product_id')) {
        $productId = $request->product_id;
        $query->whereHas('products', function ($q) use ($productId) {
            $q->where('products.id', $productId);
        });
    }

    if ($request->filled('print_date')) {
        $query->whereDate('print_date', $request->print_date);
    }

    // خزن نسخة للكويري قبل paginate
    $queryForTotals = (clone $query)->with('products');

    // احصل على limit من الإعدادات
    $paginationLimit = \App\Models\Setting::getValue('pagination_limit', $request->ajax() ? 100 : 15);

    // تطبيق paginate مع ترتيب حديث أولاً
    $shipments = $query->with('products')->latest()->paginate($paginationLimit);

    // احسب المجاميع من النسخة الأصلية
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

    // إذا كان الطلب Ajax فلترة ديناميكية فقط
    if ($request->ajax()) {
        $tableHtml = view('shipments.partials.table', [
            'shipments' => $shipments,
            'statuses' => \App\Models\ShipmentStatus::all(),
            'shippingCompanies' => \App\Models\ShippingCompany::where('is_active', true)->get(),
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

    // تحميل باقي البيانات للعرض في الصفحة
    $companies = \App\Models\ShippingCompany::where('is_active', true)->orderBy('name')->get();
    $shippingCompanies = $companies;
    $statuses = \App\Models\ShipmentStatus::orderBy('name')->get();
    $products = \App\Models\Product::all();
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
    $products       = \App\Models\Product::orderBy('name')->get(['id','name']);
    $trackingNumber = $this->generateTrackingNumber();
    $companies      = \App\Models\ShippingCompany::where('is_active', true)->orderBy('name')->get(['id','name']);
    $agents         = \App\Models\DeliveryAgent::where('is_active', true)->get();
    $statuses       = \App\Models\ShipmentStatus::all();

    $governorates = [
        'القاهرة','الجيزة','القليوبية','الإسكندرية','الإسماعيلية','السويس','الغربية','الشرقية',
        'الدقهلية','المنوفية','الفيوم','بني سويف','أسيوط','سوهاج','قنا','الأقصر','أسوان',
        'البحر الأحمر','مطروح','دمياط','بورسعيد','شمال سيناء','جنوب سيناء','كفر الشيخ','المنيا',
        'الوادي الجديد','البحيرة'
    ];

    // 👇 الافتراضي من الإعدادات
    $defaultCompanyId = (int) Setting::getValue('default_shipping_company_id', 0);

    return view('shipments.create', compact(
        'trackingNumber', 'companies', 'agents', 'statuses', 'products', 'governorates', 'defaultCompanyId'
    ));
}










public function store(Request $request)
{
    $validated = $request->validate([
        'shipping_company_id'    => 'nullable|exists:shipping_companies,id',
        'delivery_agent_id'      => 'nullable|exists:delivery_agents,id',
        'customer_name'          => 'required|string|max:255',
        'customer_phone'         => 'nullable|string|max:255',
        'alternate_phone'        => 'nullable|string|max:255',
        'customer_address'       => 'required|string|max:255',
        'governorate'            => 'required|string|max:255',
        'shipping_price'         => 'required|numeric|min:0',
        'total_amount'           => 'required|numeric|min:0',
        'notes'                  => 'nullable|string',
        'products'               => 'required|array|min:1',
        'products.*.product_id'  => 'required|exists:products,id',
        'products.*.color'       => 'required|string|max:255',
        'products.*.size'        => 'required|string|max:255',
        'products.*.quantity'    => 'required|integer|min:1',
        'products.*.price'       => 'required|numeric|min:0',
        'status_id'              => 'nullable|exists:shipment_statuses,id',
    ]);

    // (1) اسحب الافتراضيات كأرقام صريحة
    $defaultStatusId   = (int) (\App\Models\Setting::getValue('default_status_id', 0) ?: 0);
    $defaultCompanyId  = (int) (\App\Models\Setting::getValue('default_shipping_company_id', 0) ?: 0);
    $deliveredStatusId = (int) (\App\Models\Setting::getValue('delivered_status_id', 0) ?: 0);
    $returnedStatusId  = (int) (\App\Models\Setting::getValue('returned_status_id', 0) ?: 0);

    // (2) طبّق الافتراضيات لو المستخدم ما اختارش
    if (empty($validated['status_id']) && $defaultStatusId > 0) {
        $validated['status_id'] = $defaultStatusId;
    }

    // ثبّت شركة الشحن الافتراضية بقوة لو الفورم مرجعش قيمة أو رجّع 0
    $val = $validated['shipping_company_id'] ?? null;
    if ($val === null || $val === '' || (string)$val === '0') {
        $validated['shipping_company_id'] = $defaultCompanyId > 0 ? $defaultCompanyId : null;
    } else {
        $validated['shipping_company_id'] = (int) $val;
    }

    // (3) اسم الشركة النهائي بعد التثبيت
    $companyModel = $validated['shipping_company_id']
        ? \App\Models\ShippingCompany::find($validated['shipping_company_id'])
        : null;
    $companyName  = $companyModel?->name;

    // (4) هل نخصم مخزون؟ نخصم فقط لو الشركة المختارة ≠ الافتراضية، والحالة ليست "مرتجع"
    $shouldDeduct = ($validated['shipping_company_id'] && $defaultCompanyId > 0)
        ? ((int) $validated['shipping_company_id'] !== $defaultCompanyId)
        : false;

    if ($returnedStatusId > 0 && (int)($validated['status_id'] ?? 0) === $returnedStatusId) {
        $shouldDeduct = false;
    }

    return \DB::transaction(function () use ($validated, $companyName, $shouldDeduct, $deliveredStatusId, $returnedStatusId, $defaultCompanyId) {

        // إنشاء الشحنة (+ تسجيل أختام الزمن لو الحالة توصيل/مرتجع)
        $shipment = \App\Models\Shipment::create([
            'tracking_number'     => $this->generateTrackingNumber(),
            'customer_name'       => $validated['customer_name'],
            'customer_phone'      => $validated['customer_phone'] ?? null,
            'alternate_phone'     => $validated['alternate_phone'] ?? null,
            'customer_address'    => $validated['customer_address'],
            'governorate'         => $validated['governorate'],
            'shipping_price'      => $validated['shipping_price'],
            'total_amount'        => $validated['total_amount'],
            'status_id'           => (int) $validated['status_id'],
            'notes'               => $validated['notes'] ?? null,
            'shipping_company_id' => $validated['shipping_company_id'],
            'shipping_company'    => $companyName, // لو عندك العمود ده مضاف في الجدول والـ fillable
            'delivery_agent_id'   => $validated['delivery_agent_id'] ?? null,
            'delivered_at'        => ($deliveredStatusId > 0 && (int)$validated['status_id'] === $deliveredStatusId) ? now() : null,
            'returned_at'         => ($returnedStatusId  > 0 && (int)$validated['status_id'] === $returnedStatusId)  ? now() : null,
        ]);

        // حزام أمان: لو لأي سبب خرجت null، رجّع الافتراضية واحفظ
        if (!$shipment->shipping_company_id && $defaultCompanyId > 0) {
            $shipment->shipping_company_id = $defaultCompanyId;
            $shipment->shipping_company    = \App\Models\ShippingCompany::find($defaultCompanyId)?->name;
            $shipment->save();
        }

        // المنتجات + المخزون
        foreach ($validated['products'] as $idx => $product) {
            $productModel = \App\Models\Product::find($product['product_id']);

            $tieredPrice = $productModel->tierPrices()
                ->where('min_qty', '<=', $product['quantity'])
                ->orderByDesc('min_qty')
                ->value('price') ?? $product['price'];

            $shipment->products()->attach($product['product_id'], [
                'color'    => $product['color'],
                'size'     => $product['size'],
                'quantity' => $product['quantity'],
                'price'    => $tieredPrice,
            ]);

            if ($shouldDeduct) {
                $inv = \App\Models\Inventory::where([
                    'product_id' => $product['product_id'],
                    'color'      => $product['color'],
                    'size'       => $product['size'],
                ])->lockForUpdate()->first();

                if (!$inv) {
                    $inv = \App\Models\Inventory::create([
                        'product_id'   => $product['product_id'],
                        'color'        => $product['color'],
                        'size'         => $product['size'],
                        'quantity'     => 0,
                        'is_unlimited' => false,
                    ]);
                }

                $isUnlimited =
                    (bool)($inv->is_unlimited ?? false) ||
                    (bool)($productModel->is_unlimited ?? false) ||
                    (isset($productModel->track_stock) && $productModel->track_stock == false);

                if (!$isUnlimited) {
                    if ($inv->quantity < (int) $product['quantity']) {
                        $requestedQty = (int) $product['quantity'];
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            "products.$idx.quantity" => [
                                "الكمية غير كافية بالمخزن لهذا الصنف ({$product['color']}/{$product['size']}) — المتاح: {$inv->quantity}, المطلوب: {$requestedQty}"
                            ],
                        ]);
                    }
                    $inv->decrement('quantity', (int)$product['quantity']);
                }
            }
        }
        
        
        
        // ✅ لو خصمنا المخزون عند الإنشاء (شركة ≠ الافتراضية)، علّم إننا خصمنا مرة واحدة
if ($shouldDeduct) {
    if (\Illuminate\Support\Facades\Schema::hasColumn('shipments', 'inventory_reserved_at')) {
        $shipment->inventory_reserved_at = now();
        // إعادة ضبط أي أعلام أخرى مرتبطة إن لزم
        if (\Illuminate\Support\Facades\Schema::hasColumn('shipments', 'inventory_released_at')) {
            $shipment->inventory_released_at = null;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('shipments', 'inventory_returned_at')) {
            $shipment->inventory_returned_at = null;
        }
        $shipment->save();
    }
}


        if (auth()->user()->role == 'moderator') {
            return view('shipments.moderator-success');
        }

        return redirect()->route('shipments.index')->with('success', 'تم إضافة الشحنة بنجاح!');
    }, 3);
}







public function updateStatusAndCompany(Request $request, \App\Models\Shipment $shipment)
{
    $data = $request->validate([
        'status_id'           => 'nullable|exists:shipment_statuses,id',
        'shipping_company_id' => 'nullable|exists:shipping_companies,id',
        'notes'               => 'nullable|string',
    ]);

    $defaultCompany  = \App\Models\Setting::getValue('default_shipping_company_id', null);
    $deliveredStatus = \App\Models\Setting::getValue('delivered_status_id', null);
    $returnedStatus  = \App\Models\Setting::getValue('returned_status_id', null);

    $oldStatus  = (int) $shipment->status_id;
    $oldCompany = (int) ($shipment->shipping_company_id ?? 0);

    $newStatus  = array_key_exists('status_id', $data) ? (int)$data['status_id'] : $oldStatus;
    $newCompany = array_key_exists('shipping_company_id', $data) ? (int)$data['shipping_company_id'] : $oldCompany;

    return \DB::transaction(function () use ($shipment, $data, $oldStatus, $newStatus, $oldCompany, $newCompany, $defaultCompany, $deliveredStatus, $returnedStatus) {

        $crossedFromDefault = ($defaultCompany !== null)
            && ((int)$oldCompany === (int)$defaultCompany)
            && ((int)$newCompany !== (int)$defaultCompany);

        $crossedToDefault = ($defaultCompany !== null)
            && ((int)$oldCompany !== (int)$defaultCompany)
            && ((int)$newCompany === (int)$defaultCompany);

        $becameDelivered = (!empty($deliveredStatus))
            && ((int)$newStatus === (int)$deliveredStatus)
            && ((int)$oldStatus !== (int)$deliveredStatus);

        $becameReturned = (!empty($returnedStatus))
            && ((int)$newStatus === (int)$returnedStatus)
            && ((int)$oldStatus !== (int)$returnedStatus);

// نحافظ على عدم التكرار باستعمال أختام inventory_*_at
$hasReservedCol = \Illuminate\Support\Facades\Schema::hasColumn('shipments', 'inventory_reserved_at');
$hasReleasedCol = \Illuminate\Support\Facades\Schema::hasColumn('shipments', 'inventory_released_at');
$hasReturnedCol = \Illuminate\Support\Facades\Schema::hasColumn('shipments', 'inventory_returned_at');

$currentReservedAt = $hasReservedCol ? $shipment->inventory_reserved_at : null;

// A) حالة "مرتجع" => رجوع المخزون مرة واحدة فقط
if ($becameReturned) {
    if ($currentReservedAt) {
        $this->adjustInventoryForShipment($shipment, 'restock');
        if ($hasReturnedCol)  $shipment->inventory_returned_at = now();
        if ($hasReservedCol)  $shipment->inventory_reserved_at = null;
        if ($hasReleasedCol)  $shipment->inventory_released_at = null;
    }
    $shipment->returned_at = now();
}
// B) الرجوع إلى الشركة الافتراضية => رجوع المخزون مرة واحدة فقط
elseif ($crossedToDefault) {
    if ($currentReservedAt) {
        $this->adjustInventoryForShipment($shipment, 'restock');
        if ($hasReleasedCol)  $shipment->inventory_released_at = now();
        if ($hasReservedCol)  $shipment->inventory_reserved_at = null;
        if ($hasReturnedCol)  $shipment->inventory_returned_at = null;
    }
}
// C) الخروج من الافتراضية إلى شركة أخرى => خصم المخزون مرة واحدة فقط
elseif ($crossedFromDefault) {
    if (!$currentReservedAt) {
        $this->adjustInventoryForShipment($shipment, 'deduct');
        if ($hasReservedCol)  $shipment->inventory_reserved_at = now();
        if ($hasReleasedCol)  $shipment->inventory_released_at = null;
        if ($hasReturnedCol)  $shipment->inventory_returned_at = null;
    }
}


        if ($becameDelivered) {
            $shipment->delivered_at = now();
        }

        if (array_key_exists('status_id', $data)) {
            $shipment->status_id = $newStatus;
        }
        if (array_key_exists('shipping_company_id', $data)) {
            $shipment->shipping_company_id = $newCompany;
            $shipment->shipping_company = $newCompany
                ? \App\Models\ShippingCompany::find($newCompany)?->name
                : null;
        }
        if (array_key_exists('notes', $data)) {
            $shipment->notes = $data['notes'];
        }

        $shipment->save();

        return back()->with('success', 'تم تحديث الشحنة وتطبيق منطق المخزون والحالات بنجاح.');
    }, 3);
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

    $defaultCompanyId = (int) \App\Models\Setting::getValue('default_shipping_company_id', 0);

    $oldCompanyId = (int) ($shipment->shipping_company_id ?? 0);
    $newCompanyId = (int) ($request->shipping_company_id ?? 0);

    $company = $newCompanyId ? \App\Models\ShippingCompany::find($newCompanyId) : null;
    if ($newCompanyId && !$company) {
        return response()->json(['success' => false, 'message' => 'شركة الشحن غير موجودة']);
    }

    \DB::transaction(function () use ($shipment, $oldCompanyId, $newCompanyId, $defaultCompanyId, $company) {
        $hasReservedCol = \Illuminate\Support\Facades\Schema::hasColumn('shipments', 'inventory_reserved_at');
        $hasReleasedCol = \Illuminate\Support\Facades\Schema::hasColumn('shipments', 'inventory_released_at');
        $currentReservedAt = $hasReservedCol ? $shipment->inventory_reserved_at : null;

        // خرجنا من الافتراضية -> شركة أخرى: خصم مرة واحدة فقط
        $movedFromDefaultToAnother = ($defaultCompanyId > 0)
            && ($oldCompanyId === $defaultCompanyId)
            && ($newCompanyId !== $defaultCompanyId)
            && ($newCompanyId !== 0);

        // رجعنا إلى الافتراضية (لو عايز ترجع المخزون)
        $movedToDefault = ($defaultCompanyId > 0)
            && ($oldCompanyId !== $defaultCompanyId)
            && ($newCompanyId === $defaultCompanyId);

        if ($movedFromDefaultToAnother && !$currentReservedAt) {
            $this->adjustInventoryForShipment($shipment, 'deduct');
            if ($hasReservedCol) $shipment->inventory_reserved_at = now();
            if ($hasReleasedCol) $shipment->inventory_released_at = null;
        }

        // لو عايزك ترجع المخزون لما ترجع الافتراضية فعِّل السطور دي:
        // if ($movedToDefault && $currentReservedAt) {
        //     $this->adjustInventoryForShipment($shipment, 'restock');
        //     if ($hasReservedCol) $shipment->inventory_reserved_at = null;
        //     if ($hasReleasedCol) $shipment->inventory_released_at = now();
        // }

        // تحديث بيانات الشركة
        $shipment->shipping_company_id = $newCompanyId ?: null;
        $shipment->shipping_company    = $company?->name;

        // لو الشركة اتغيّرت فعلاً، امسح المندوب المرتبط
        if ($oldCompanyId !== $newCompanyId) {
            $shipment->delivery_agent_id   = null;
            $shipment->delivery_agent_name = null;
        }

        $shipment->save();
    });

    return response()->json([
        'success' => true,
        'message' => 'تم تحديث شركة الشحن بنجاح',
        'shipping_company' => $company?->name,
    ]);
}



















public function update(Request $request, Shipment $shipment)

{

    $validated = $request->validate([

        'tracking_number' => 'required|string|max:255',

        'shipping_company_id' => 'required|exists:shipping_companies,id',

        'customer_name' => 'required|string|max:255',

        'customer_phone' => 'nullable|string|max:255',
        
        'alternate_phone' => 'nullable|string|max:255',

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
        
        'alternate_phone'       => $validated['alternate_phone'] ?? null,

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












public function quickDelete($id)
{
    $shipment = Shipment::findOrFail($id);
    $shipment->delete();

    return redirect()->route('shipments.index')->with('success', 'تم حذف الشحنة بنجاح');
}




public function quickUpdate(Request $request, Shipment $shipment)
{
    $request->validate([
        'field' => 'required|string',
        'value' => 'nullable',
    ]);

    try {
        // 🟢 تحديث شركة الشحن = استخدم منطق المخزون الكامل
        if ($request->field === 'shipping_company_id') {
            // حوّل الطلب للدالة الموحّدة
            $forward = new \Illuminate\Http\Request(['shipping_company_id' => $request->value]);
            $resp = $this->updateShippingCompany($forward, $shipment); // دي بترجع JSON وبتعمل الخصم/الإرجاع

            // عزّز الاستجابة عشان ما نكسرش الواجهة لو مستنية color/label
            $data = $resp->getData(true);
            return response()->json($data + [
                'color' => optional($shipment->status)->color ?? 'table-secondary',
                'label' => optional($shipment->status)->name ?? 'غير محدد',
            ]);
        }

        // 🟢 تحديث الحالة = استخدم الدالة الموحّدة (هتضبط “تم التوصيل/مرتجع” + مخزون المرتجع)
        if ($request->field === 'status_id') {
            $forward = new \Illuminate\Http\Request(['status_id' => $request->value]);
            // دي هتدير الأختام + المخزون لو الحالة بقت مرتجع
            $this->updateStatusAndCompany($forward, $shipment);

            // رجّع نفس فورمات quickUpdate
            $shipment->refresh();
            return response()->json([
                'success' => true,
                'color' => optional($shipment->status)->color ?? 'table-secondary',
                'label' => optional($shipment->status)->name ?? 'غير محدد',
            ]);
        }

        // 🟡 باقي الحقول: السلوك القديم كما هو
        \Illuminate\Support\Facades\Log::info("QuickUpdate received for Shipment ID {$shipment->id}: Field={$request->field}, Value={$request->value}");
        
        if ($request->field === 'shipping_date') {
             // ensure standard Y-m-d format to avoid cast issues
             $shipment->shipping_date = $request->value ? \Carbon\Carbon::parse($request->value)->format('Y-m-d') : null;
        } else {
             $shipment->{$request->field} = $request->value;
        }

        try {
            $saved = $shipment->save();
            \Illuminate\Support\Facades\Log::info("QuickUpdate Save Result: " . ($saved ? 'True' : 'False') . ". New Value in DB: " . $shipment->refresh()->{$request->field});
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("QuickUpdate Save Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'color' => optional($shipment->status)->color ?? 'table-secondary',
            'label' => optional($shipment->status)->name ?? 'غير محدد',
        ]);
    } catch (\Throwable $e) {
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
        'status_id'   => 'required|exists:shipment_statuses,id',
        'agent_notes' => 'nullable|string',
    ]);

    $deliveredStatusId = (int) \App\Models\Setting::getValue('delivered_status_id', 0);
    $returnedStatusId  = (int) \App\Models\Setting::getValue('returned_status_id', 0);

    $oldStatusId = (int) $shipment->status_id;
    $newStatusId = (int) $request->status_id;

    if (!$request->isMethod('put') && !$request->isMethod('post') && !$request->isMethod('patch')) {
        abort(405, 'Method Not Allowed');
    }

    \DB::transaction(function () use ($shipment, $request, $oldStatusId, $newStatusId, $deliveredStatusId, $returnedStatusId) {

        // عدّاد التعديلات + الملاحظات
        $shipment->edit_count = (int)($shipment->edit_count ?? 0) + 1;
        if ($request->filled('agent_notes')) {
            $shipment->agent_notes = $request->agent_notes;
        }

        // ✅ لو الحالة أصبحت "تم التوصيل" (حسب الإعدادات) سجّل الوقت
        if ($deliveredStatusId > 0 && $oldStatusId !== $deliveredStatusId && $newStatusId === $deliveredStatusId) {
            $shipment->delivered_at = now();
            $shipment->delivery_date = now(); // لو لسه بتستخدم الحقل القديم
        }

        // ✅ لو الحالة أصبحت "مرتجع" (حسب الإعدادات): رجوع مخزون + ختم الزمن
        if ($returnedStatusId > 0 && $oldStatusId !== $returnedStatusId && $newStatusId === $returnedStatusId) {
            $this->adjustInventoryForShipment($shipment, 'restock');
            $shipment->returned_at = now();
            $shipment->return_date = now(); // لو لسه بتستخدم الحقل القديم
        }

        // في الآخر حدّث الحالة وحفظ
        $shipment->status_id = $newStatusId;
        $shipment->save();
    });

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













/**
 * ضبط مخزون كل عناصر الشحنة بالكامل.
 * $op = 'deduct' للخصم، أو 'restock' للرجوع.
 */
private function adjustInventoryForShipment(\App\Models\Shipment $shipment, string $op = 'deduct'): void
{
    $shipment->loadMissing(['products' => function ($q) {
        $q->withPivot(['color','size','quantity','price']);
    }]);

    foreach ($shipment->products as $item) {
        $productId = $item->id;
        $color     = $item->pivot->color;
        $size      = $item->pivot->size;
        $qty       = (int) $item->pivot->quantity;

        $inv = \App\Models\Inventory::where([
            'product_id' => $productId,
            'color'      => $color,
            'size'       => $size,
        ])->lockForUpdate()->first();

        if (!$inv) {
            $inv = \App\Models\Inventory::create([
                'product_id'   => $productId,
                'color'        => $color,
                'size'         => $size,
                'quantity'     => 0,
                'is_unlimited' => false,
            ]);
        }

        $productModel = \App\Models\Product::find($productId);
        $isUnlimited =
            (bool)($inv->is_unlimited ?? false) ||
            (bool)($productModel->is_unlimited ?? false) ||
            (isset($productModel->track_stock) && $productModel->track_stock == false);

        if ($isUnlimited) {
            continue;
        }

        if ($op === 'deduct') {
            if ($inv->quantity < $qty) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "products" => ["الكمية غير كافية بالمخزن لهذا الصنف ({$color}/{$size}) — المتاح: {$inv->quantity}, المطلوب: {$qty}"],
                ]);
            }
            $inv->decrement('quantity', $qty);
        } elseif ($op === 'restock') {
            $inv->increment('quantity', $qty);
        }
    }
}







    

}





