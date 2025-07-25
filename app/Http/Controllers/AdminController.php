<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;

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

        // Hangi kolonlar görünecek?
        $fields = $allFields;
        if ($request->has('filter')) {
            $fields = explode(',', $request->input('filter'));
            $fields = array_intersect($fields, $allFields); // Güvenlik için
            if (empty($fields)) $fields = $allFields;
        }

        // Filtre değerlerini al
        $filters = [];
        foreach ($allFields as $field) {
            $key = $field . '_value';
            if ($request->has($key) && trim($request->$key) !== '') {
                $filters[$field] = $request->$key;
            }
        }

        // Sorgu (sadece bugünün kayıtları)
        $visitsQuery = Visit::with(['visitor', 'approver'])
            ->whereDate('entry_time', today());

        // Filtreleri uygula
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
}