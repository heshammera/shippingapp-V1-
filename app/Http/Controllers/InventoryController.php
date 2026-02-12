<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Notifications\LowStockNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class InventoryController extends Controller
{
public function index(Request $request)
{
    $inventories = Inventory::with(['product:id,name'])
        // فلترة العرض: النشطة فقط / المحذوفة فقط / الكل
        ->when($request->get('show') === 'trashed', fn($q) => $q->onlyTrashed())
        ->when($request->get('show') === 'all',     fn($q) => $q->withTrashed())

        // البحث بالاسم/اللون/المقاس (مجمّعة صح لتجنّب مشاكل orWhere)
        ->when($request->filled('search'), function ($q) use ($request) {
            $s = trim($request->search);
            $q->where(function ($q) use ($s) {
                $q->whereHas('product', fn($p) => $p->where('name', 'like', "%{$s}%"))
                  ->orWhere('color', 'like', "%{$s}%")
                  ->orWhere('size',  'like', "%{$s}%");
            });
        })

        ->orderBy('product_id')
        ->orderBy('color')
        ->orderBy('size')
        ->paginate(100)
        ->withQueryString(); // يحافظ على باراميتر search/show في الروابط

    return view('inventories.index', compact('inventories'));
}


    public function create()
    {
        $products = Product::orderBy('name')->get(['id','name']);
        return view('inventories.create', compact('products'));
    }

public function store(Request $request)
{
    // فاليديشن للحالتين (منتج موجود أو منتج جديد)
    $data = $request->validate([
        // إمّا تختار منتج موجود، أو تعبّي حقول المنتج الجديد
        'product_id'         => ['nullable','integer','exists:products,id'],
        'new_product_name'   => ['required_without:product_id','string','max:255'],
        'new_product_price'  => ['required_without:product_id','numeric','min:0'],
        'new_colors'         => ['required_without:product_id','string'],
        'new_sizes'          => ['required_without:product_id','string'],

        // اللون والمقاس مطلوبين فقط مع "منتج موجود"
        'color'              => ['required_with:product_id','nullable','string','max:50'],
        'size'               => ['required_with:product_id','nullable','string','max:50'],

        'quantity'           => ['required','integer','min:0'],
        'low_stock_alert'    => ['required','integer','min:0'],
    ]);

    // 1) لو اختار "منتج موجود"
    if ($request->filled('product_id')) {
        $productId = (int) $request->product_id;
        $color = trim((string) $request->color);
        $size  = trim((string) $request->size);

        if (Inventory::where([
            'product_id' => $productId,
            'color'      => $color,
            'size'       => $size,
        ])->exists()) {
            return back()->withErrors(['color'=>'هذا اللون/المقاس مسجل بالفعل لهذا المنتج'])->withInput();
        }

        Inventory::create([
            'product_id'      => $productId,
            'color'           => $color,
            'size'            => $size,
            'quantity'        => $data['quantity'],
            'low_stock_alert' => $data['low_stock_alert'],
        ]);

        return redirect()->route('inventories.index')->with('success','تم إضافة سجل المخزون.');
    }

    // 2) لو اختار "إضافة منتج جديد"
    $colorsArr = $this->parseCsv($request->new_colors);
    $sizesArr  = $this->parseCsv($request->new_sizes);

    // لو المستخدم كتب قيم قليلة، هنكمل طبيعي بالتركيبات حسب القاعدة
    // لو مفيش أي قيم (نادرًا)، نستخدم القيم المخفية كـ fallback لو موجودة
    if (empty($colorsArr) && $request->filled('color')) {
        $colorsArr = [trim((string) $request->color)];
    }
    if (empty($sizesArr) && $request->filled('size')) {
        $sizesArr = [trim((string) $request->size)];
    }

    // خزّن ألوان ومقاسات المنتج كـ CSV (نفس نظامك القديم)
    $product = Product::create([
        'name'   => $data['new_product_name'],
        'price'  => $data['new_product_price'],
        'colors' => implode(',', $colorsArr),
        'sizes'  => implode(',', $sizesArr),
        // 'cost_price' => ... لو عندك
    ]);

    // ابنِ التركيبات حسب 3 الاحتمالات
    $pairs = $this->buildVariantPairs($colorsArr, $sizesArr);

    $created = 0;
    $skipped = [];
    foreach ($pairs as [$c, $s]) {
        $c = (string) $c;
        $s = (string) $s;

        $exists = Inventory::where([
            'product_id' => $product->id,
            'color'      => $c,
            'size'       => $s,
        ])->exists();

        if ($exists) {
            $skipped[] = ($c ?: 'بدون لون') . ' / ' . ($s ?: 'بدون مقاس');
            continue;
        }

        Inventory::create([
            'product_id'      => $product->id,
            'color'           => $c,
            'size'            => $s,
            'quantity'        => $data['quantity'],
            'low_stock_alert' => $data['low_stock_alert'],
        ]);
        $created++;
    }

    $msg = "تم إنشاء المنتج وإضافة {$created} سجل مخزون.";
    if ($skipped) {
        $msg .= " تم تجاهل مكررات: " . implode('، ', $skipped);
    }

    return redirect()->route('inventories.index')->with('success', $msg);
}

/**
 * يحول نص CSV إلى مصفوفة مميزة ونظيفة، ويدعم الفاصلة العربية والإنجليزية.
 */
private function parseCsv(?string $raw): array
{
    $raw = (string) $raw;
    if ($raw === '') return [];
    $parts = preg_split('/\s*[,\x{060C}]\s*/u', $raw, -1, PREG_SPLIT_NO_EMPTY); // , و الفاصلة العربية
    $parts = array_map('trim', $parts);
    $parts = array_values(array_unique(array_filter($parts, fn($v) => $v !== '')));
    return $parts;
}

/**
 * يبني التركيبات حسب 3 الحالات:
 * 1) لون واحد + مقاس واحد → صف واحد
 * 2) ألوان متعددة + مقاسات متعددة → كل التركيبات
 * 3) ألوان متعددة + مقاس واحد، أو مقاسات متعددة + لون واحد → صف لكل قيمة في البُعد المتعدد
 */
private function buildVariantPairs(array $colors, array $sizes): array
{
    $cCount = count($colors);
    $sCount = count($sizes);

    // ضمان وجود عناصر حتى لو فاضيين
    if ($cCount === 0) $colors = [''];
    if ($sCount === 0) $sizes  = [''];
    $cCount = count($colors);
    $sCount = count($sizes);

    // 1) واحد × واحد
    if ($cCount === 1 && $sCount === 1) {
        return [[ $colors[0], $sizes[0] ]];
    }

    // 2) متعدد × متعدد
    if ($cCount > 1 && $sCount > 1) {
        $pairs = [];
        foreach ($colors as $c) {
            foreach ($sizes as $s) {
                $pairs[] = [$c, $s];
            }
        }
        return $pairs;
    }

    // 3) متعدد × واحد، أو واحد × متعدد
    if ($cCount > 1) {
        $s = $sizes[0];
        return array_map(fn($c) => [$c, $s], $colors);
    }
    if ($sCount > 1) {
        $c = $colors[0];
        return array_map(fn($s) => [$c, $s], $sizes);
    }

    // احتياط
    return [[ $colors[0] ?? '', $sizes[0] ?? '' ]];
}




    
    public function updateAlert(Request $request, Inventory $inventory)
{
    $data = $request->validate([
        'low_stock_alert' => ['required','integer','min:0'],
    ]);

    $inventory->update(['low_stock_alert' => $data['low_stock_alert']]);

    return back()->with('success', 'تم تحديث حد التنبيه بنجاح.');
}
public function unlimit(Inventory $inventory)
{
    $inventory->quantity = null; // خليها null = غير محدود
    $inventory->save();

    return back()->with('success', 'تم جعل الكمية غير محدودة للمنتج.');
}

public function toggleUnlimited(\App\Models\Inventory $inventory)
{
    $inventory->is_unlimited = ! $inventory->is_unlimited;
    $inventory->save();

    return back()->with('success', 'تم تحديث حالة الكمية ('.($inventory->is_unlimited ? 'غير محدود' : 'محدود').')');
}



public function setUnlimited(Inventory $inventory)
{
    $inventory->is_unlimited = 1;
    $inventory->quantity = null; // اختياري، لو عايز تمسح العدد
    $inventory->save();

    return back()->with('success','تم جعل الكمية غير محدود');
}


public function setQuantity(Request $request, Inventory $inventory)
{
    $data = $request->validate([
        'quantity' => ['required','integer','min:0'],
    ]);

    $inventory->quantity = $data['quantity'];
    $inventory->is_unlimited = 0;
    $inventory->save();

    return back()->with('success','تم تحديد الكمية');
}

public function add(Request $request, Inventory $inventory)
{
    $data = $request->validate([
        'qty' => ['required','integer','min:1'],
    ]);

    if ($inventory->is_unlimited) {
        return back()->with('warning','المخزون غير محدود. حدد كمية أولاً.');
    }

    $inventory->quantity += $data['qty'];
    $inventory->save();

    return back()->with('success','تمت إضافة الكمية');
}

public function remove(Request $request, Inventory $inventory)
{
    $data = $request->validate([
        'qty' => ['required','integer','min:1'],
    ]);

    if ($inventory->is_unlimited) {
        return back()->with('warning','المخزون غير محدود. حدد كمية أولاً.');
    }

    $inventory->quantity = max(0, $inventory->quantity - $data['qty']);
    $inventory->save();

    return back()->with('success','تم خصم الكمية');
}






public function destroy(Inventory $inventory)
{
    // خيار إضافي: لو عايز تمنع حذف سجل فيه كمية، فك الكومنت التالي
    // if (!$inventory->is_unlimited && $inventory->quantity > 0) {
    //     return back()->with('warning', 'لا يمكن حذف سجل وبه كمية > 0. صفّر الكمية أولاً.');
    // }

    $inventory->delete(); // Soft delete
    return back()->with('success', 'تم حذف سجل المخزون مؤقتًا.');
}

public function restore($id)
{
    $inventory = Inventory::withTrashed()->findOrFail($id);
    $inventory->restore();
    return back()->with('success', 'تم استعادة سجل المخزون.');
}

public function forceDelete($id)
{
    $inventory = Inventory::withTrashed()->findOrFail($id);

    // خيار إضافي: منع الحذف النهائي لو فيه كمية
    // if (!$inventory->is_unlimited && $inventory->quantity > 0) {
    //     return back()->with('warning', 'لا يمكن الحذف النهائي مع وجود كمية. صفّر الكمية أولاً.');
    // }

    $inventory->forceDelete(); // حذف نهائي من القاعدة
    return back()->with('success', 'تم حذف سجل المخزون نهائيًا.');
}


}
