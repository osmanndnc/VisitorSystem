<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;

class AdminReportController extends Controller
{
    public function generateReport(Request $request)
    {
        $fields = $request->input('fields', []); // Arayüzden seçilen alanlar
        $visits = Visit::with('visitor')->get();

        $data = $visits->map(function ($visit) use ($fields) {
            $visitor = $visit->visitor;

            return [
                'name' => in_array('name', $fields) ? $this->maskName($visitor->name) : $this->fullMask($visitor->name),
                'tc_no' => in_array('tc_no', $fields) ? $this->partialMask($visitor->tc_no, 1, 2) : $this->fullMask($visitor->tc_no),
                'phone' => in_array('phone', $fields) ? $this->partialMask($visitor->phone, 0, 2) : $this->fullMask($visitor->phone),
                'plate' => in_array('plate', $fields) ? $this->maskPlate($visitor->plate) : $this->fullMask($visitor->plate),
                'approved_by' => in_array('approved_by', $fields) ? $visit->approved_by : '*****',
                'person_to_visit' => in_array('person_to_visit', $fields) ? $visit->person_to_visit : '*****',
                'purpose' => in_array('purpose', $fields) ? $visit->purpose : '*****',
                'entry_time' => $visit->entry_time->format('Y-m-d H:i:s'),
            ];
        });

        return view('report-result', compact('data'));
    }

    private function fullMask($text)
    {
        return str_repeat('*', strlen($text));
    }

    private function partialMask($text, $visibleStart = 1, $visibleEnd = 1)
    {
        $start = substr($text, 0, $visibleStart);
        $end = substr($text, -$visibleEnd);
        $middleLength = strlen($text) - $visibleStart - $visibleEnd;
        return $start . str_repeat('*', max($middleLength, 0)) . $end;
    }

    private function maskName($fullName)
    {
        $parts = explode(' ', $fullName);
        return implode(' ', array_map(function ($part) {
            return mb_substr($part, 0, 1) . str_repeat('*', max(0, mb_strlen($part) - 1));
        }, $parts));
    }

    private function maskPlate($plate)
    {
        if (preg_match('/^(\d{2})\s*(\D+)\s*(\d+)$/', $plate, $matches)) {
            return $matches[1] . ' *** ' . str_repeat('*', strlen($matches[3]));
        }
        return $this->partialMask($plate, 2, 2);
    }
}
