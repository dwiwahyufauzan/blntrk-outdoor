<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            // Dipakai scheduler sinkronisasi status (terlambat & kedaluwarsa) dan filter dashboard
            $table->index(['status', 'jatuh_tempo'], 'pesanan_status_jatuh_tempo_index');
            $table->index(['status', 'batas_ambil'], 'pesanan_status_batas_ambil_index');
        });

        Schema::table('pesanan_detail', function (Blueprint $table) {
            // Agregasi pemakaian stok per alat
            $table->index(['alat_id', 'pesanan_id'], 'pesanan_detail_alat_pesanan_index');
        });

        Schema::table('alat', function (Blueprint $table) {
            $table->index(['is_aktif', 'nama'], 'alat_aktif_nama_index');
        });

        Schema::table('denda', function (Blueprint $table) {
            $table->index(['pesanan_id', 'status_bayar'], 'denda_pesanan_status_bayar_index');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropIndex('pesanan_status_jatuh_tempo_index');
            $table->dropIndex('pesanan_status_batas_ambil_index');
        });
        Schema::table('pesanan_detail', fn (Blueprint $t) => $t->dropIndex('pesanan_detail_alat_pesanan_index'));
        Schema::table('alat', fn (Blueprint $t) => $t->dropIndex('alat_aktif_nama_index'));
        Schema::table('denda', fn (Blueprint $t) => $t->dropIndex('denda_pesanan_status_bayar_index'));
    }
};
