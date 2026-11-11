<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Batas pembayaran pesanan menunggu; setelah lewat, scheduler membatalkan pesanan dan stok dilepas.
        // Null pada pesanan lama: perilakunya tetap mengikuti batas_ambil.
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dateTime('batas_bayar')->nullable()->after('batas_ambil');
            $table->index(['status', 'batas_bayar'], 'pesanan_status_batas_bayar_index');
        });

        // Uang yang dikembalikan kepada pelanggan saat pesanan yang sudah dibayar dibatalkan.
        // Selisih pembayaran dan refund pada pesanan dibatalkan dianggap hangus (tetap menjadi pendapatan toko).
        Schema::create('refund', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->string('metode', 20); // tunai | transfer
            $table->dateTime('direfund_pada');
            $table->foreignId('dicatat_oleh')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('catatan', 255)->nullable();
            $table->timestamps();

            $table->index(['pesanan_id']);
            $table->index(['direfund_pada']);
        });

        // Cara denda dilunasi di kasir (tunai/transfer), setara dengan pencatatan metode pada pembayaran sewa.
        Schema::table('denda', function (Blueprint $table) {
            $table->string('metode_bayar', 20)->nullable()->after('status_bayar');
        });
    }

    public function down(): void
    {
        Schema::table('denda', function (Blueprint $table) {
            $table->dropColumn('metode_bayar');
        });

        Schema::dropIfExists('refund');

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropIndex('pesanan_status_batas_bayar_index');
            $table->dropColumn('batas_bayar');
        });
    }
};
