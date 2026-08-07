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
     * Password per role:
     *   admin   → kokiku12
     *   kasir   → kokiku08
     *   kitchen → kokiku05
     */
    public function run(): void
    {
        $users = [
            [
                'name'      => 'Administrator',
                'email'     => 'admin@kokiku.test',
                'role_slug' => Role::ADMIN,
                'password'  => 'kokiku12',
            ],
            [
                'name'      => 'Kasir Depan',
                'email'     => 'kasir@kokiku.test',
                'role_slug' => Role::KASIR,
                'password'  => 'kokiku08',
            ],
            [
                'name'      => 'Staff Dapur',
                'email'     => 'kitchen@kokiku.test',
                'role_slug' => Role::KITCHEN,
                'password'  => 'kokiku05',
            ],
        ];

        foreach ($users as $data) {
            $role = Role::where('slug', $data['role_slug'])->first();

            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make($data['password']),
                    'role_id'           => $role?->id,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
