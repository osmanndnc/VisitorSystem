<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Tüm alanlar
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

        // Checkboxlar işaretli gelmesin, sadece formdan gelirse dolsun
        $fields = $request->input('fields', []);

        // İlişkili verilerle birlikte tüm ziyaretleri çek
        $visits = Visit::with(['visitor', 'approver'])->get();

        // allFields'ı da blade'e gönder
        return view('admin.index', compact('visits', 'fields', 'allFields'));
    }
}