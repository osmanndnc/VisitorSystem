<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    public function export(Request $request)
    {
        $fields = explode(',', $request->get('fields', ''));
        $dateFilter = $request->get('date_filter', '');
        $sortOrder = $request->get('sort_order', 'desc');

        return Excel::download(new ReportExport($fields, $dateFilter, $sortOrder), 'rapor.xlsx');
    }
}