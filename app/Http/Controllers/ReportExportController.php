<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Visit;

class ReportExportController extends Controller
{
    /**
     * Excel dışa aktarımı (sayfadaki görünümle birebir).
     */
    public function export(Request $request)
    {
        // Alanlar
        $allFields      = ['entry_time','name','tc_no','phone','plate','purpose','person_to_visit','approved_by'];
        $selectedFields = $request->input('fields', $allFields);

        // >>> MASKE NORMALİZASYONU — Varsayılan: BOŞ (mask seçmediysen maskesiz indir)
        $maskedInput = $request->input('mask', []);
        if (is_array($maskedInput)) {
            $isAssoc = array_keys($maskedInput) !== range(0, count($maskedInput) - 1);
            $masked  = $isAssoc
                ? array_keys(array_filter($maskedInput, fn($v) => $v === 'on' || $v === 1 || $v === '1' || $v === true))
                : $maskedInput;
        } else {
            $masked = [];
        }
        // <<<

        // (İstersen burada AdminReportController’daki filtrelerin aynısını uygula)
        $visits = Visit::with(['visitor','approver'])
            ->orderBy('entry_time', $request->input('sort_order','desc'))
            ->get();

        return Excel::download(
            new \App\Exports\ReportExport($visits, $selectedFields, $masked),
            'rapor.xlsx'
        );
    }

    /**
     * (İsteğe bağlı) Her zaman maskeli "Güvenli Excel" için ikinci endpoint.
     */
    public function exportSecure(Request $request)
    {
        $request->merge(['mask' => ['name','tc_no','phone','plate','person_to_visit']]);
        return $this->export($request);
    }
}
