<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->foreignId('paket_bundling_id')->nullable()->after('pelanggan_id')
                ->constrained('paket_bundling')->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedSmallInteger('jumlah_paket')->default(0)->after('total_sewa');
            $table->decimal('diskon_paket', 12, 2)->default(0)->after('jumlah_paket');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('paket_bundling_id');
            $table->dropColumn(['jumlah_paket', 'diskon_paket']);
        });
    }
};
