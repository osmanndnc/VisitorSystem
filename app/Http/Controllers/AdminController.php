<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $allFields = [
            'id',
            'entry_time',
            'name',
            'tc_no',
            'phone',
            'plate',
            'purpose',
            'person_to_visit',
            'approved_by'
        ];
        
        $fields = $allFields;
        if ($request->has('filter')) {
            $fields = explode(',', $request->input('filter'));
            $fields = array_intersect($fields, $allFields); // Güvenlik için
            if (empty($fields)) $fields = $allFields;
        }

        $filters = [];
        foreach ($allFields as $field) {
            $key = $field . '_value';
            if ($request->has($key) && trim($request->$key) !== '') {
                $filters[$field] = $request->$key;
            }
        }

        $visitsQuery = Visit::with(['visitor', 'approver']);

        // Günlük, Aylık, Yıllık filtrelemeyi ekle
        $dateFilter = $request->input('date_filter', 'daily'); // Varsayılan

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
        }

        // Diğer filtreleri uygula
        foreach ($filters as $field => $value) {
            if (in_array($field, ['name', 'tc_no', 'phone', 'plate'])) {
                $visitsQuery->whereHas('visitor', function ($query) use ($field, $value) {
                    $query->where($field, 'like', "%{$value}%");
                });
            } elseif ($field === 'approved_by') {
                $visitsQuery->whereHas('approver', function ($query) use ($value) {
                    $query->where('username', 'like', "%{$value}%");
                });
            } else {
                $visitsQuery->where($field, 'like', "%{$value}%");
            }
        }

        $visits = $visitsQuery->get();

        return view('admin.index', [
            'visits' => $visits,
            'fields' => $fields,
            'allFields' => $allFields,
            'filters' => $filters,
            'filterFields' => array_keys($filters)
        ]);
    }

    public function exportPdfUnmasked(Request $request)
    {
        $allFields = [
            'id', 'entry_time', 'name', 'tc_no', 'phone', 'plate',
            'purpose', 'person_to_visit', 'approved_by'
        ];
        $selectedFields = $request->input('fields', []);

        // Eğer fields[] parametresi boş gelirse (hiçbir filtre seçilmemişse), tüm alanları kullan
        if (empty($selectedFields)) {
            $selectedFields = $allFields;
        }

        $visitsQuery = Visit::with(['visitor', 'approver']);

        // Tarih filtresi
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
                        $query->where('username', 'like', "%{$searchValue}%");
                    });
                } else {
                    $visitsQuery->where($field, 'like', "%{$searchValue}%");
                }
            }
        }

        $sortOrder = $request->input('sort_order', 'desc');
        $visitsQuery->orderBy('entry_time', $sortOrder);
        
        $visits = $visitsQuery->get();

        // PDF için verileri formatlanması
        $pdfData = $visits->map(function ($visit) use ($selectedFields) {
            $row = [];
            foreach ($selectedFields as $field) {
                switch ($field) {
                    case 'id': $row[$field] = $visit->id ?? '-'; break;
                    case 'entry_time': $row[$field] = $visit->entry_time ? $visit->entry_time->format('Y-m-d H:i:s') : '-'; break;
                    case 'name': $row[$field] = $visit->visitor->name ?? '-'; break;
                    case 'tc_no': $row[$field] = $visit->visitor->tc_no ?? '-'; break;
                    case 'phone': $row[$field] = $visit->visitor->phone ?? '-'; break;
                    case 'plate': $row[$field] = $visit->visitor->plate ?? '-'; break;
                    case 'purpose': $row[$field] = $visit->purpose ?? '-'; break;
                    case 'person_to_visit': $row[$field] = $visit->person_to_visit ?? '-'; break;
                    case 'approved_by': $row[$field] = $visit->approver->username ?? $visit->approved_by ?? '-'; break;
                    default: $row[$field] = $visit->$field ?? '-'; break;
                }
            }
            return $row;
        });

        $pdfHeadings = [];
        foreach ($selectedFields as $field) {
            $pdfHeadings[] = match($field) {
                'id' => 'ID',
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

        $reportTitle = '';
        switch ($dateFilter) {
            case 'daily': $reportTitle = 'Günlük'; break;
            case 'monthly': $reportTitle = 'Aylık'; break;
            case 'yearly': $reportTitle = 'Yıllık'; break;
            default: $reportTitle = 'Tüm'; break;
        }
        $fullReportTitle = $reportTitle . ' Ziyaretçi Listesi';
        $pdf = Pdf::loadView('pdf.unmasked_report', compact('pdfData', 'pdfHeadings', 'fullReportTitle'));
        return $pdf->download('ziyaretci_listesi.pdf');
    }
}