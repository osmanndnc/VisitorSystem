<?php

namespace App\Services;

use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VisitService
{
    /**
     * Yeni ziyaret kaydı oluşturur.
     * - Visitor TC'ye göre bulunur veya oluşturulur.
     * - Plaka normalize edilir (""/null -> null, dolu ise UPPER).
     * - İşlem atomik yapılır (transaction).
     */
    public function store(array $data, ?int $approvedByUserId = null): Visit
    {
        return DB::transaction(function () use ($data, $approvedByUserId) {
            $visitor = Visitor::firstOrCreate(
                ['tc_no' => $data['tc_no']],
                ['name'  => $data['name']]
            );

            $plate = $this->normalizePlate($data['plate'] ?? null);

            return Visit::create([
                'visitor_id'      => $visitor->id,
                'entry_time'      => now(),
                'person_to_visit' => $data['person_to_visit'],
                'purpose'         => $data['purpose'],
                'approved_by'     => $approvedByUserId,
                'phone'           => $data['phone'],
                'plate'           => $plate,
            ]);
        });
    }

    /**
     * Mevcut ziyaret kaydını günceller.
     * - Ziyaretçi (visitor) adı/TC güncellenir.
     * - Plaka normalize edilir.
     * - Transaction ile atomik yapılır.
     */
    public function update(Visit $visit, array $data): Visit
    {
        return DB::transaction(function () use ($visit, $data) {
            $visit->visitor->update([
                'tc_no' => $data['tc_no'],
                'name'  => $data['name'],
            ]);

            $plate = $this->normalizePlate($data['plate'] ?? null);

            $visit->update([
                'entry_time'      => now(),
                'person_to_visit' => $data['person_to_visit'],
                'purpose'         => $data['purpose'],
                'phone'           => $data['phone'],
                'plate'           => $plate,
            ]);

            return $visit->refresh();
        });
    }

    /**
     * TC ile ziyaretçi geçmişini getirir.
     * - Ziyaretçi yoksa null döner (controller aynı davranışı sürdürür).
     * - Telefon/plaka listeleri null/boş filtrelenmiş ve benzersiz olarak döner.
     */
    public function getVisitorDataByTc(string $tc): ?array
    {
        $visitor = Visitor::where('tc_no', $tc)->first();

        if (!$visitor) {
            return null;
        }

        $phones = $visitor->visits()
            ->pluck('phone')
            ->filter()   // null/'' sil
            ->unique()
            ->values();

        $plates = $visitor->visits()
            ->pluck('plate')
            ->filter()
            ->unique()
            ->values();

        return [
            'name'   => $visitor->name,
            'phones' => $phones,
            'plates' => $plates,
        ];
    }

    /**
     * Plaka normalizasyonu:
     * - null veya "" => null
     * - dolu => trim + uppercase
     */
    private function normalizePlate(mixed $plate): ?string
    {
        if ($plate === null || $plate === '') {
            return null;
        }

        return Str::upper(trim((string) $plate));
    }
}
