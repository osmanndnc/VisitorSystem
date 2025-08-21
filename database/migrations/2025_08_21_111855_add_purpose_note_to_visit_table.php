<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visit', function (Blueprint $table) {
            if (!Schema::hasColumn('visits', 'purpose_note')) { // visit veya visits
                $table->string('purpose_note', 500)
                      ->nullable()
                      ->after('purpose');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit', function (Blueprint $table) {
            if (Schema::hasColumn('visits', 'purpose_note')) {  // visit veya visits
                $table->dropColumn('purpose_note');
            }
        });
    }
};
