<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use App\Http\Controllers\AdminReportController;

class AdminController extends Controller
{
    public function index(Request $request)
    {
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

        $fields = $request->input('fields', []);

        $visits = Visit::with(['visitor', 'approver'])->get();

        return view('admin.index', compact('visits', 'fields', 'allFields'));

    }

    public function reports(Request $request)
    {
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

        
        $selectedFields = $request->input('fields', []); // Request'ten gelen alanlar

        $fieldsForBlade = []; 

        if (empty($selectedFields)) {
            // ID ve Onaylayan Blade tarafında zaten manuel olarak gösteriliyor.
            $fieldsForBlade = [];
        } else {
             
             $fieldsForBlade = array_values(array_filter($selectedFields, function($field) {
                return !in_array($field, ['id', 'approved_by']);
            }));
        }

        $visits = Visit::with(['visitor', 'approver'])->get();

        $adminReportController = new AdminReportController();
        //Fonksiyon çağrılmaları AdminReportController sayfasında da yapıldı. Veri aktarım hataları oluşması engellendi.

        $data = $visits->map(function ($visit) use ($selectedFields, $adminReportController) {
            $visitor = $visit->visitor;
            $row = [];

            $row['id'] = $visit->id;
            $row['approved_by'] = $visit->approver->name ?? '-';

            if (in_array('entry_time', $selectedFields)) {
                $row['entry_time'] = $visit->entry_time->format('Y-m-d H:i:s');
            }

            if (in_array('name', $selectedFields)) {
                $row['name'] = $adminReportController->maskName($visitor->name ?? '');
            }

            if (in_array('tc_no', $selectedFields)) {
                $row['tc_no'] = $adminReportController->partialMask($visitor->tc_no ?? '', 1, 2);
            }

            if (in_array('phone', $selectedFields)) {
                $row['phone'] = $adminReportController->partialMask($visitor->phone ?? '', 0, 2);
            }

            if (in_array('plate', $selectedFields)) {
                $row['plate'] = $adminReportController->maskPlate($visitor->plate ?? '');
            }

            if (in_array('purpose', $selectedFields)) {
                $row['purpose'] = $visit->purpose;
            }

            if (in_array('person_to_visit', $selectedFields)) {
                $row['person_to_visit'] = $adminReportController->maskName($visit->person_to_visit ?? '');
            }

            return $row;
        });

        return view('admin.reports', compact('data', 'fieldsForBlade'));
    }
}