<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menandai kategori itu dipakai oleh Kasir, Kitchen, atau Umum (keduanya).
     * Dipakai untuk menghitung Stok Kasir & Stok Kitchen secara otomatis
     * berdasarkan kategori barang, tanpa perlu tracking lokasi baru.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('used_by')->default('umum')->after('name'); // kasir | kitchen | umum
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('used_by');
        });
    }
};
