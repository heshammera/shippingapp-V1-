<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    
    
    public function getDetails($id)
{
    $product = \App\Models\Product::findOrFail($id);

    // لو مخزن JSON
    $colors = is_string($product->colors) ? json_decode($product->colors, true) : $product->colors;
    $sizes  = is_string($product->sizes)  ? json_decode($product->sizes, true)  : $product->sizes;

    // لو مخزن نصوص مفصولة بفواصل
    if (is_string($product->colors) && str_contains($product->colors, ',')) {
        $colors = array_map('trim', explode(',', $product->colors));
    }
    if (is_string($product->sizes) && str_contains($product->sizes, ',')) {
        $sizes = array_map('trim', explode(',', $product->sizes));
    }

    return response()->json([
        'price'  => $product->price,
        'colors' => $colors ?? [],
        'sizes'  => $sizes ?? [],
    ]);
}

    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'colors' => 'nullable|string',
            'sizes' => 'nullable|string',
        ]);

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'colors' => $request->colors,
            'sizes' => $request->sizes,
        ]);

        return redirect()->route('products.index')->with('success', 'تم إضافة المنتج بنجاح');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'colors' => 'nullable|string',
            'sizes' => 'nullable|string',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'colors' => $request->colors,
            'sizes' => $request->sizes,
        ]);

        return redirect()->route('products.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'تم حذف المنتج بنجاح');
    }
    
    
    public function options(\App\Models\Product $product)
{
    // لو متخزّن Array في القاعدة
    $colors = $product->colors ?? [];
    $sizes  = $product->sizes  ?? [];

    // لو طالعين كسلسلة CSV، حوّلهم لمصفوفات
    if (is_string($colors)) {
        $colors = preg_split('/\s*,\s*/u', $colors, -1, PREG_SPLIT_NO_EMPTY);
    }
    if (is_string($sizes)) {
        $sizes = preg_split('/\s*,\s*/u', $sizes, -1, PREG_SPLIT_NO_EMPTY);
    }

    return response()->json([
        'colors' => array_values(array_filter(array_map('trim', $colors))),
        'sizes'  => array_values(array_filter(array_map('trim', $sizes))),
    ]);
}

    public function printBarcodes(Request $request)
    {
        $query = Product::query();

        if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }

        $products = $query->with('variants')->get();

        return view('print.product_barcodes', compact('products'));
    }

}
