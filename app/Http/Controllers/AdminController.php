<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $allFields = [
            'id', // ID sütunu ana sayfada görüntülenecek
            'entry_time',
            'name',
            'tc_no',
            'phone',
            'plate',
            'purpose',
            'person_to_visit',
            'approved_by'
        ];
        
        $fields = $request->has('filter')
            ? array_intersect(explode(',', $request->input('filter')), $allFields)
            : $allFields;
        
        if (empty($fields)) {
            $fields = $allFields;
        }

        $visitsQuery = Visit::with(['visitor', 'approver']);

        // --- Tarih Filtreleme Mantığı ---
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();
            $visitsQuery->whereBetween('entry_time', [$start, $end]);
        } else {
            $dateFilter = $request->input('date_filter', 'daily');
            switch ($dateFilter) {
                case 'daily':
                    $visitsQuery->whereDate('entry_time', Carbon::today());
                    break;
                case 'monthly':
                    $visitsQuery->whereMonth('entry_time', Carbon::now()->month)
                                ->whereYear('entry_time', Carbon::now()->year);
                    break;
                case 'yearly':
                    $visitsQuery->whereYear('entry_time', Carbon::now()->year);
                    break;
                case 'all':
                default:
                    break;
            }
        }

        // --- Diğer Alan Filtreleme Mantığı ---
        foreach ($allFields as $field) {
            $value = $request->input($field . '_value');
            if ($value) {
                if (in_array($field, ['name', 'tc_no', 'phone', 'plate'])) {
                    $visitsQuery->whereHas('visitor', function ($query) use ($field, $value) {
                        $query->where($field, 'like', "%{$value}%");
                    });
                } elseif ($field === 'approved_by') {
                    $visitsQuery->whereHas('approver', function ($query) use ($value) {
                        $query->where('ad_soyad', 'like', "%{$value}%");
                    });
                } elseif ($field === 'id') { 
                    $visitsQuery->where('id', 'like', "%{$value}%");
                } else {
                    $visitsQuery->where($field, 'like', "%{$value}%");
                }
            }
        }

        $visits = $visitsQuery->get();

        Log::info('Admin ziyaretçi listeleme yaptı', [
            'user_id' => auth()->id(),
            'requested_by' => auth()->user()->username ?? 'Anonim',
            'filters' => $request->except('_token'),
            'filter_type' => $startDate ? 'custom_range' : ($request->input('date_filter') ?? 'daily'),
            'record_count' => $visits->count(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        return view('admin.index', [
            'visits' => $visits,
            'fields' => $fields,
            'allFields' => $allFields,
        ]);
    }

    public function exportPdfUnmasked(Request $request)
    {
        $allFields = [
            'id', 'entry_time', 'name', 'tc_no', 'phone', 'plate',
            'purpose', 'person_to_visit', 'approved_by'
        ];
        
        // fields parametresinden ID'yi kaldırıyoruz
        $selectedFields = array_diff($request->input('fields', $allFields), ['id']);

        if (empty($selectedFields)) {
            $selectedFields = array_diff($allFields, ['id']);
        }

        $visitsQuery = Visit::with(['visitor', 'approver']);
        
        $reportTitle = '';
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();
            $visitsQuery->whereBetween('entry_time', [$start, $end]);
            $reportTitle = Carbon::parse($startDate)->format('d.m.Y') . ' - ' . Carbon::parse($endDate)->format('d.m.Y') . ' Aralığı';
        } else {
            $dateFilter = $request->input('date_filter', 'daily');
            switch ($dateFilter) {
                case 'daily':
                    $visitsQuery->whereDate('entry_time', Carbon::today());
                    $reportTitle = 'Günlük';
                    break;
                case 'monthly':
                    $visitsQuery->whereMonth('entry_time', Carbon::now()->month)
                                ->whereYear('entry_time', Carbon::now()->year);
                    $reportTitle = 'Aylık';
                    break;
                case 'yearly':
                    $visitsQuery->whereYear('entry_time', Carbon::now()->year);
                    $reportTitle = 'Yıllık';
                    break;
                case 'all':
                default:
                    $reportTitle = 'Tüm';
                    break;
            }
        }

        foreach ($allFields as $field) {
            $searchValue = $request->input($field . '_value');
            if ($searchValue) {
                if (in_array($field, ['name', 'tc_no', 'phone', 'plate'])) {
                    $visitsQuery->whereHas('visitor', function ($query) use ($field, $searchValue) {
                        $query->where($field, 'like', "%{$searchValue}%");
                    });
                } elseif ($field === 'approved_by') {
                    $visitsQuery->whereHas('approver', function ($query) use ($searchValue) {
                        $query->where('ad_soyad', 'like', "%{$searchValue}%");
                    });
                } elseif ($field === 'id') {
                     $visitsQuery->where('id', 'like', "%{$searchValue}%");
                } else {
                    $visitsQuery->where($field, 'like', "%{$searchValue}%");
                }
            }
        }

        $sortOrder = $request->input('sort_order', 'desc');
        $visitsQuery->orderBy('entry_time', $sortOrder);
        
        $visits = $visitsQuery->get();

        $pdfData = $visits->map(function ($visit) use ($selectedFields) {
            $row = [];
            foreach ($selectedFields as $field) {
                switch ($field) {
                    // ID case'i kaldırıldı
                    case 'entry_time': $row[$field] = $visit->entry_time ? $visit->entry_time->format('Y-m-d H:i:s') : '-'; break;
                    case 'name': $row[$field] = $visit->visitor->name ?? '-'; break;
                    case 'tc_no': $row[$field] = $visit->visitor->tc_no ?? '-'; break;
                    case 'phone': $row[$field] = $visit->visitor->phone ?? '-'; break;
                    case 'plate': $row[$field] = $visit->visitor->plate ?? '-'; break;
                    case 'purpose': $row[$field] = $visit->purpose ?? '-'; break;
                    case 'person_to_visit': $row[$field] = $visit->person_to_visit ?? '-'; break;
                    case 'approved_by': $row[$field] = $visit->approver->ad_soyad ?? $visit->approved_by ?? '-'; break;
                    default: $row[$field] = $visit->$field ?? '-'; break;
                }
            }
            return $row;
        });

        $pdfHeadings = [];
        foreach ($selectedFields as $field) {
            $pdfHeadings[] = match($field) {
                'entry_time' => 'Giriş Tarihi',
                'name' => 'Ad-Soyad',
                'tc_no' => 'T.C. Kimlik No',
                'phone' => 'Telefon',
                'plate' => 'Plaka',
                'purpose' => 'Ziyaret Sebebi',
                'person_to_visit' => 'Ziyaret Edilen Kişi',
                'approved_by' => 'Ekleyen',
                default => ucfirst(str_replace('_', ' ', $field)),
            };
        }

        $fullReportTitle = $reportTitle . ' Ziyaretçi Listesi';

        Log::info('Admin PDF (unmasked) dışa aktarımı yaptı', [
            'user_id' => auth()->id(),
            'requested_by' => auth()->user()->username ?? 'Anonim',
            'filters' => $request->except('_token'),
            'field_count' => count($selectedFields),
            'record_count' => $visits->count(),
            'export_type' => 'pdf_unmasked',
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        $pdf = Pdf::loadView('pdf.unmasked_report', compact('pdfData', 'pdfHeadings', 'fullReportTitle'));
        return $pdf->download('ziyaretci_listesi.pdf');
    }
}