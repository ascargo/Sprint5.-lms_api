<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 👇 Create one admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@domus.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // 👇 Create several patron users
        User::factory(3)->create([
            'role' => 'patron',
        ]);
    }
}
