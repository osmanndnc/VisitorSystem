<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Helpers\MaskHelper;

class ReportExport implements FromCollection, WithMapping, WithHeadings, WithStyles
{
    protected Collection $data;
    protected array $fields;
    protected array $masked;

    /**
     * @param Collection $data   -> Visit koleksiyonu (with visitor, approver)
     * @param array $fields      -> ['entry_time','name','tc_no','phone','plate','purpose','department','person_to_visit','approved_by']
     * @param array $masked      -> ['name','tc_no','phone','plate','department','person_to_visit'] gibi
     */
    public function __construct(Collection $data, array $fields, array $masked = [])
    {
        $this->data   = $data;
        $this->fields = $fields;
        $this->masked = $masked;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return array_map(fn($f) => match ($f) {
            'entry_time'      => 'Giriş Tarihi',
            'name'            => 'Ad-Soyad',
            'tc_no'           => 'T.C. Kimlik No',
            'phone'           => 'Telefon',
            'plate'           => 'Plaka',
            'purpose'         => 'Ziyaret Sebebi',
            'department'      => 'Ziyaret Edilen Birim',
            'person_to_visit' => 'Ziyaret Edilen Kişi',
            'approved_by'     => 'Ekleyen',
            default           => ucfirst(str_replace('_', ' ', $f)),
        }, $this->fields);
    }

    public function map($row): array
    {
        $out = [];

        foreach ($this->fields as $f) {
            // ham değer
            $val = match ($f) {
                'entry_time'      => $row->entry_time ? $row->entry_time->format('Y-m-d H:i:s') : '-',
                'name'            => optional($row->visitor)->name ?? '-',
                'tc_no'           => optional($row->visitor)->tc_no ?? '-',
                'phone'           => $row->phone ?? '-',
                'plate'           => $row->plate ?? '-',
                'purpose'         => $row->purpose ?? '-',
                'department'      => optional($row->visitor)->department->name ?? '-',
                'person_to_visit' => $row->person_to_visit ?? '-',
                'approved_by'     => optional($row->approver)->ad_soyad ?? ($row->approved_by ?? '-'),
                default           => data_get($row, $f, '-'),
            };

            // seçime göre maske uygula (telefon burada 1 baş, 2 son kalsın)
            $out[] = MaskHelper::apply($f, $val, $this->masked);
        }

        return $out;
    }

    // Basit başlık stili (opsiyonel)
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);
        return [];
    }
}