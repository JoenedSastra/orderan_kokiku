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
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->string('location')->default('gudang_utama')->change();
        });
        Schema::table('stock_outs', function (Blueprint $table) {
            $table->string('location')->default('gudang_utama')->change();
        });
    }

    public function down(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            // Reverting to enum requires knowing original values, typically easier to just leave as string or rollback if strictly needed
            $table->enum('location', ['gudang', 'restoran'])->default('restoran')->change();
        });
        Schema::table('stock_outs', function (Blueprint $table) {
            $table->enum('location', ['gudang', 'restoran'])->default('restoran')->change();
        });
    }
};
