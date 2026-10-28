<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            InventorySeeder::class,
            ImagesSeeder::class,
            BrandSeeder::class,
            ProductsSeeder::class,
            ProduksiSetUpSeeder::class,
        ]);
    }
}
