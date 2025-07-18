<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\User;

class VisitorSeeder extends Seeder
{
    public function run()
    {
        $user = User::first() ?? User::factory()->create(['name' => 'Admin Kullanıcı']);

        $visitor = new Visitor();
        $visitor->name = 'Ali Veli';
        $visitor->tc_no = '12345678901';
        $visitor->phone = '05551234567';
        $visitor->plate = '34ABC123';
        $visitor->save();

        $visit = new Visit();
        $visit->visitor_id = $visitor->id;
        $visit->entry_time = now();
        $visit->purpose = 'Toplantı';
        $visit->person_to_visit = 'Mehmet Güneş';
        $visit->approved_by = $user->id;
        $visit->save();
    }
}
