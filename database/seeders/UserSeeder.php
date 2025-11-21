<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@domus.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::factory()->count(3)->create([
            'role' => 'patron',
        ]);
    }
}
