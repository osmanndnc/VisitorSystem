<?php

namespace App\Exports;

use App\Models\Visit;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Http\Request;

class ReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $fields; // Excel'e aktarılacak seçili alanlar
    protected $dateFilter;
    protected $sortOrder;
    protected $request; 
    protected $unmasked; 

    public function __construct(array $fields, $dateFilter = '', $sortOrder = 'desc', $unmasked = false)
    {
        $this->fields = $fields;
        $this->dateFilter = $dateFilter;
        $this->sortOrder = $sortOrder;
        $this->request = request();
        $this->unmasked = $unmasked;
    }

    public function collection()
    {
        $visitsQuery = Visit::with('visitor', 'approver');

        // TARİH FİLTRELEME
        if ($this->dateFilter === 'daily') {
            $visitsQuery->whereDate('entry_time', Carbon::today());
        } elseif ($this->dateFilter === 'monthly') {
            $visitsQuery->whereMonth('entry_time', Carbon::now()->month)
                        ->whereYear('entry_time', Carbon::now()->year);
        } elseif ($this->dateFilter === 'yearly') {
            $visitsQuery->whereYear('entry_time', Carbon::now()->year);
        }

        // ARAMA FİLTRELEME
        $allPossibleFieldsForSearch = [
            'id', 'entry_time', 'name', 'tc_no', 'phone', 'plate',
            'purpose', 'person_to_visit', 'approved_by'
        ];
        
        foreach ($allPossibleFieldsForSearch as $field) {
            $searchValue = $this->request->input($field . '_value');
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

        // SIRALAMA
        $visitsQuery->orderBy('entry_time', $this->sortOrder);

        return $visitsQuery->get();
    }

    public function map($visit): array
    {
        $row = [];
        $row[] = $visit->id ?? '-';
        $row[] = $this->unmasked ? ($visit->approver->username ?? '-') : ($visit->approver->username ?? '-');
        
        foreach ($this->fields as $field) {
            // ID ve approved_by zaten eklendiği için tekrarlamıyoruz
            if (in_array($field, ['id', 'approved_by'])) {
                continue;
            }

            switch ($field) {
                case 'entry_time':
                    $row[] = $visit->entry_time ? $visit->entry_time->format('Y-m-d H:i:s') : '-';
                    break;
                case 'name':
                    $row[] = $this->unmasked ? ($visit->visitor->name ?? '-') : $this->maskName($visit->visitor->name ?? '');
                    break;
                case 'tc_no':
                    $row[] = $this->unmasked ? ($visit->visitor->tc_no ?? '-') : $this->partialMask($visit->visitor->tc_no ?? '', 1, 2);
                    break;
                case 'phone':
                    $row[] = $this->unmasked ? ($visit->visitor->phone ?? '-') : $this->partialMask($visit->visitor->phone ?? '', 0, 2);
                    break;
                case 'plate':
                    $row[] = $this->unmasked ? ($visit->visitor->plate ?? '-') : $this->maskPlate($visit->visitor->plate ?? '');
                    break;
                case 'purpose':
                    $row[] = $visit->purpose ?? '-';
                    break;
                case 'person_to_visit':
                    $row[] = $this->unmasked ? ($visit->person_to_visit ?? '-') : $this->maskName($visit->person_to_visit ?? '');
                    break;
                default:
                    $row[] = $visit->$field ?? '-';
                    break;
            }
        }
        return $row;
    }

    public function headings(): array
    {
        $headings = ['ID', 'Ekleyen'];
        foreach ($this->fields as $field) {
            if (in_array($field, ['id', 'approved_by'])) {
                continue;
            }

            $headings[] = match($field) {
                'entry_time' => 'Giriş Tarihi',
                'name' => 'Ad-Soyad',
                'tc_no' => 'T.C. Kimlik No',
                'phone' => 'Telefon',
                'plate' => 'Plaka',
                'purpose' => 'Ziyaret Sebebi',
                'person_to_visit' => 'Ziyaret Edilen Kişi',
                default => ucfirst(str_replace('_', ' ', $field)),
            };
        }
        return $headings;
    }
    
    private function fullMask($text)
    {
        return str_repeat('*', mb_strlen($text));
    }

    private function partialMask($text, $visibleStart = 1, $visibleEnd = 1)
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

    private function maskName($fullName)
    {
        if (!$fullName) return '';
        $parts = explode(' ', $fullName);
        $maskedParts = array_map(function ($part) {
            return mb_substr($part, 0, 1) . str_repeat('*', max(mb_strlen($part) - 1, 0));
        }, $parts);
        return implode(' ', $maskedParts);
    }

    private function maskPlate($plate)
    {
        if (!$plate) return '';
        if (preg_match('/^(\d{2})\s*(\D+)\s*(\d+)$/', $plate, $matches)) {
            return $matches[1] . ' ' . str_repeat('*', mb_strlen($matches[2])) . ' ' . str_repeat('*', mb_strlen($matches[3]));
        }
        return $this->partialMask($plate, 2, 2);
    }
}