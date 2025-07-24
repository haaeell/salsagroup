<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Barang;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'nama_depan' => 'Admin',
            'nama_belakang' => 'UTama',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'no_telepon' => '081234567890',
            'alamat' => 'Jalan Admin No.1',
            'password' => Hash::make('password'),
            'nik' => '1234567890',
            'role' => 'admin',
        ]);

        User::create([
            'nama_depan' => 'Budi',
            'nama_belakang' => 'User',
            'username' => 'budi123',
            'email' => 'budi@example.com',
            'no_telepon' => '081298765432',
            'alamat' => 'Jalan User No.2',
            'password' => Hash::make('password'),
            'nik' => '0987654321',
            'role' => 'user',
        ]);

        // Kategori
        $k1 = Kategori::create(['nama' => 'Cat Interior']);
        $k2 = Kategori::create(['nama' => 'Cat Eksterior']);

        // Barang
        Barang::create([
            'gambar' => null,
            'kode' => 'CATINT001',
            'nama' => 'Cat Tembok Putih 5L',
            'kategori_id' => $k1->id,
            'harga' => 150000,
            'stok' => 20,
        ]);

        Barang::create([
            'gambar' => null,
            'kode' => 'CATEKS001',
            'nama' => 'Cat Tembok Eksterior Merah 5L',
            'kategori_id' => $k2->id,
            'harga' => 200000,
            'stok' => 15,
        ]);
    }
}
