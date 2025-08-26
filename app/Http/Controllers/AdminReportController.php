<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use App\Helpers\MaskHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

/**
 * AdminReportController
 *
 * SRP:
 *  - Rapor ekranını, filtreli/maskeli veri üretimini ve maskeli PDF export’u yönetir.
 *
 * Notlar:
 *  - Maskelenmiş alanlar UI’dan yönetilir; helper ile uygulanır.
 *  - Tarih filtresi hazır şablonlar (daily/monthly/yearly) ve özel aralık destekler.
 */
class AdminReportController extends Controller
{
    /**
     * Rapor ana sayfası – varsayılan boş veri ile açılır.
     * GET -> /admin/reports
     */
    public function index(Request $request)
    {
        $fieldsForBlade = ['entry_time', 'name', 'tc_no', 'phone', 'plate', 'purpose', 'department', 'person_to_visit'];

        return view('admin.reports', [
            'visits'         => collect(),
            'fieldsForBlade' => $fieldsForBlade,
            'dateFilter'     => '',
            'sortOrder'      => 'desc',
            'chartData'      => [],
            'reportTitle'    => 'Tüm',
            'reportRange'    => 'Tüm zamanlar',
            // UI başlangıcı için varsayılan maske alanları
            'masked'         => ['name','tc_no','phone','plate','department','person_to_visit'],
        ]);
    }

    /**
     * Filtrelenmiş & maskelenmiş rapor verisini getirir.
     * POST -> /admin/reports/generate
     *
     * Çıktılar:
     *  - $data: MaskHelper::maskVisits(...) ile maskelenmiş veri
     *  - $chartData: Tarih filtresine göre özet/istatistik verisi
     */
    public function generateReport(Request $request)
    {
        Carbon::setLocale('tr');

        $allFields      = ['entry_time', 'name', 'tc_no', 'phone', 'plate', 'purpose', 'department', 'person_to_visit', 'approved_by'];
        $selectedFields = $request->input('fields', $allFields);

        // Varsayılan maske davranışı (UI ilk yüklemede hepsi seçili kabul edilir)
        $masked = $request->input('mask', ['name','tc_no','phone','plate','department','person_to_visit']);

        // Sayfa ilk açıldığında günlük kayıtlar da gelsin
        if (!$request->has('date_filter')) {
            $request->merge(['date_filter' => 'daily']);
        }

        $visitsQuery = Visit::with(['visitor.department', 'approver']);
        [$reportTitle, $reportRange] = $this->applyDateFilter($visitsQuery, $request);
        $this->applyFieldFilters($visitsQuery, $request, $allFields);

        $visits    = $visitsQuery->orderBy('entry_time', $request->input('sort_order', 'desc'))->get();
        $data      = MaskHelper::maskVisits($visits, $selectedFields, $masked); // Maskeyi uygula
        $chartData = $this->prepareChartData($visits, $request->input('date_filter', ''));

        return view('admin.reports', compact('data', 'selectedFields', 'chartData', 'reportTitle', 'reportRange'))
            ->with([
                'fieldsForBlade' => $selectedFields,
                'dateFilter'     => $request->date_filter,
                'sortOrder'      => $request->sort_order,
                'masked'         => $masked,
            ]);
    }

    /**
     * Maskelenmiş PDF üretir.
     * POST -> /admin/reports/pdf-masked
     */
    public function exportMaskedPdf(Request $request)
    {
        Carbon::setLocale('tr');

        $allFields      = ['entry_time', 'name', 'tc_no', 'phone', 'plate', 'purpose', 'department', 'person_to_visit', 'approved_by'];
        $selectedFields = $request->input('fields', $allFields);

        // Maske girdisini normalize et (hem map hem liste formatını kabul et)
        $maskedInput = $request->input('mask', ['name','tc_no','phone','plate','department','person_to_visit']);
        if (is_array($maskedInput)) {
            $isAssoc = array_keys($maskedInput) !== range(0, count($maskedInput) - 1);
            $masked  = $isAssoc
                ? array_keys(array_filter($maskedInput, fn($v) => $v === 'on' || $v === 1 || $v === '1' || $v === true))
                : $maskedInput;
        } else {
            $masked = ['name','tc_no','phone','plate','department','person_to_visit'];
        }

        $visitsQuery = Visit::with(['visitor.department', 'approver']);
        [$reportTitle, $reportRange] = $this->applyDateFilter($visitsQuery, $request);
        $this->applyFieldFilters($visitsQuery, $request, $allFields);

        $visits = $visitsQuery->orderBy('entry_time', $request->input('sort_order', 'desc'))->get();
        if ($visits->isEmpty()) {
            return back()->with('error', 'Seçilen filtrelere uygun kayıt bulunamadı.');
        }

        $data = MaskHelper::maskVisits($visits, $selectedFields, $masked);

        $pdf = Pdf::loadView('pdf.masked_pdf', [
            'data'           => $data,
            'fieldsForBlade' => $selectedFields,
            'reportTitle'    => $reportTitle,
            'reportRange'    => $reportRange,
            'masked'         => $masked,
        ])->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'DejaVu Sans'
        ]);

        return $pdf->download('Guvenli_Rapor.pdf');
    }

    /** Tarih filtresi uygular ve rapor başlık/metnini döner. */
    private function applyDateFilter($query, Request $request): array
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $filter    = $request->input('date_filter', 'all');

        // Özel tarih aralığı
        if ($startDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end   = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();
            $query->whereBetween('entry_time', [$start, $end]);

            $title = '';
            $range = Carbon::parse($startDate)->format('d.m.Y') . ' - ' . ($endDate ? Carbon::parse($endDate)->format('d.m.Y') : 'Bugün');
            return [$title, $range];
        }

        // Hazır filtreler
        switch ($filter) {
            case 'daily':
                $query->whereDate('entry_time', today());
                return ['Günlük', today()->format('d.m.Y')];

            case 'monthly':
                $query->whereMonth('entry_time', now()->month)
                      ->whereYear('entry_time', now()->year);
                return ['Aylık', now()->isoFormat('MMMM YYYY')];

            case 'yearly':
                $query->whereYear('entry_time', now()->year);
                return ['Yıllık', now()->year];

            default:
                return ['Tüm', 'Tüm zamanlar'];
        }
    }

    /** Alan bazlı filtre uygular (rapor tarafı). */
    private function applyFieldFilters($query, Request $request, array $allFields): void
    {
        foreach ($allFields as $field) {
            $value = $request->input("{$field}_value");
            if (!$value) continue;

            if (in_array($field, ['name', 'tc_no'])) {
                $query->whereHas('visitor', fn($q) => $q->where($field, 'like', "%{$value}%"));
            } elseif ($field === 'approved_by') {
                $query->whereHas('approver', fn($q) => $q->where('ad_soyad', 'like', "%{$value}%"));
            } elseif ($field === 'id') {
                $query->where('id', 'like', "%{$value}%");
            } elseif (in_array($field, ['phone', 'plate', 'purpose', 'person_to_visit', 'department'])) {
                $query->where($field, 'like', "%{$value}%");
            }
        }
    }

    /** Grafik/özet verisi (örnek: günlük/aylık sayımlar). */
    private function prepareChartData($visits, string $dateFilter): array
    {
        // Basit örnek: tarih filtresine göre sayım döndür
        // (Geliştirilebilir: gün/ay kırılımı, en sık ziyaret edilen birim/kişi vb.)
        return [
            'total'      => $visits->count(),
            'dateFilter' => $dateFilter,
        ];
    }
}
