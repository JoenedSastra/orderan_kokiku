<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Catatan "Keterangan" yang pernah diketik admin secara manual, disimpan
 * murni sebagai teks bebas — TIDAK ada teks otomatis/sistem yang ikut
 * tersimpan di sini. Dipakai sebagai daftar saran yang bisa dipilih ulang
 * atau dihapus di modal Kirim Barang.
 */
class CatatanTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['teks'];
}
