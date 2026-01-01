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
        Schema::table('pembayaran', function (Blueprint $table) {
            // Kita tambahkan kolom foto_kwitansi setelah kolom bukti_transfer
            // Kolom ini nullable karena di awal pembayaran (saat verifikasi) foto ini belum ada
            $table->string('foto_kwitansi')->after('bukti_transfer')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn('foto_kwitansi');
        });
    }
};