<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CollectionsExport;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use App\Models\ShippingCompany;

class CollectionsReportController extends Controller
{
    
    


public function index(Request $request)
{
    $shippingCompanies = ShippingCompany::all();

    $collections = Collection::with('shippingCompany')
        ->when($request->date_from, fn($q) => $q->whereDate('date', '>=', $request->date_from))
        ->when($request->date_to, fn($q) => $q->whereDate('date', '<=', $request->date_to))
        ->when($request->shipping_company_id, fn($q) => $q->where('shipping_company_id', $request->shipping_company_id))
        ->get();

    $total_collection = $collections->sum('amount');

    return view('reports.collections', compact('shippingCompanies', 'collections', 'total_collection'));
}


    
    
    private function initMpdf()
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'amiri', // لازم يكون الخط موجود في storage/fonts
            'fontDir' => array_merge($fontDirs, [storage_path('fonts')]),
            'fontdata' => $fontData + [
                'amiri' => [
                    'R' => 'Amiri-Regular.ttf',
                    'B' => 'Amiri-Bold.ttf',
                ]
            ],
        ]);

        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->SetDirectionality('rtl');

        return $mpdf;
    }

    private function getCollectionsData(Request $request)
    {
        $collections = Collection::with('shippingCompany')
            ->when($request->date_from, fn($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('date', '<=', $request->date_to))
            ->when($request->shipping_company_id, fn($q) => $q->where('shipping_company_id', $request->shipping_company_id))
            ->get();

        $total_collection = $collections->sum('amount');

        $collectionsByCompany = $collections
            ->groupBy('shipping_company_id')
            ->map(function ($group) {
                return [
                    'company_name' => optional($group->first()->shippingCompany)->name ?? 'غير معروف',
                    'count'        => $group->count(),
                    'total'        => $group->sum('amount'),
                ];
            });

        return compact('collections', 'total_collection', 'collectionsByCompany');
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getCollectionsData($request);

        $html = view('reports.collections_pdf', $data)->render();

        $mpdf = $this->initMpdf();
        $mpdf->WriteHTML($html);

        $filename = 'تقرير_التحصيلات_' . now()->format('Ymd_His') . '.pdf';
        return $mpdf->Output($filename, 'D'); // تحميل
    }

    public function printPdf(Request $request)
    {
        $data = $this->getCollectionsData($request);

        $html = view('reports.collections_pdf', $data)->render();

        $mpdf = $this->initMpdf();
        $mpdf->WriteHTML($html);

        $mpdf->SetJS('this.print();');

        $filename = 'تقرير_التحصيلات_' . now()->format('Ymd_His') . '.pdf';
        return $mpdf->Output($filename, 'I'); // عرض في المتصفح
    }

public function exportExcel(Request $request)
{
    return Excel::download(
        new CollectionsExport(
            $request->date_from,
            $request->date_to,
            $request->shipping_company_id
        ),
        'تقرير_التحصيلات_' . now()->format('Ymd_His') . '.xlsx'
    );
}

}
