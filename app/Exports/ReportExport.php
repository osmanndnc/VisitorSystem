<?php

namespace App\Exports;

use App\Models\Visit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class ReportExport implements FromCollection,WithHeadings
{
    protected $fields;
    protected $dateFilter;
    protected $sortOrder; 

    public function __construct(array $fields, $dateFilter = '', $sortOrder = 'desc') // Yeni: sortOrder eklendi
    {
        $this->fields = $fields;
        $this->dateFilter = $dateFilter;
        $this->sortOrder = $sortOrder;
    }

    public function collection()
    {
        $visitsQuery = Visit::with('visitor', 'approver');

        if (!empty($this->dateFilter)) {
            if ($this->dateFilter === 'daily') {
                $visitsQuery->whereDate('entry_time', today());
            } elseif ($this->dateFilter === 'monthly') {
                $visitsQuery->whereMonth('entry_time', today()->month)
                            ->whereYear('entry_time', today()->year);
            } elseif ($this->dateFilter === 'yearly') {
                $visitsQuery->whereYear('entry_time', today()->year);
            }
        }

        if ($this->sortOrder === 'asc') {
            $visitsQuery->orderBy('entry_time', 'asc');
        } else {
            $visitsQuery->orderBy('entry_time', 'desc');
        }
        
        $visits = $visitsQuery->get();

        return $visits->map(function ($visit) {
            $row = [];

            $row[] = $visit->id;
            $row[] = $visit->approver->name ?? '-';

            // Dinamik olarak sadece seçilen alanları ekle (id ve approved_by hariç)
            foreach ($this->fields as $field) {
                switch ($field) {
                    case 'entry_time':
                        $row[] = $visit->entry_time ? $visit->entry_time->format('Y-m-d H:i:s') : '-';
                        break;
                    case 'name':
                        $row[] = $this->maskName($visit->visitor->name ?? '');
                        break;
                    case 'tc_no':
                        $row[] = $this->maskTc($visit->visitor->tc_no ?? '');
                        break;
                    case 'phone':
                        $row[] = $this->maskPhone($visit->visitor->phone ?? '');
                        break;
                    case 'plate':
                        $row[] = $this->maskPlate($visit->visitor->plate ?? '');
                        break;
                    case 'purpose':
                        $row[] = $visit->purpose;
                        break;
                    case 'person_to_visit':
                        $row[] = $this->maskName($visit->person_to_visit ?? '');
                        break;
                    // 'approved_by' ve 'id' zaten yukarıda eklendiği için burada tekrar eklenmiyor
                }
            }

            return collect($row);
        });
    }

    public function headings(): array
    {
        $headings = [
            'ID',
            'Onaylayan',
        ];

        // Dinamik olarak seçilen alan başlıklarını eklendi (id ve approved_by hariç)
        foreach ($this->fields as $field) {
            switch ($field) {
                case 'entry_time': $headings[] = 'Giriş Tarihi'; break;
                case 'name': $headings[] = 'Ad-Soyad'; break;
                case 'tc_no': $headings[] = 'TC'; break;
                case 'phone': $headings[] = 'Telefon'; break;
                case 'plate': $headings[] = 'Plaka'; break;
                case 'purpose': $headings[] = 'Ziyaret Sebebi'; break;
                case 'person_to_visit': $headings[] = 'Ziyaret Edilen Kişi'; break;
                default: $headings[] = ucfirst(str_replace('_', ' ', $field)); break;
            }
        }
        return $headings;
    }

    
    private function maskName($fullName)
    {
        if (!is_string($fullName) || empty($fullName)) return '';
        $parts = explode(' ', $fullName);
        $maskedParts = array_map(function ($part) {
            return mb_substr($part, 0, 1) . str_repeat('*', max(mb_strlen($part) - 1, 0));
        }, $parts);
        return implode(' ', $maskedParts);
    }

    private function maskTc($tc)
    {
        return is_string($tc) && !empty($tc) ? substr($tc, 0, 3) . str_repeat('*', strlen($tc) - 5) . substr($tc, -2) : '';
    }

    private function maskPhone($phone)
    {
        return is_string($phone) && !empty($phone) ? substr($phone, 0, 4) . str_repeat('*', strlen($phone) - 7) . substr($phone, -3) : '';
    }

    private function maskPlate($plate)
    {
        return is_string($plate) && !empty($plate) ? (preg_match('/^(\d{2})\s*(\D+)\s*(\d+)$/', $plate, $matches) ? $matches[1] . ' *** ' . str_repeat('*', strlen($matches[3])) : $this->partialMask($plate, 2, 2)) : '';
    }
    
    private function partialMask($text, $visibleStart = 1, $visibleEnd = 1) { return is_string($text) && !empty($text) ? mb_substr($text, 0, $visibleStart) . str_repeat('*', mb_strlen($text) - $visibleStart - $visibleEnd) . mb_substr($text, -$visibleEnd) : ''; }
}