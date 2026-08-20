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
        Schema::table('items', function (Blueprint $table) {
            $table->double('kasir_keluar')->nullable();
            $table->double('kasir_stock')->nullable();
            $table->double('kasir_last_masuk')->nullable();
            $table->double('kitchen_keluar')->nullable();
            $table->double('kitchen_stock')->nullable();
            $table->double('kitchen_last_masuk')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'kasir_keluar', 'kasir_stock', 'kasir_last_masuk',
                'kitchen_keluar', 'kitchen_stock', 'kitchen_last_masuk'
            ]);
        });
    }
};
