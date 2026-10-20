<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->enum('metode', ['tunai', 'transfer'])->default('tunai');
            $table->dateTime('dibayar_pada');
            $table->foreignId('dicatat_oleh')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('catatan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
