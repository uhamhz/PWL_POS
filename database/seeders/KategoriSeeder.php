<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kategori_id' => 1, 'kategori_kode' => 'MKN', 'kategori_nama' => 'Makanan & Minuman'],
            ['kategori_id' => 2, 'kategori_kode' => 'BPL', 'kategori_nama' => 'Buku & Peralatan'],
            ['kategori_id' => 3, 'kategori_kode' => 'PRT', 'kategori_nama' => 'Peralatan Rumah Tangga'],
            ['kategori_id' => 4, 'kategori_kode' => 'ELE', 'kategori_nama' => 'Elektronik'],
            ['kategori_id' => 5, 'kategori_kode' => 'OTR', 'kategori_nama' => 'Otomotif'],   
        ];

        DB::table('m_kategori')->insert($data);
    }
}
