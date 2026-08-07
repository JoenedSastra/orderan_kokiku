<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Sayur, Daging, Saos = kategori bahan yang dipakai Kitchen.
     * Perlengkapan = kategori barang yang dipakai Kasir (gelas, sedotan, tisu, dll).
     */
    public function run(): void
    {
        $categories = ['Sayur', 'Daging', 'Saos', 'Perlengkapan'];

        foreach ($categories as $name) {
            Category::updateOrCreate(['name' => $name]);
        }
    }
}
