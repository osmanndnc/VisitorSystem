<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class ReportExportController extends Controller
{
    /**
     * Excel dışa aktarımı yapar. Maskeli veya maskesiz olarak.
     */
    public function export(Request $request)
    {
        // Alanları al, string geldiyse array'e çevir
        $fields = $request->input('fields', []);
        if (is_string($fields)) {
            $fields = explode(',', $fields);
        }

        // Boşlukları ve boş elemanları temizle
        $fields = array_filter(array_map('trim', $fields));

        $dateFilter = $request->input('date_filter', '');
        $sortOrder = $request->input('sort_order', 'desc');
        $unmasked  = $request->boolean('unmasked');

        // Log kaydı
        Log::info('Excel rapor dışa aktarımı yapıldı', [
            'user_id'     => auth()->id(),
            'username'    => auth()->user()->username ?? 'Anonim',
            'fields'      => $fields,
            'date_filter' => $dateFilter,
            'sort_order'  => $sortOrder,
            'unmasked'    => $unmasked,
            'timestamp'   => now()->toDateTimeString(),
        ]);

        return Excel::download(
            new ReportExport($fields, $dateFilter, $sortOrder, $unmasked),
            'rapor.xlsx'
        );
    }
}
