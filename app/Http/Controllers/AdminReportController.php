<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use App\Helpers\MaskHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminReportController extends Controller
{
    /**
     * Rapor ana sayfası – boş veri ile açılır.
     */
    public function index(Request $request)
    {
        $fieldsForBlade = ['entry_time', 'name', 'tc_no', 'phone', 'plate', 'purpose', 'person_to_visit'];

        Log::channel('admin')->info('Rapor sayfası açıldı', $this->logContext([
            'action'  => 'report_index',
            'status'  => 'success',
            'message' => 'Admin rapor sayfası görüntülendi'
        ]));

        return view('admin.reports', [
            'visits'         => collect(),
            'fieldsForBlade' => $fieldsForBlade,
            'dateFilter'     => '',
            'sortOrder'      => 'desc',
            'chartData'      => [],
            'reportTitle'    => 'Tüm',
            'reportRange'    => 'Tüm zamanlar',
            // Varsayılan maskeler (UI açıldığında tikli gözüksün)
            'masked'         => ['name','tc_no','phone','plate','person_to_visit'],
        ]);
    }

    /**
     * Filtrelenmiş ve maskelenmiş rapor verisini getirir.
     */
    public function generateReport(Request $request)
    {
        Carbon::setLocale('tr');

        $allFields      = ['entry_time', 'name', 'tc_no', 'phone', 'plate', 'purpose', 'person_to_visit', 'approved_by'];
        $selectedFields = $request->input('fields', $allFields);

        // Kullanıcının seçtiği maskeler (varsayılan hepsi tikli)
        $masked = $request->input('mask', ['name','tc_no','phone','plate','person_to_visit']);
        // // Liste ekranında default: MASKE YOK
        // $maskedInput = $request->input('mask', []);
        // if (is_array($maskedInput)) {
        //     $isAssoc = array_keys($maskedInput) !== range(0, count($maskedInput) - 1);
        //     $masked  = $isAssoc
        //         ? array_keys(array_filter($maskedInput, fn($v) => $v === 'on' || $v === 1 || $v === '1' || $v === true))
        //         : $maskedInput;
        // } else {
        //     $masked = [];
        // }

        //iLK AÇILDIĞINDA GELEN GÜNLÜK KAYITLARIN RAPORLARDA DA GÖRÜNMESİ İÇİN 
        if (!$request->has('date_filter')) {
        $request->merge(['date_filter' => 'daily']);}
        
        $visitsQuery = Visit::with(['visitor', 'approver']);
        [$reportTitle, $reportRange] = $this->applyDateFilter($visitsQuery, $request);
        $this->applyFieldFilters($visitsQuery, $request, $allFields);

        $visits    = $visitsQuery->orderBy('entry_time', $request->input('sort_order', 'desc'))->get();
        // === ÖNEMLİ: Helper çağrısına $masked de veriyoruz
        $data      = MaskHelper::maskVisits($visits, $selectedFields, $masked);
        $chartData = $this->prepareChartData($visits, $request->input('date_filter', ''));

        if ($visits->isEmpty()) {
            Log::channel('admin')->warning('Rapor oluşturma – veri bulunamadı', $this->logContext([
                'action'  => 'generate_report',
                'status'  => 'warning',
                'message' => 'Filtrelere uygun kayıt bulunamadı',
                'filters' => $request->except('_token')
            ]));
        } else {
            Log::channel('admin')->info('Maskeleme ile rapor oluşturuldu', $this->logContext([
                'action'       => 'generate_report',
                'status'       => 'success',
                'message'      => 'Maskeleme ile rapor oluşturuldu',
                'filters'      => $request->except('_token'),
                'masked'       => $masked,
                'fields'       => $selectedFields,
                'record_count' => $visits->count()
            ]));
        }

        return view('admin.reports', compact('data', 'selectedFields', 'chartData', 'reportTitle', 'reportRange'))
            ->with([
                'fieldsForBlade' => $selectedFields,
                'dateFilter'     => $request->date_filter,
                'sortOrder'      => $request->sort_order,
                'masked'         => $masked,
            ]);
    }

    /**
     * Maskelenmiş PDF çıktısı üretir.
     */
    public function exportMaskedPdf(Request $request)
    {
        Carbon::setLocale('tr');

        $allFields      = ['entry_time', 'name', 'tc_no', 'phone', 'plate', 'purpose', 'person_to_visit', 'approved_by'];
        $selectedFields = $request->input('fields', $allFields);
        
        //$masked         = $request->input('mask', ['name','tc_no','phone','plate','person_to_visit']);
        // >>> MASK NORMALİZASYONU (map + liste her ikisini de kabul eder)
        $maskedInput = $request->input('mask', ['name','tc_no','phone','plate','person_to_visit']);
        if (is_array($maskedInput)) {
            $isAssoc = array_keys($maskedInput) !== range(0, count($maskedInput) - 1);
            if ($isAssoc) {
                $masked = array_keys(array_filter($maskedInput, fn($v) => $v === 'on' || $v === 1 || $v === '1' || $v === true));
            } else {
                $masked = $maskedInput;
            }
        } else {
            $masked = ['name','tc_no','phone','plate','person_to_visit'];
        }
        // <<<


        $visitsQuery = Visit::with(['visitor', 'approver']);
        [$reportTitle, $reportRange] = $this->applyDateFilter($visitsQuery, $request);
        $this->applyFieldFilters($visitsQuery, $request, $allFields);

        $visits = $visitsQuery->orderBy('entry_time', $request->input('sort_order', 'desc'))->get();
        // === ÖNEMLİ: PDF için de aynı maske seti kullanılıyor
        $data   = MaskHelper::maskVisits($visits, $selectedFields, $masked);

        if ($visits->isEmpty()) {
            Log::channel('admin')->warning('PDF export denemesi – veri yok', $this->logContext([
                'action'  => 'pdf_export_masked',
                'status'  => 'warning',
                'message' => 'PDF export için uygun veri bulunamadı',
                'filters' => $request->except('_token')
            ]));

            return back()->with('error', 'Seçilen filtrelere uygun kayıt bulunamadı.');
        }

        Log::channel('admin')->info('PDF olarak güvenli (masked) rapor indirildi', $this->logContext([
            'action'       => 'pdf_export_masked',
            'status'       => 'success',
            'message'      => 'PDF masked export başarılı',
            'field_count'  => count($selectedFields),
            'masked'       => $masked,
            'record_count' => $visits->count()
        ]));

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

    /**
     * Ortak log context bilgisi.
     */
    private function logContext(array $extra = []): array
    {
        $user = auth()->user();

        return array_merge([
            'user_id'  => $user->id ?? null,
            'username' => $user->username ?? 'Anonim',
            'ip'       => request()->ip(),
            'time'     => now()->toDateTimeString(),
        ], $extra);
    }

    /**
     * Tarih filtreleme uygular ve rapor başlığı döner.
     */
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
                $title = 'Günlük';
                $range = today()->format('d.m.Y');
                break;

            case 'monthly':
                $query->whereMonth('entry_time', now()->month)
                    ->whereYear('entry_time', now()->year);
                $title = 'Aylık';
                $range = now()->isoFormat('MMMM YYYY');
                break;

            case 'yearly':
                $query->whereYear('entry_time', now()->year);
                $title = 'Yıllık';
                $range = (string) now()->year;
                break;

            case 'all':
            default:
                $title = 'Tüm';
                $range = 'Tüm zamanlar';
                break;
        }

        return [$title, $range];
    }

    /**
     * Alan bazlı filtreleme uygular.
     */
    private function applyFieldFilters($query, Request $request, array $fields): void
    {
        foreach ($fields as $field) {
            $value = $request->input("{$field}_value");
            if (!$value) continue;

            if (in_array($field, ['name', 'tc_no'])) {
                $query->whereHas('visitor', fn($q) => $q->where($field, 'like', "%{$value}%"));
            } elseif ($field === 'approved_by') {
                $query->whereHas('approver', fn($q) => $q->where('ad_soyad', 'like', "%{$value}%"));
            } elseif (in_array($field, ['phone', 'plate', 'purpose', 'person_to_visit'])) {
                $query->where($field, 'like', "%{$value}%");
            }
        }
    }

    /**
     * Grafik verisini hazırlar.
     */
    private function prepareChartData($visits, $filter): array
    {
        return match ($filter) {
            'daily'   => $this->groupChart($visits, 'H', 0, 23),
            'monthly' => $this->groupChart($visits, 'd', 1, now()->daysInMonth),
            'yearly'  => $this->groupChart($visits, 'n', 1, 12),
            default   => $this->groupChartByYear($visits),
        };
    }

    /**
     * Saat/gün/ay gruplamalı chart verisi.
     */
    private function groupChart($visits, $format, $start, $end): array
    {
        $group = $visits->groupBy(fn($v) => $v->entry_time->format($format))->map->count();
        $chart = [];

        for ($i = $start; $i <= $end; $i++) {
            $label    = $format === 'H' ? str_pad($i, 2, '0', STR_PAD_LEFT) : $i;
            $chart[]  = ['label' => (int)$label, 'count' => $group[$label] ?? 0];
        }

        return $chart;
    }

    /**
     * Yıllara göre chart verisi.
     */
    private function groupChartByYear($visits): array
    {
        $group = $visits->groupBy(fn($v) => $v->entry_time->format('Y'))->map->count();
        $years = $visits->isNotEmpty()
            ? range($visits->min('entry_time')->year, $visits->max('entry_time')->year)
            : [];

        return array_map(fn($year) => ['label' => $year, 'count' => $group[$year] ?? 0], $years);
    }
}
