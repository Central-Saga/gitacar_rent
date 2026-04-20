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
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->dateTime('waktu_kembali')->nullable();
            $table->enum('tipe_harga', ['harian', 'mingguan', 'bulanan'])->default('harian');
            $table->integer('harga_sewa'); // This will store the price per unit (day/week/month)
            $table->integer('harga_per_hari');
            $table->integer('total_harga');
            $table->integer('denda_per_hari')->default(0);
            $table->integer('hari_terlambat')->default(0);
            $table->integer('denda')->default(0);
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
