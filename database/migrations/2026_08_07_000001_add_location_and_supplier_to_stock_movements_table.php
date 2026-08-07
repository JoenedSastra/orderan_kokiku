<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('item_id')->constrained()->nullOnDelete();
            $table->enum('location', ['gudang', 'restoran'])->default('restoran')->after('user_id');
        });

        Schema::table('stock_outs', function (Blueprint $table) {
            $table->enum('location', ['gudang', 'restoran'])->default('restoran')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropColumn('location');
        });

        Schema::table('stock_outs', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};
