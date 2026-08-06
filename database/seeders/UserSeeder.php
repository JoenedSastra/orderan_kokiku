<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Membuat 1 user default untuk masing-masing role supaya bisa langsung
     * dipakai untuk testing login. Password default: "password".
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@kokiku.test',
                'role_slug' => Role::ADMIN,
            ],
            [
                'name' => 'Kasir Depan',
                'email' => 'kasir@kokiku.test',
                'role_slug' => Role::KASIR,
            ],
            [
                'name' => 'Staff Dapur',
                'email' => 'kitchen@kokiku.test',
                'role_slug' => Role::KITCHEN,
            ],
        ];

        foreach ($users as $data) {
            $role = Role::where('slug', $data['role_slug'])->first();

            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $role?->id,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
