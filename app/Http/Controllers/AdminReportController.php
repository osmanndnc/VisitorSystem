<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $visits = Visit::with('visitor', 'approver')->get();
        $fields = $request->old('fields', []);

        return view('report.index', compact('visits', 'fields'));
    }

    public function generateReport(Request $request)
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
        $selectedFields = $request->input('fields', []);

        $visits = Visit::with('visitor', 'approver')->get();

        $data = $visits->map(function ($visit) use ($selectedFields) {
            $visitor = $visit->visitor;
            $row = [];

            $row['id'] = $visit->id;
            $row['approved_by'] = $visit->approver->name ?? '-';

            if (in_array('entry_time', $selectedFields)) {
                $row['entry_time'] = $visit->entry_time->format('Y-m-d H:i:s');
            }
            if (in_array('name', $selectedFields)) {
                $row['name'] = $this->maskName($visitor->name ?? '');
            }
            if (in_array('tc_no', $selectedFields)) {
                $row['tc_no'] = $this->partialMask($visitor->tc_no ?? '', 1, 2);
            }
            if (in_array('phone', $selectedFields)) {
                $row['phone'] = $this->partialMask($visitor->phone ?? '', 0, 2);
            }
            if (in_array('plate', $selectedFields)) {
                $row['plate'] = $this->maskPlate($visitor->plate ?? '');
            }
            if (in_array('purpose', $selectedFields)) {
                $row['purpose'] = $visit->purpose;
            }
            if (in_array('person_to_visit', $selectedFields)) {
                $row['person_to_visit'] = $this->maskName($visit->person_to_visit ?? '');
            }

            return $row;
        });

        $fieldsForBlade = array_values(array_filter($selectedFields, function($field) {
            return !in_array($field, ['id', 'approved_by']);
        }));

        return view('admin.reports', compact('data', 'fieldsForBlade'));
    }


    //Sadece ilk karakteri gösterecek şekilde maskeleme foksiyonu:
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

    //Girilen İsimleri Maskeleme Fonksiyonu:
    public function maskName($fullName)
    {
        if (!$fullName) return '';
        $parts = explode(' ', $fullName);
        $maskedParts = array_map(function ($part) {
            return mb_substr($part, 0, 1) . str_repeat('*', max(mb_strlen($part) - 1, 0));
        }, $parts);
        return implode(' ', $maskedParts);
    }

    //Plaka Maskeleme Fonksiyonu:
    public function maskPlate($plate)
    {
        if (!$plate) return '';
        if (preg_match('/^(\d{2})\s*(\D+)\s*(\d+)$/', $plate, $matches)) {
            return $matches[1] . ' *** ' . str_repeat('*', strlen($matches[3]));
        }
        return $this->partialMask($plate, 2, 2);
    }
}