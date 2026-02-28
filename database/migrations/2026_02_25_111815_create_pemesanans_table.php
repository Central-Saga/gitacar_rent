<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kendaraan_unit_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('harga_per_hari');
            $table->integer('total_harga');
            $table->enum('status_pemesanan', ['menunggu_konfirmasi', 'disetujui', 'ditolak', 'dibatalkan', 'selesai'])->default('menunggu_konfirmasi');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
