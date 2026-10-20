<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('pengembalian_id')->constrained('pengembalian')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('pengembalian_detail_id')->nullable()->constrained('pengembalian_detail')->cascadeOnUpdate()->nullOnDelete();
            $table->enum('jenis', ['keterlambatan', 'kerusakan']);
            $table->string('keterangan', 255)->nullable();
            $table->decimal('jumlah', 12, 2);
            $table->enum('status_bayar', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->dateTime('dilunasi_pada')->nullable();
            $table->foreignId('dilunasi_oleh')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denda');
    }
};
