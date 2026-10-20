<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->unique()->constrained('pesanan')->cascadeOnUpdate()->restrictOnDelete();
            $table->dateTime('dikembalikan_pada');
            $table->unsignedSmallInteger('hari_terlambat')->default(0);
            $table->foreignId('diterima_oleh')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
