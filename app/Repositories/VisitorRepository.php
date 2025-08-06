<?php

namespace App\Repositories;

use App\Models\Visitor;
use App\Models\Visit;

class VisitorRepository
{
    /**
     * Yeni ziyaretçi ve ziyaret kaydını oluşturur.
     */
    public function createVisit(array $data, $user)
    {
        $visitor = Visitor::firstOrCreate(
            ['tc_no' => $data['tc_no']],
            ['name' => $data['name']]
        );

        Visit::create([
            'visitor_id' => $visitor->id,
            'entry_time' => now(),
            'person_to_visit' => $data['person_to_visit'],
            'purpose' => $data['purpose'],
            'approved_by' => $user->id,
            'phone' => $data['phone'],
            'plate' => strtoupper($data['plate']),
        ]);
    }

    /**
     * Var olan ziyaret ve ziyaretçi bilgisini günceller.
     */
    public function updateVisit($id, array $data)
    {
        $visit = Visit::with('visitor')->findOrFail($id);

        $visit->visitor->update([
            'name' => $data['name'],
            'tc_no' => $data['tc_no'],
        ]);

        $visit->update([
            'entry_time' => now(),
            'person_to_visit' => $data['person_to_visit'],
            'purpose' => $data['purpose'],
            'phone' => $data['phone'],
            'plate' => strtoupper($data['plate']),
        ]);
    }

    /**
     * T.C. numarasına göre kullanıcı adı;  geçmiş telefon ve plaka bilgilerini getirir.
     */
    public function getVisitorDataByTc($tc)
    {
        $visitor = Visitor::where('tc_no', $tc)->first();

        if (!$visitor) return null;

        return [
            'name' => $visitor->name,
            'phones' => Visit::where('visitor_id', $visitor->id)->pluck('phone')->unique()->values(),
            'plates' => Visit::where('visitor_id', $visitor->id)->pluck('plate')->unique()->values(),
        ];
    }
}
