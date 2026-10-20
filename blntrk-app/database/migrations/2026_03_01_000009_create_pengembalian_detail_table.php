<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengembalian_id')->constrained('pengembalian')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('pesanan_detail_id')->unique()->constrained('pesanan_detail')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedSmallInteger('jml_baik')->default(0);
            $table->unsignedSmallInteger('jml_rusak_ringan')->default(0);
            $table->unsignedSmallInteger('jml_rusak_berat')->default(0);
            $table->unsignedSmallInteger('jml_hilang')->default(0);
            $table->string('catatan', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian_detail');
    }
};
