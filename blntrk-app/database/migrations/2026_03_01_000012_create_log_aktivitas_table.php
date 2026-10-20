<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->string('aksi', 50);
            $table->string('modul', 50);
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->string('deskripsi', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->dateTime('created_at');

            $table->index(['modul', 'ref_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};
