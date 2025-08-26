<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Visit;

/**
 * ReportExportController
 *
 * SRP:
 *  - Raporların Excel'e dışa aktarımının HTTP akışını yönetir (maskeli veya maskesiz).
 *
 * Not:
 *  - Listeleme/filtreleme bu controller'ın sorumluluğu değildir; yalnızca verilen veri seti/alanlarla export yapılır.
 */
class ReportExportController extends Controller
{
    /**
     * Excel dışa aktarımı (ekrandaki seçimlere paralel).
     * POST -> /admin/reports/export
     */
    public function export(Request $request)
    {
        // 1) Alanlar — kaynak dosyada aynı beyaz liste kullanılıyor, korunur:contentReference[oaicite:17]{index=17}
        $allFields      = ['entry_time','name','tc_no','phone','plate','purpose','department','person_to_visit','approved_by'];
        $selectedFields = $request->input('fields', $allFields);

        // 2) Maske normalizasyonu — map/liste her iki formatı da kabul eder (kaynak akış korunur):contentReference[oaicite:18]{index=18}
        $maskedInput = $request->input('mask', []);
        if (is_array($maskedInput)) {
            $isAssoc = array_keys($maskedInput) !== range(0, count($maskedInput) - 1);
            $masked  = $isAssoc
                ? array_keys(array_filter($maskedInput, fn($v) => $v === 'on' || $v === 1 || $v === '1' || $v === true))
                : $maskedInput;
        } else {
            $masked = [];
        }

        // 3) Veri — kaynakta with(...) + orderBy(...) ile çekiliyor:contentReference[oaicite:19]{index=19}
        $visits = Visit::with(['visitor.department','approver'])
            ->orderBy('entry_time', $request->input('sort_order','desc'))
            ->get();

        // 4) Excel çıktısı (ReportExport sınıfına delege):contentReference[oaicite:20]{index=20}
        return Excel::download(
            new ReportExport($visits, $selectedFields, $masked),
            'rapor.xlsx'
        );
    }

    /**
     * (İsteğe bağlı) Her zaman maskeli “Güvenli Excel”.
     * POST -> /admin/reports/export-secure
     */
    public function exportSecure(Request $request)
    {
        // Varsayılan mask seti ile export çalıştır
        $request->merge(['mask' => ['name','tc_no','phone','plate','department','person_to_visit']]); //:contentReference[oaicite:21]{index=21}
        return $this->export($request);
    }
}
