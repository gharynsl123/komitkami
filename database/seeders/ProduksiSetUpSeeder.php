<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProduksiSetUpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('ruang_produksi')->insert([
            [
                'nama_ruangan' => 'Ruang Penimbangan', 
                'created_at' => Carbon::now()
            ],
            [
                'nama_ruangan' => 'Ruang Mixing', 
                'created_at' => Carbon::now()
            ],
            [
                'nama_ruangan' => 'Ruang Filling', 
                'created_at' => Carbon::now()
            ]
        ]);

        DB::table('bagian_kebersihan')->insert([
            [
                'ruang_produksi_id' => 1,
                'nama_bagian' => 'Timbangan',
                'created_at' => Carbon::now()
            ],
            [
                'ruang_produksi_id' => 1,
                'nama_bagian' => 'Wadah Bahan Baku',
                'created_at' => Carbon::now()
            ],
            [
                'ruang_produksi_id' => 1,
                'nama_bagian' => 'Lantai',
                'created_at' => Carbon::now()
            ],
            [
                'ruang_produksi_id' => 1,
                'nama_bagian' => 'Selang Bahan Baku',
                'created_at' => Carbon::now()
            ],
            [
                'ruang_produksi_id' => 1,
                'nama_bagian' => 'Pompa Bahan Baku',
                'created_at' => Carbon::now()
            ],
            [
                'ruang_produksi_id' => 2,
                'nama_bagian' => 'Mesin Mixing',
                'created_at' => Carbon::now()
            ],
            [
                'ruang_produksi_id' => 2,
                'nama_bagian' => 'Lantai',
                'created_at' => Carbon::now()
            ],
            [
                'ruang_produksi_id' => 3,
                'nama_bagian' => 'Mesin Filling',
                'created_at' => Carbon::now()
            ],
            [
                'ruang_produksi_id' => 3,
                'nama_bagian' => 'Lantai',
                'created_at' => Carbon::now()
            ],
            [
                'ruang_produksi_id' => 3,
                'nama_bagian' => 'Selang Produk',
                'created_at' => Carbon::now()
            ],
            [
                'ruang_produksi_id' => 3,
                'nama_bagian' => 'Meja Filling',
                'created_at' => Carbon::now()
            ],
            [
                'ruang_produksi_id' => 3,
                'nama_bagian' => 'Tangki Mixing',
                'created_at' => Carbon::now()
            ],
        ]);
    }
}
