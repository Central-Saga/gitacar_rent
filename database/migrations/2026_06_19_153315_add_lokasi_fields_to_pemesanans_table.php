<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->string('lokasi_url')->nullable()->after('catatan');
            $table->text('lokasi_deskripsi')->nullable()->after('lokasi_url');
        });
    }

    public function down(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropColumn(['lokasi_url', 'lokasi_deskripsi']);
        });
    }
};
