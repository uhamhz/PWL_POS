<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'barang_id' => 1,
                'barang_kode' => 'KCGABC',
                'barang_nama' => 'Kacang',
                'kategori_id' => 1,
                'harga_beli' => 8000,
                'harga_jual' => 10000,
            ],
            [
                'barang_id' => 2,
                'barang_kode' => 'BKTXYZ', 
                'barang_nama' => 'Buku Tulis',
                'kategori_id' => 2,
                'harga_beli' => 12000,
                'harga_jual' => 15000,
            ],
            [
                'barang_id' => 3,
                'barang_kode' => 'SPLMN',
                'barang_nama' => 'Sapu',
                'kategori_id' => 3,
                'harga_beli' => 15000,
                'harga_jual' => 20000,
            ],
            [
                'barang_id' => 4,
                'barang_kode' => 'BLDABC',
                'barang_nama' => 'Blender',
                'kategori_id' => 4,
                'harga_beli' => 200000,
                'harga_jual' => 250000,
            ],
            [
                'barang_id' => 5,
                'barang_kode' => 'PLMXYZ',
                'barang_nama' => 'Pelumas Rantai Kendaraan',
                'kategori_id' => 5,
                'harga_beli' => 25000,
                'harga_jual' => 30000,
            ],
            [
                'barang_id' => 6,
                'barang_kode' => 'BTRLMN',
                'barang_nama' => 'Baterai Motor',
                'kategori_id' => 5,
                'harga_beli' => 150000,
                'harga_jual' => 180000,
            ],
            [
                'barang_id' => 7,
                'barang_kode' => 'RSKABC',
                'barang_nama' => 'Rusk Cookies',
                'kategori_id' => 1,
                'harga_beli' => 20000,
                'harga_jual' => 25000,
            ],
            [
                'barang_id' => 8,
                'barang_kode' => 'PNAXYZ',
                'barang_nama' => 'Pena',
                'kategori_id' => 2,
                
                'harga_beli' => 4000,
                'harga_jual' => 5000,
            ],
            [
                'barang_id' => 9,
                'barang_kode' => 'PNCLMN',
                'barang_nama' => 'Penggorengan',
                'kategori_id' => 3,
                'harga_beli' => 70000,
                'harga_jual' => 85000,
            ],
            [
                'barang_id' => 10,
                'barang_kode' => 'RCEABC',
                'barang_nama' => 'Rice Cooker',
                'kategori_id' => 4,
                'harga_beli' => 300000,
                'harga_jual' => 350000,
            ],
            [
                'barang_id' => 11,
                'barang_kode' => 'STPXYZ',
                'barang_nama' => 'Setip',
                'kategori_id' => 2,
                
                'harga_beli' => 2000,
                'harga_jual' => 3000,
            ],
            [
                'barang_id' => 12,
                'barang_kode' => 'PKRLMN',
                'barang_nama' => 'Panci Kukus',
                'kategori_id' => 3,
                'harga_beli' => 80000,
                'harga_jual' => 95000,
            ],
            [
                'barang_id' => 13,
                'barang_kode' => 'BSKABC',
                'barang_nama' => 'Biskuit',
                'kategori_id' => 1,
                'harga_beli' => 10000,
                'harga_jual' => 12000,
            ],
            [
                'barang_id' => 14,
                'barang_kode' => 'SPKXYZ',
                'barang_nama' => 'Spion Kendaraan',
                'kategori_id' => 5,
                
                'harga_beli' => 35000,
                'harga_jual' => 45000,
            ],
            [
                'barang_id' => 15,
                'barang_kode' => 'KPSLMN',
                'barang_nama' => 'Kipas Angin',
                'kategori_id' => 4,
                'harga_beli' => 180000,
                'harga_jual' => 225000,
            ],
        ];
        DB::table('m_barang')->insert($data);
    }
}
