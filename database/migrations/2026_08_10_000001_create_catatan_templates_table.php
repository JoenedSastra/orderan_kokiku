<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Daftar catatan "Keterangan" yang PERNAH diketik admin secara manual di
 * modal Kirim Barang. Dipakai sebagai saran (bisa dipilih ulang) supaya
 * admin tidak perlu ketik ulang catatan yang sama — dan bisa dihapus satu
 * per satu lewat tombol "x" kalau sudah tidak relevan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_templates', function (Blueprint $table) {
            $table->id();
            $table->string('teks', 255)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_templates');
    }
};
