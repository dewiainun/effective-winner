<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat Akun Admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // Buat Akun User Biasa
        User::factory()->create([
            'name' => 'Budi Customer',
            'email' => 'budi@mail.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        // Buat Beberapa Produk Dummy
        \App\Models\Product::create([
            'name' => 'Gaming Laptop',
            'description' => 'Laptop spek dewa untuk gaming dan rendering',
            'price' => 15000000,
            'status' => 'active'
        ]);

        \App\Models\Product::create([
            'name' => 'Mechanical Keyboard',
            'description' => 'Keyboard RGB switch biru',
            'price' => 750000,
            'status' => 'active'
        ]);

        \App\Models\Product::create([
            'name' => 'Mouse Wireless',
            'description' => 'Mouse tanpa kabel baterai awet',
            'price' => 250000,
            'status' => 'active'
        ]);
    }
}