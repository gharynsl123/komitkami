<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\User;
use App\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roles = [
            'Customer',
            'Production Manager',
            'Inventory Manager',
            'Quality Control',
            'Sales',
            'Supervisor',
            'Producer',
            'Admin'
        ];

        foreach ($roles as $role) {
            DB::table('role')->insert([
                'role_name' => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $users = [
            [
                'name' => 'PT. PRIMA ALKESINDO NUSANTARA',
                'username' => 'Alkessi',
                'password' => 'prima',
                'tipe' => 'khusus',
                'roles' => ['Customer']
            ],
            [
                'name' => 'PT FRISANSHA ANUGERAH ALKESINDO',
                'username' => 'frics',
                'password' => 'frics123',
                'tipe' => 'khusus',
                'roles' => ['Customer']
            ],
            [
                'name' => 'PT GRACIA PHARMINDO',
                'username' => 'gracia',
                'password' => 'cerplast',
                'tipe' => 'khusus',
                'roles' => ['Customer']
            ],
            [
                'name' => 'PT LAMI INDO MEDIKA',
                'username' => 'lami',
                'password' => 'deca',
                'tipe' => 'khusus',
                'roles' => ['Customer']
            ],
            [
                'name' => 'PT BUANA INTIPRIMA USAHA',
                'username' => 'chrona',
                'password' => 'buana',
                'tipe' => 'khusus',
                'roles' => ['Customer']
            ],
            [
                'name' => 'PT MEDIZA INSAN BATAVIA',
                'username' => 'medizsisma',
                'password' => 'medizsisma123',
                'tipe' => 'khusus',
                'roles' => ['Customer']
            ],
            [
                'name' => 'PT KOMITKAMI INTINUSA GEMILANG',
                'username' => 'king',
                'password' => 'komitkami',
                'tipe' => 'khusus',
                'roles' => ['Customer']
            ],
            [
                'name' => 'PT TRISTANISA GLOBAL INDONESIA',
                'username' => 'tristanisa',
                'password' => 'diarra',
                'tipe' => 'khusus',
                'roles' => ['Customer']
            ],
            [
                'name' => 'PT SHAMEER INDO MEDIKA',
                'username' => 'shameer',
                'password' => 'force-x',
                'tipe' => 'khusus',
                'roles' => ['Customer']
            ],
            [
                'name' => 'PT SANAYA MEDIKA PRATAMA',
                'username' => 'sanaya',
                'password' => 'ok-med',
                'tipe' => 'khusus',
                'roles' => ['Customer']
            ],
            [
                'name' => 'Ananda Gharyn',
                'username' => 'admin',
                'email' => 'persolna1243@gmail.com',
                'address' => 'none',
                'password' => '120122',
                'roles' => ['Admin']
            ],
            [
                'name' => 'Dedi Maryadi',
                'username' => 'dedi',
                'email' => 'dedi@gmail.com',
                'address' => 'none',
                'password' => 'dedi123',
                'roles' => ['Admin']
            ],
            [
                'name' => 'Bu Tania',
                'username' => 'tania',
                'email' => 'tania@gmail.com',
                'address' => 'none',
                'password' => 'tania123',
                'roles' => ['Admin']
            ],
            [
                'name' => 'Maura Adelya',
                'username' => 'adel123',
                'email' => 'maura@gmail.com',
                'address' => 'none',
                'password' => 'adel123',
                'roles' => ['Sales', 'Production Manager']
            ],
            [
                'name' => 'Operator Produksi',
                'username' => 'produksi',
                'address' => 'none',
                'password' => 'produksi123',
                'roles' => ['Producer']
            ],
            [
                'name' => 'Pak Bagus',
                'username' => 'bagus',
                'phone_number' => '0812897367453',
                'address' => 'none',
                'password' => 'sales123',
                'roles' => ['Sales']
            ],
            [
                'name' => 'Yuni',
                'username' => 'yuni',
                'password' => 'yuni123',
                'roles' => ['Production Manager']
            ],
            [
                'name' => 'Tio Darmawan',
                'username' => 'tio',
                'phone_number' => '081232736934',
                'address' => 'none',
                'password' => 'tio123',
                'roles' => ['Supervisor']
            ],
            [
                'name' => 'Pak ahmand',
                'username' => 'ahmad',
                'phone_number' => '081232736934',
                'address' => 'none',
                'password' => 'gudang123',
                'roles' => ['Inventory Manager']
            ],
            [
                'name' => 'Liyan',
                'username' => 'Liyan',
                'password' => 'LiyanPass',
                'roles' => ['Quality Control']
            ],
            [
                'name' => 'Tsabita',
                'username' => 'Tsabita',
                'password' => 'TsabitaPass',
                'roles' => ['Quality Control']
            ],
        ];

        foreach ($users as $data) {
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'tipe' => $data['tipe'] ?? 'general',
                'email' => $data['email'] ?? null,
                'password' => Hash::make($data['password']),
                'raw_pass' => $data['password'] ?? null,
                'created_at' => Carbon::now(),
            ]);

            $roleIds = Role::whereIn('role_name', $data['roles'])->pluck('id');
            $user->roles()->attach($roleIds);
        }
    }
}
