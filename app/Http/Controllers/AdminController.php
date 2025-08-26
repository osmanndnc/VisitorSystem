<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    private array $allFields = [
        'id', 'entry_time', 'name', 'tc_no', 'phone', 'plate',
        'purpose', 'department', 'person_to_visit', 'approved_by'
    ];

    /**
     * Admin ziyaretçileri listeler (filtre).
     */
    public function index(Request $request)
    {
        $fields = $this->determineFields($request);
        $visitsQuery = Visit::with(['approver', 'visitor.department']); 

        $this->applyDateFilter($visitsQuery, $request);
        $this->applyFieldFilters($visitsQuery, $request);

        // ✅ person_to_visit üzerinden birim filtresi
        if ($request->filled('unit_name')) {
            $unit = $request->unit_name;
            $visitsQuery->where('person_to_visit', 'like', "%{$unit}%");
        }

        $visits = $visitsQuery->get();

        return view('admin.index', [
            'visits'      => $visits,
            'fields'      => $fields,
            'allFields'   => $this->allFields,
        ]);
    }

    /**
     * PDF çıktısını filtrelenmemiş olarak üretir.
     */
    public function exportPdfUnmasked(Request $request)
    {
        $selectedFields = array_diff($request->input('fields', $this->allFields), ['id']);
        if (empty($selectedFields)) {
            $selectedFields = array_diff($this->allFields, ['id']);
        }

        $visitsQuery = Visit::with(['approver', 'visitor.department']);
        $this->applyDateFilter($visitsQuery, $request);
        $this->applyFieldFilters($visitsQuery, $request);

        // ✅ person_to_visit üzerinden birim filtresi
        if ($request->filled('unit_name')) {
            $unit = $request->unit_name;
            $visitsQuery->where('person_to_visit', 'like', "%{$unit}%");
        }

        $visitsQuery->orderBy('entry_time', $request->input('sort_order', 'desc'));
        $visits = $visitsQuery->get();

        if ($visits->isEmpty()) {
            return back()->with('error', 'Seçilen filtrelere uygun kayıt bulunamadı.');
        }

        $pdfData         = $this->mapVisitsForPdf($visits, $selectedFields);
        $pdfHeadings     = $this->mapFieldLabels($selectedFields);
        $reportTitle     = $this->generateReportTitle($request);
        $fullReportTitle = $reportTitle . ' Ziyaretçi Listesi';

        $pdf = Pdf::loadView('pdf.unmasked_report', compact('pdfData', 'pdfHeadings', 'fullReportTitle'));
        return $pdf->download('ziyaretci_listesi.pdf');
    }

    /**
     * Seçili alanları belirler.
     */
    private function determineFields(Request $request): array
    {
        $requested = $request->input('filter');
        $fields    = $requested ? array_intersect(explode(',', $requested), $this->allFields) : $this->allFields;
        return empty($fields) ? $this->allFields : $fields;
    }

    /**
     * Tarih filtresi uygular.
     */
    private function applyDateFilter($query, Request $request): void
    {
        if ($request->filled('start_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end   = $request->filled('end_date')
                        ? Carbon::parse($request->end_date)->endOfDay()
                        : Carbon::now()->endOfDay();
            $query->whereBetween('entry_time', [$start, $end]);
        } else {
            match ($request->input('date_filter', 'daily')) {
                'daily'   => $query->whereDate('entry_time', Carbon::today()),
                'monthly' => $query->whereMonth('entry_time', Carbon::now()->month)
                                    ->whereYear('entry_time', Carbon::now()->year),
                'yearly'  => $query->whereYear('entry_time', Carbon::now()->year),
                default   => null,
            };
        }
    }

    /**
     * Alan bazlı filtre uygular.
     */
    private function applyFieldFilters($query, Request $request): void
    {
        foreach ($this->allFields as $field) {
            $value = $request->input("{$field}_value");
            if (!$value) continue;

            if (in_array($field, ['name', 'tc_no'])) {
                $query->whereHas('visitor', fn($q) => $q->where($field, 'like', "%{$value}%"));
            } elseif ($field === 'approved_by') {
                $query->whereHas('approver', fn($q) => $q->where('ad_soyad', 'like', "%{$value}%"));
            } elseif ($field === 'id') {
                $query->where('id', 'like', "%{$value}%");
            } elseif (in_array($field, ['phone', 'plate', 'purpose', 'person_to_visit'])) {
                $query->where($field, 'like', "%{$value}%");
            }
        }
    }

    /**
     * PDF veri setini mapler.
     */
    private function mapVisitsForPdf($visits, $fields)
    {
        return $visits->map(function ($visit) use ($fields) {
            $row = [];
            foreach ($fields as $field) {
                $row[$field] = match ($field) {
                    'entry_time'      => optional($visit->entry_time)->format('Y-m-d H:i:s'),
                    'name'            => $visit->visitor->name ?? '-',
                    'tc_no'           => $visit->visitor->tc_no ?? '-',
                    'phone',
                    'plate',
                    'purpose',
                    'person_to_visit' => $visit->$field ?? '-',
                    'approved_by'     => $visit->approver->ad_soyad ?? $visit->approved_by ?? '-',
                    'department'      => $visit->visitor->department->name ?? '-', 
                    default           => $visit->$field ?? '-',
                };
            }
            return $row;
        });
    }

    /**
     * PDF başlıklarını Türkçeleştirir.
     */
    private function mapFieldLabels($fields): array
    {
        return array_map(fn($field) => match($field) {
            'entry_time'      => 'Giriş Tarihi',
            'name'            => 'Ad Soyad',
            'tc_no'           => 'T.C. Kimlik No',
            'phone'           => 'Telefon',
            'plate'           => 'Plaka',
            'purpose'         => 'Ziyaret Sebebi',
            'person_to_visit' => 'Ziyaret Edilen Kişi',
            'approved_by'     => 'Ekleyen',
            'department'      => 'Ziyaret Edilen Birim', 
            default           => ucfirst(str_replace('_', ' ', $field)),
        }, $fields);
    }

    /**
     * Rapor başlığını oluşturur.
     */
    private function generateReportTitle(Request $request): string
    {
        if ($request->filled('start_date')) {
            $start = Carbon::parse($request->start_date)->format('d.m.Y');
            $end   = $request->filled('end_date') ? Carbon::parse($request->end_date)->format('d.m.Y') : $start;
            return "$start - $end Aralığı";
        }

        return match ($request->input('date_filter', 'daily')) {
            'daily'   => 'Günlük',
            'monthly' => 'Aylık',
            'yearly'  => 'Yıllık',
            default   => 'Tüm',
        };
    }
}