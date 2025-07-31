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
        // Boş liste veya varsayılan sayfa
        $fieldsForBlade = [
            'entry_time', 'name', 'tc_no', 'phone', 'plate',
            'purpose', 'person_to_visit'
        ];

        $visits = collect();
        $chartData = [];
        $dateFilter = '';
        $sortOrder = 'desc';

        return view('admin.reports', compact('visits', 'fieldsForBlade', 'dateFilter', 'sortOrder', 'chartData'));
    }

    public function generateReport(Request $request)
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

        $selectedFields = $request->input('fields', $allFields);

        if (empty($selectedFields)) {
            $selectedFields = $allFields;
        }

        $dateFilter = $request->input('date_filter', '');
        $sortOrder = $request->input('sort_order', 'desc');

        $visitsQuery = Visit::with(['visitor', 'approver']);

        if ($dateFilter === 'daily') {
            $visitsQuery->whereDate('entry_time', today());
        } elseif ($dateFilter === 'monthly') {
            $visitsQuery->whereMonth('entry_time', now()->month)
                ->whereYear('entry_time', now()->year);
        } elseif ($dateFilter === 'yearly') {
            $visitsQuery->whereYear('entry_time', now()->year);
        }

        if ($sortOrder === 'asc') {
            $visitsQuery->orderBy('entry_time', 'asc');
        } else {
            $visitsQuery->orderBy('entry_time', 'desc');
        }

        $visits = $visitsQuery->get();

        $data = MaskHelper::maskVisits($visits, $selectedFields);

        $fieldsForBlade = array_values(array_filter($selectedFields, function ($field) {
            return !in_array($field, ['id', 'approved_by']);
        }));

        $chartData = $this->prepareChartData($visits, $dateFilter);

        return view('admin.reports', compact('data', 'fieldsForBlade', 'dateFilter', 'sortOrder', 'chartData'));
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


    // PDF olarak maskelenmiş raporu indir
    public function exportMaskedPdf(Request $request)
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

        $selectedFields = $request->input('fields', $allFields);

        if (empty($selectedFields)) {
            $selectedFields = $allFields;
        }

        $dateFilter = $request->input('date_filter', '');
        $sortOrder = $request->input('sort_order', 'desc');

        $visitsQuery = Visit::with(['visitor', 'approver']);

        if ($dateFilter === 'daily') {
            $visitsQuery->whereDate('entry_time', today());
        } elseif ($dateFilter === 'monthly') {
            $visitsQuery->whereMonth('entry_time', now()->month)
                ->whereYear('entry_time', now()->year);
        } elseif ($dateFilter === 'yearly') {
            $visitsQuery->whereYear('entry_time', now()->year);
        }

        if ($sortOrder === 'asc') {
            $visitsQuery->orderBy('entry_time', 'asc');
        } else {
            $visitsQuery->orderBy('entry_time', 'desc');
        }

        $visits = $visitsQuery->get();

        $data = MaskHelper::maskVisits($visits, $selectedFields);

        $fieldsForBlade = array_values(array_filter($selectedFields, function ($field) {
            return !in_array($field, ['id', 'approved_by']);
        }));

        $dateFilterTitles = [
            'daily' => 'Günlük',
            'monthly' => 'Aylık',
            'yearly' => 'Yıllık',
            '' => 'Tüm',
            null => 'Tüm',
        ];
        $reportTitle = $dateFilterTitles[$dateFilter ?? ''] ?? 'Tüm';

        $pdf = Pdf::loadView('pdf.masked_pdf', compact('data', 'fieldsForBlade', 'dateFilter'))
          ->setOptions([
              'isHtml5ParserEnabled' => true,
              'isRemoteEnabled' => true,
              'defaultFont' => 'DejaVu Sans'
          ]);
        return $pdf->download('Güvenli_Rapor.pdf');
    }
}
