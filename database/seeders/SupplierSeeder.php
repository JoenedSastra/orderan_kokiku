<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name'    => 'CV Sumber Segar',
                'phone'   => '081234567801',
                'address' => 'Pasar Induk Sidoarjo, Blok A No. 12',
            ],
            [
                'name'    => 'PT Saos Nusantara',
                'phone'   => '081234567802',
                'address' => 'Jl. Industri Raya No. 45, Sidoarjo',
            ],
            [
                'name'    => 'Toko Perlengkapan Jaya',
                'phone'   => '081234567803',
                'address' => 'Jl. Diponegoro No. 8, Sidoarjo',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(['name' => $supplier['name']], $supplier);
        }
    }
}
