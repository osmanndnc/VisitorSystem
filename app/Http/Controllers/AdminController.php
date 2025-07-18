<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;

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

        $fields = $request->input('fields', []);
        $visits = \App\Models\Visit::with(['visitor', 'approver'])->get();

        return view('admin.reports', compact('visits', 'fields', 'allFields'));
    }
}