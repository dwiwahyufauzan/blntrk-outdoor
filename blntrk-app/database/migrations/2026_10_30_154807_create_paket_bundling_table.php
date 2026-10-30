<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('paket_bundling', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('nomor_paket')->default(1); // 1, 2, 3
            $table->string('kode_paket', 20)->unique(); // PKT-01, PKT-02, PKT-03
            $table->string('nama', 100); // Paket 1: Solo Backpacker, Paket 2: Duo Adventure, dll.
            $table->string('slug', 120)->unique();
            $table->string('kapasitas_orang', 50)->default('1-2 Orang');
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->decimal('harga_paket_per_hari', 12, 2);
            $table->decimal('harga_asli_per_hari', 12, 2)->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();
        });

        Schema::create('paket_bundling_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_bundling_id')->constrained('paket_bundling')->cascadeOnDelete();
            $table->foreignId('alat_id')->constrained('alat')->cascadeOnDelete();
            $table->unsignedInteger('jumlah')->default(1);
            $table->timestamps();

            $table->unique(['paket_bundling_id', 'alat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_bundling_detail');
        Schema::dropIfExists('paket_bundling');
    }
};
