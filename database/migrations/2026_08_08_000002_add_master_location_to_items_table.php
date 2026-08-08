<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * master_location = master barang mana yang "memiliki" barang ini:
     * gudang_utama | gudang_resto | kasir | kitchen.
     * Ditentukan langsung oleh Admin saat mencatat Barang Masuk Gudang.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('master_location')->nullable()->after('category_id');
        });

        // Backfill data lama berdasarkan kategori, supaya barang yang sudah
        // ada tetap muncul di tab Master Barang yang sesuai (bukan hilang).
        $kasirCategoryIds = DB::table('categories')->where('used_by', 'kasir')->pluck('id');
        DB::table('items')->whereIn('category_id', $kasirCategoryIds)->update(['master_location' => 'kasir']);

        $kitchenCategoryIds = DB::table('categories')->where('used_by', 'kitchen')->pluck('id');
        DB::table('items')->whereIn('category_id', $kitchenCategoryIds)->update(['master_location' => 'kitchen']);

        // Sisanya (tanpa kategori / kategori umum) default ke Gudang Resto.
        DB::table('items')->whereNull('master_location')->update(['master_location' => 'gudang_resto']);
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('master_location');
        });
    }
};
