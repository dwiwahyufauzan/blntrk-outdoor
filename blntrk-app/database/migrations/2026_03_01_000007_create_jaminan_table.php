<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jaminan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->unique()->constrained('pesanan')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('jenis', ['ktp', 'sim', 'kartu_pelajar', 'lainnya']);
            $table->string('nomor_identitas_samar', 30); // 4 digit terakhir untuk privasi
            $table->string('keterangan', 255)->nullable();
            $table->enum('status', ['dititipkan', 'dikembalikan'])->default('dititipkan');
            $table->dateTime('diterima_pada');
            $table->foreignId('diterima_oleh')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->dateTime('dikembalikan_pada')->nullable();
            $table->foreignId('dikembalikan_oleh')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jaminan');
    }
};
