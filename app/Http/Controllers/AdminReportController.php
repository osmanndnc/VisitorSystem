<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use Carbon\Carbon;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $visits = collect(); 
        $fields = $request->old('fields', []);
        $dateFilter = $request->input('date_filter', '');
        $sortOrder = $request->input('sort_order', 'desc');

        $fieldsForBlade = [
            'entry_time', 'name', 'tc_no', 'phone', 'plate',
            'purpose', 'person_to_visit'
        ];
        
        if ($request->has('fields') && !empty($request->input('fields'))) {
            $fieldsForBlade = array_values(array_filter($request->input('fields'), function($field) {
                return !in_array($field, ['id', 'approved_by']);
            }));
        }

        return view('admin.reports', compact('visits', 'fieldsForBlade', 'dateFilter', 'sortOrder'));
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
        
        $selectedFields = $request->input('fields', []);

        // Eğer hiçbir alan seçilmemişse, tüm alanları kullan
        if (empty($selectedFields)) {
            $selectedFields = $allFields;
        }

        $dateFilter = $request->input('date_filter', '');
        $sortOrder = $request->input('sort_order', 'desc');

        $visitsQuery = Visit::with(['visitor', 'approver']);

        // TARİH FİLTRELEME
        if ($dateFilter === 'daily') {
            $visitsQuery->whereDate('entry_time', today());
        } elseif ($dateFilter === 'monthly') {
            $visitsQuery->whereMonth('entry_time', now()->month)
                        ->whereYear('entry_time', now()->year);
        } elseif ($dateFilter === 'yearly') {
            $visitsQuery->whereYear('entry_time', now()->year);
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

        if ($sortOrder === 'asc') {
            $visitsQuery->orderBy('entry_time', 'asc');
        } else {
            $visitsQuery->orderBy('entry_time', 'desc');
        }
        
        $visits = $visitsQuery->get();

        $data = $visits->map(function ($visit) use ($selectedFields) {
            $visitor = $visit->visitor;
            $row = [];

            // ID ve Onaylayan her zaman gösterilecek
            $row['id'] = $visit->id;
            $row['approved_by'] = $visit->approver->username ?? $visit->approved_by ?? '-';

            // Sadece seçilen alanları doldur
            foreach ($selectedFields as $field) {
                if (in_array($field, ['id', 'approved_by'])) { // id ve approved_by zaten eklendi
                    continue;
                }

                switch ($field) {
                    case 'entry_time':
                        $row['entry_time'] = $visit->entry_time ? $visit->entry_time->format('Y-m-d H:i:s') : '-';
                        break;
                    case 'name':
                        $row['name'] = $this->maskName($visitor->name ?? '');
                        break;
                    case 'tc_no':
                        $row['tc_no'] = $this->partialMask($visitor->tc_no ?? '', 1, 2);
                        break;
                    case 'phone':
                        $row['phone'] = $this->partialMask($visitor->phone ?? '', 0, 2);
                        break;
                    case 'plate':
                        $row['plate'] = $this->maskPlate($visitor->plate ?? '');
                        break;
                    case 'purpose':
                        $row['purpose'] = $visit->purpose ?? '-';
                        break;
                    case 'person_to_visit':
                        $row['person_to_visit'] = $this->maskName($visit->person_to_visit ?? '');
                        break;
                    default:
                        $row[$field] = $visit->$field ?? '-';
                        break;
                }
            }

            return $row;
        });

        $fieldsForBlade = array_values(array_filter($selectedFields, function($field) {
            return !in_array($field, ['id', 'approved_by']);
        }));

        return view('admin.reports', compact('data', 'fieldsForBlade', 'dateFilter', 'sortOrder'));
    }

    public function fullMask($text)
    {
        return str_repeat('*', mb_strlen($text));
    }

    public function partialMask($text, $visibleStart = 1, $visibleEnd = 1)
    {
        if (!$text) return '';
        $length = mb_strlen($text);
        if ($length <= $visibleStart + $visibleEnd) {
            return str_repeat('*', $length);
        }
        $start = mb_substr($text, 0, $visibleStart);
        $end = mb_substr($text, -$visibleEnd);
        $middle = str_repeat('*', $length - $visibleStart - $visibleEnd);
        return $start . $middle . $end;
    }

    public function maskName($fullName)
    {
        if (!$fullName) return '';
        $parts = explode(' ', $fullName);
        $maskedParts = array_map(function ($part) {
            return mb_substr($part, 0, 1) . str_repeat('*', max(mb_strlen($part) - 1, 0));
        }, $parts);
        return implode(' ', $maskedParts);
    }

    public function maskPlate($plate)
    {
        if (!$plate) return '';
        if (preg_match('/^(\d{2})\s*(\D+)\s*(\d+)$/', $plate, $matches)) {
            return $matches[1] . ' ' . str_repeat('*', mb_strlen($matches[2])) . ' ' . str_repeat('*', mb_strlen($matches[3]));
        }
        return $this->partialMask($plate, 2, 2);
    }
}