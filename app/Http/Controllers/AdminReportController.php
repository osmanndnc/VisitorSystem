<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use App\Helpers\MaskHelper;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $fieldsForBlade = [
            'entry_time', 'name', 'tc_no', 'phone', 'plate',
            'purpose', 'person_to_visit'
        ];

        $visits = collect();
        $chartData = [];
        $dateFilter = '';
        $sortOrder = 'desc';

        $reportTitle = 'Tüm';
        $reportRange = 'Tüm zamanlar';

        return view('admin.reports', compact('visits', 'fieldsForBlade', 'dateFilter', 'sortOrder', 'chartData', 'reportTitle', 'reportRange'));
    }

    public function generateReport(Request $request)
    {
        Carbon::setLocale('tr');

        $allFields = [
            'entry_time',
            'name',
            'tc_no',
            'phone',
            'plate',
            'purpose',
            'person_to_visit',
            'approved_by'
        ];

        $selectedFields = $request->input('fields', $allFields);

        if (empty($selectedFields)) {
            $selectedFields = $allFields;
        }

        $dateFilter = $request->input('date_filter', '');
        $sortOrder = $request->input('sort_order', 'desc');

        $visitsQuery = Visit::with(['visitor', 'approver']);

        $reportTitle = '';
        $reportRange = '';
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();
            $visitsQuery->whereBetween('entry_time', [$start, $end]);
            $reportTitle = '';
            $reportRange = Carbon::parse($startDate)->format('d.m.Y') . ' - ' . ($endDate ? Carbon::parse($endDate)->format('d.m.Y') : 'Bugün');
        } else {
            switch ($dateFilter) {
                case 'daily':
                    $visitsQuery->whereDate('entry_time', today());
                    $reportTitle = 'Günlük';
                    $reportRange = Carbon::today()->format('d.m.Y');
                    break;
                case 'monthly':
                    $visitsQuery->whereMonth('entry_time', now()->month)
                        ->whereYear('entry_time', now()->year);
                    $reportTitle = 'Aylık';
                    $reportRange = Carbon::now()->isoFormat('MMMM YYYY');
                    break;
                case 'yearly':
                    $visitsQuery->whereYear('entry_time', now()->year);
                    $reportTitle = 'Yıllık';
                    $reportRange = Carbon::now()->format('Y');
                    break;
                case 'all':
                default:
                    $reportTitle = '';
                    $reportRange = 'Tüm zamanlar';
                    break;
            }
        }

        if ($sortOrder === 'asc') {
            $visitsQuery->orderBy('entry_time', 'asc');
        } else {
            $visitsQuery->orderBy('entry_time', 'desc');
        }

        foreach ($allFields as $field) {
            $value = $request->input($field . '_value');
            if ($value) {
                if (in_array($field, ['name', 'tc_no'])) {
                    // visitor ilişkisi üzerinden filtrele
                    $visitsQuery->whereHas('visitor', function ($query) use ($field, $value) {
                        $query->where($field, 'like', "%{$value}%");
                    });
                } elseif (in_array($field, ['phone', 'plate'])) {
                    // visit tablosundan direkt filtrele
                    $visitsQuery->where($field, 'like', "%{$value}%");
                } elseif ($field === 'approved_by') {
                    $visitsQuery->whereHas('approver', function ($query) use ($value) {
                        $query->where('ad_soyad', 'like', "%{$value}%");
                    });
                } else {
                    $visitsQuery->where($field, 'like', "%{$value}%");
                }
            }
        }

        $visits = $visitsQuery->get();

        $data = MaskHelper::maskVisits($visits, $selectedFields);

        $fieldsForBlade = $selectedFields; 

        $chartData = $this->prepareChartData($visits, $dateFilter);

        return view('admin.reports', compact('data', 'fieldsForBlade', 'dateFilter', 'sortOrder', 'chartData', 'reportTitle', 'reportRange'));
    }
    
    protected function prepareChartData($visits, $dateFilter)
    {
        $chartData = [];

        if ($dateFilter === 'daily') {
            $hourlyCounts = $visits->groupBy(function ($visit) {
                return $visit->entry_time->format('H');
            })->map->count();

            for ($i = 0; $i < 24; $i++) {
                $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
                $chartData[] = ['label' => $hour, 'count' => $hourlyCounts[$hour] ?? 0];
            }
        } elseif ($dateFilter === 'monthly') {
            $dailyCounts = $visits->groupBy(function ($visit) {
                return $visit->entry_time->format('d');
            })->map->count();

            $daysInMonth = Carbon::now()->daysInMonth;

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $day = str_pad($i, 2, '0', STR_PAD_LEFT);
                $chartData[] = ['label' => (int)$day, 'count' => $dailyCounts[$day] ?? 0];
            }
        } elseif ($dateFilter === 'yearly') {
            $monthlyCounts = $visits->groupBy(function ($visit) {
                return $visit->entry_time->format('n');
            })->map->count();

            for ($i = 1; $i <= 12; $i++) { 
                $chartData[] = ['label' => $i, 'count' => $monthlyCounts[$i] ?? 0];
            }
        } else {
            $yearlyCounts = $visits->groupBy(function ($visit) {
                return $visit->entry_time->format('Y');
            })->map->count();

            if ($visits->isNotEmpty()) {
                $minYear = $visits->min('entry_time')->year;
                $maxYear = $visits->max('entry_time')->year;
                for ($year = $minYear; $year <= $maxYear; $year++) {
                    $chartData[] = ['label' => $year, 'count' => $yearlyCounts[$year] ?? 0];
                }
            }
        }

        return array_values($chartData);
    }
    
    public function exportMaskedPdf(Request $request)
    {
        Carbon::setLocale('tr');

        $allFields = [
            'entry_time', 'name', 'tc_no', 'phone', 'plate',
            'purpose', 'person_to_visit', 'approved_by'
        ];

        $selectedFields = $request->input('fields', []);
        if (empty($selectedFields)) {
            $selectedFields = $allFields;
        }

        $dateFilter = $request->input('date_filter', '');
        $sortOrder = $request->input('sort_order', 'desc');

        $visitsQuery = Visit::with(['visitor', 'approver']);

        $reportTitle = '';
        $reportRange = '';
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();
            $visitsQuery->whereBetween('entry_time', [$start, $end]);
            $reportTitle = '';
            $reportRange = Carbon::parse($startDate)->format('d.m.Y') . ' - ' . ($endDate ? Carbon::parse($endDate)->format('d.m.Y') : 'Bugün');
        } else {
            switch ($dateFilter) {
                case 'daily':
                    $visitsQuery->whereDate('entry_time', today());
                    $reportTitle = 'Günlük';
                    $reportRange = Carbon::today()->format('d.m.Y');
                    break;
                case 'monthly':
                    $visitsQuery->whereMonth('entry_time', now()->month)
                        ->whereYear('entry_time', now()->year);
                    $reportTitle = 'Aylık';
                    $reportRange = Carbon::now()->isoFormat('MMMM YYYY');
                    break;
                case 'yearly':
                    $visitsQuery->whereYear('entry_time', now()->year);
                    $reportTitle = 'Yıllık';
                    $reportRange = Carbon::now()->format('Y');
                    break;
                case 'all':
                default:
                    $reportTitle = '';
                    $reportRange = 'Tüm zamanlar';
                    break;
            }
        }
        
        if ($sortOrder === 'asc') {
            $visitsQuery->orderBy('entry_time', 'asc');
        } else {
            $visitsQuery->orderBy('entry_time', 'desc');
        }
        
        foreach ($allFields as $field) {
            $searchValue = $request->input($field . '_value');
            if ($searchValue) {
                if (in_array($field, ['name', 'tc_no'])) {
                    $visitsQuery->whereHas('visitor', function ($query) use ($field, $searchValue) {
                        $query->where($field, 'like', "%{$searchValue}%");
                    });
                } elseif (in_array($field, ['phone', 'plate'])) {
                    $visitsQuery->where($field, 'like', "%{$searchValue}%");
                } elseif ($field === 'approved_by') {
                    $visitsQuery->whereHas('approver', function ($query) use ($searchValue) {
                        $query->where('ad_soyad', 'like', "%{$searchValue}%");
                    });
                } else {
                    $visitsQuery->where($field, 'like', "%{$searchValue}%");
                }
            }
        }

        $visits = $visitsQuery->get();

        $data = MaskHelper::maskVisits($visits, $selectedFields);
        
        $fieldsForBlade = $selectedFields;

        $pdf = Pdf::loadView('pdf.masked_pdf', compact('data', 'fieldsForBlade', 'reportTitle', 'reportRange'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans'
            ]);
        return $pdf->download('Güvenli_Rapor.pdf');
    }
}
