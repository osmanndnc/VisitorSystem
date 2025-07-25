<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    public function export(Request $request)
    {
        $fields = $request->input('fields', []); 
        
        if (is_string($fields)) { 
            $fields = explode(',', $fields);
        }
        $fields = array_filter($fields); // Boş elemanları temizle

        $dateFilter = $request->get('date_filter', '');
        $sortOrder = $request->get('sort_order', 'desc');
        $unmasked = $request->boolean('unmasked');

        return Excel::download(new ReportExport($fields, $dateFilter, $sortOrder, $unmasked), 'rapor.xlsx');
    }
}