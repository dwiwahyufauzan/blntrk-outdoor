<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bukti transfer yang diunggah pelanggan (disimpan di disk privat)
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('bukti_transfer', 255)->nullable()->after('catatan');
            $table->dateTime('bukti_diunggah_pada')->nullable()->after('bukti_transfer');
            // Penanda pengingat otomatis agar tiap pelanggan hanya dikirimi satu kali per jenis
            $table->dateTime('pengingat_kembali_pada')->nullable();
            $table->dateTime('pengingat_terlambat_pada')->nullable();
        });

        // Jaminan berupa deposit uang (alternatif kartu identitas) mencatat nominalnya di sini
        Schema::table('jaminan', function (Blueprint $table) {
            $table->decimal('nominal', 12, 2)->nullable()->after('keterangan');
        });

        // Pembayaran awal (sewa) dibedakan dari tambahan biaya perpanjangan
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('jenis', 20)->default('sewa')->after('metode'); // sewa | perpanjangan
        });

        // Riwayat keluar-masuk perawatan alat (menggantikan angka stok_perawatan yang tanpa jejak)
        Schema::create('riwayat_perawatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_id')->constrained('alat')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('jenis', 20); // masuk | selesai
            $table->unsignedInteger('jumlah');
            $table->string('keterangan', 255)->nullable();
            $table->foreignId('pesanan_id')->nullable()->constrained('pesanan')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();

            $table->index(['alat_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_perawatan');

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });

        Schema::table('jaminan', function (Blueprint $table) {
            $table->dropColumn('nominal');
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['bukti_transfer', 'bukti_diunggah_pada', 'pengingat_kembali_pada', 'pengingat_terlambat_pada']);
        });
    }
};
