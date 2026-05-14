<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'user_code' => 'Admin2026',
            'name' => 'Ian',
            'lastname' => 'Mavulule',
            'phone' => '867732237',
            'email' => 'ian@gmail.com',
            'role' => 'admin',
            'password' => bcrypt('123456'),
        ]);
    }
}