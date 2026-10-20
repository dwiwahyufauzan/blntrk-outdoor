<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('alat_id')->constrained('alat')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedSmallInteger('jumlah');
            $table->decimal('harga_per_hari', 10, 2); // snapshot harga historis
            $table->decimal('subtotal', 12, 2);

            $table->unique(['pesanan_id', 'alat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_detail');
    }
};
