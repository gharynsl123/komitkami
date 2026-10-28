<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $brands = [
            [
                'user_id' => 1,
                'nama_merek' => 'Alkessi',
            ],
            [
                'user_id' => 2,
                'nama_merek' => 'frics',
            ],
            [
                'user_id' => 3,
                'nama_merek' => 'cerplast',
            ],
            [
                'user_id' => 4,
                'nama_merek' => 'deca',
            ],
            [
                'user_id' => 5,
                'nama_merek' => 'chrona care',
            ],
            [
                'user_id' => 6,
                'nama_merek' => 'medizsisma',
            ],
            [
                'user_id' => 7,
                'nama_merek' => 'king',
            ],
            [
                'user_id' => 8,
                'nama_merek' => 'diarra',
            ],
            [
                'user_id' => 9,
                'nama_merek' => 'force-x',
            ],
            [
                'user_id' => 10,
                'nama_merek' => 'ok-med',
            ],
        ];

        foreach ($brands as $brand) {
            DB::table('brand')->insert([
                'id_user' => $brand['user_id'],
                'name' => $brand['nama_merek'],
                'created_at' => Carbon::now(),
            ]);
        }
    }
}
