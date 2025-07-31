<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('visit', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('plate')->nullable();
        });

        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn(['phone', 'plate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('plate')->nullable();
        });

        Schema::table('visit', function (Blueprint $table) {
            $table->dropColumn(['phone', 'plate']);
        });
    }
};
