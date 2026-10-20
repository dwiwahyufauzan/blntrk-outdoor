<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('no_resi', 20)->unique();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('dibuat_oleh')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedSmallInteger('durasi_hari');
            $table->dateTime('jatuh_tempo');
            $table->dateTime('batas_ambil');
            $table->enum('status', ['menunggu', 'dibayar', 'disewa', 'terlambat', 'dikembalikan', 'dibatalkan'])->default('menunggu');
            $table->decimal('total_sewa', 12, 2);
            $table->dateTime('diambil_pada')->nullable();
            $table->dateTime('dibatalkan_pada')->nullable();
            $table->string('alasan_batal', 255)->nullable();
            $table->string('catatan', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'tanggal_mulai', 'tanggal_selesai']);
            $table->index('pelanggan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
