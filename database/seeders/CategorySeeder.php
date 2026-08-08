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
        $categories = [
            'Sayur'        => Category::USED_BY_KITCHEN,
            'Daging'       => Category::USED_BY_KITCHEN,
            'Saos'         => Category::USED_BY_KITCHEN,
            'Perlengkapan' => Category::USED_BY_KASIR,
        ];

        foreach ($categories as $name => $usedBy) {
            Category::updateOrCreate(['name' => $name], ['used_by' => $usedBy]);
        }
    }
}
