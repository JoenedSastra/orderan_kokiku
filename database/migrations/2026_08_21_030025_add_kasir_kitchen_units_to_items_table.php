<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('kasir_unit', 30)->nullable()->after('unit');
            $table->string('kitchen_unit', 30)->nullable()->after('kasir_unit');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['kasir_unit', 'kitchen_unit']);
        });
    }
};
