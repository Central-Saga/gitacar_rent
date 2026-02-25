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
        Schema::create('kendaraan_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained()->cascadeOnDelete();
            $table->string('nomor_plat')->unique();
            $table->year('tahun');
            $table->enum('status_unit', ['tersedia', 'disewa', 'maintenance', 'nonaktif'])->default('tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan_units');
    }
};
