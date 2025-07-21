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


        return Excel::download(new ReportExport($fields), 'rapor.xlsx');
    }
}