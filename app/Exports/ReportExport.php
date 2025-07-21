<?php

namespace App\Exports;

use App\Models\Visit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection,WithHeadings
{
    protected $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function collection()
    {
        $visits = Visit::with('visitor', 'approver')->get();
        return $visits->map(function ($visit) {
            $row = [];

            
            $row[] = $visit->id;
            $row[] = $visit->approver->name ?? '-';

            // Dinamik olarak sadece seçilen alanları ekle (id ve approved_by hariç)
            foreach ($this->fields as $field) {
                switch ($field) {
                    case 'entry_time':
                        $row[] = $visit->entry_time;
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
                case 'entry_time':
                    $headings[] = 'Giriş Tarihi';
                    break;
                case 'name':
                    $headings[] = 'Ad-Soyad';
                    break;
                case 'tc_no':
                    $headings[] = 'TC';
                    break;
                case 'phone':
                    $headings[] = 'Telefon';
                    break;
                case 'plate':
                    $headings[] = 'Plaka';
                    break;
                case 'purpose':
                    $headings[] = 'Ziyaret Sebebi';
                    break;
                case 'person_to_visit':
                    $headings[] = 'Ziyaret Edilen Kişi';
                    break;
                default:
                    $headings[] = ucfirst(str_replace('_', ' ', $field));
                    break;
            }
        }
        return $headings;
    }

    // Maskeleme fonksiyonları (Bu fonksiyonlara dokunulmadı)
    private function maskName($fullName)
    {
        $parts = explode(' ', $fullName);
        return implode(' ', array_map(fn($p) => mb_substr($p, 0, 1) . str_repeat('*', mb_strlen($p) - 1), $parts));
    }

    private function maskTc($tc)
    {
        return substr($tc, 0, 3) . str_repeat('*', strlen($tc) - 5) . substr($tc, -2);
    }

    private function maskPhone($phone)
    {
        return substr($phone, 0, 4) . str_repeat('*', strlen($phone) - 7) . substr($phone, -3);
    }

    private function maskPlate($plate)
    {
        return substr($plate, 0, 2) . str_repeat('*', strlen($plate) - 4) . substr($plate, -2);
    }
}