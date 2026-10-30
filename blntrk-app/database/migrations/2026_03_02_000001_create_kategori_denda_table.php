<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_denda', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama', 100);
            $table->string('jenis', 30)->default('kerusakan'); // keterlambatan, kehilangan, kerusakan, lainnya
            $table->decimal('tarif_standar', 12, 2)->default(0);
            $table->string('satuan', 50)->default('per unit'); // per hari, per unit, per kejadian
            $table->text('deskripsi')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        Schema::table('denda', function (Blueprint $table) {
            $table->foreignId('kategori_denda_id')->nullable()->after('pesanan_id')->constrained('kategori_denda')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('pengembalian_id')->nullable()->change();
            $table->string('jenis', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('denda', function (Blueprint $table) {
            $table->dropForeign(['kategori_denda_id']);
            $table->dropColumn('kategori_denda_id');
        });

        Schema::dropIfExists('kategori_denda');
    }
};
