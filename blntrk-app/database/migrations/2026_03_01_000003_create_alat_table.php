<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_alat')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('kode_alat', 20)->unique();
            $table->string('nama', 100);
            $table->string('varian', 50)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('foto', 255)->nullable();
            $table->decimal('harga_per_hari', 10, 2);
            $table->unsignedInteger('stok_total')->default(0);
            $table->unsignedInteger('stok_perawatan')->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
