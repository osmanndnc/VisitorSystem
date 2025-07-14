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
        Schema::create('visit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visitor_id');
            $table->dateTime('entry_time');
            $table->dateTime('exit_time')->nullable();
            $table->string('person_to_visit');
            $table->string('purpose')->nullable();
            $table->timestamps();

            
            $table->foreign('visitor_id')->references('id')->on('visitors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit');
    }
};
