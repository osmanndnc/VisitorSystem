<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            ['reason' => 'Toplantı', 'is_active' => true],
            ['reason' => 'Ziyaret', 'is_active' => true],
            ['reason' => 'Seminer', 'is_active' => true],
            ['reason' => 'Öğrenci İşleri', 'is_active' => true],
        ];

        DB::table('visit_reasons')->insert($reasons);
    }
}
