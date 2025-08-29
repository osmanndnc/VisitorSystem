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
        Schema::table('visitors', function (Blueprint $table) {
            // Önce foreign key constraint'i kaldır
            $table->dropForeign(['approved_by']);
            
            // Sonra sütunu kaldır
            $table->dropColumn('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            // Sütunu geri ekle
            $table->unsignedBigInteger('approved_by')->after('tc_no');
            
            // Foreign key constraint'i geri ekle
            $table->foreign('approved_by')->references('id')->on('users');
        });
    }
};