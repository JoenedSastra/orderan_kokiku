<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Data contoh sesuai infografis:
     * - Sayur & Daging & Saos dipakai Kitchen
     * - Perlengkapan dipakai Kasir
     */
    public function run(): void
    {
        $items = [
            // ---- Sayur (Kitchen) ----
            ['category' => 'Sayur', 'name' => 'Tomat',  'unit' => 'Kg', 'min_stock' => 5],
            ['category' => 'Sayur', 'name' => 'Cabai',  'unit' => 'Kg', 'min_stock' => 3],
            ['category' => 'Sayur', 'name' => 'Wortel', 'unit' => 'Kg', 'min_stock' => 5],
            ['category' => 'Sayur', 'name' => 'Kol',    'unit' => 'Kg', 'min_stock' => 5],

            // ---- Daging (Kitchen) ----
            ['category' => 'Daging', 'name' => 'Ayam',        'unit' => 'Kg', 'min_stock' => 5],
            ['category' => 'Daging', 'name' => 'Daging Sapi', 'unit' => 'Kg', 'min_stock' => 3],
            ['category' => 'Daging', 'name' => 'Ikan',        'unit' => 'Kg', 'min_stock' => 3],

            // ---- Saos (Kitchen) ----
            ['category' => 'Saos', 'name' => 'Saos Sambal', 'unit' => 'Botol', 'min_stock' => 3],
            ['category' => 'Saos', 'name' => 'Kecap',       'unit' => 'Botol', 'min_stock' => 3],
            ['category' => 'Saos', 'name' => 'Mayonaise',   'unit' => 'Botol', 'min_stock' => 2],

            // ---- Perlengkapan (Kasir) ----
            ['category' => 'Perlengkapan', 'name' => 'Gelas Plastik', 'unit' => 'Pcs',  'min_stock' => 50],
            ['category' => 'Perlengkapan', 'name' => 'Sedotan',       'unit' => 'Pack', 'min_stock' => 10],
            ['category' => 'Perlengkapan', 'name' => 'Tisu',          'unit' => 'Pack', 'min_stock' => 10],
            ['category' => 'Perlengkapan', 'name' => 'Cup',           'unit' => 'Pcs',  'min_stock' => 50],
        ];

        foreach ($items as $data) {
            $category = Category::where('name', $data['category'])->first();

            Item::updateOrCreate(
                ['name' => $data['name']],
                [
                    'category_id' => $category?->id,
                    'unit'        => $data['unit'],
                    'min_stock'   => $data['min_stock'],
                ]
            );
        }
    }
}
