<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data  = [
            // Transaksi penjualan_id = 1
            [
                'detail_id' => 1,
                'penjualan_id' => 1,
                'barang_id' => 1,  // Kacang
                'harga' => 10000,
                'jumlah' => 5,
            ],
            [
                'detail_id' => 2,
                'penjualan_id' => 1,
                'barang_id' => 8,  // Pena
                'harga' => 5000,
                'jumlah' => 10,
            ],
            [
                'detail_id' => 3,
                'penjualan_id' => 1,
                'barang_id' => 3,  // Sapu
                'harga' => 20000,
                'jumlah' => 2,
            ],
        
            // Transaksi penjualan_id = 2
            [
                'detail_id' => 4,
                'penjualan_id' => 2,
                'barang_id' => 7,  // Rusk Cookies
                'harga' => 25000,
                'jumlah' => 3,
            ],
            [
                'detail_id' => 5,
                'penjualan_id' => 2,
                'barang_id' => 11, // Setip
                'harga' => 3000,
                'jumlah' => 15,
            ],
            [
                'detail_id' => 6,
                'penjualan_id' => 2,
                'barang_id' => 9,  // Penggorengan
                'harga' => 85000,
                'jumlah' => 1,
            ],
        
            // Transaksi penjualan_id = 3
            [
                'detail_id' => 7,
                'penjualan_id' => 3,
                'barang_id' => 4,  // Blender
                'harga' => 250000,
                'jumlah' => 1,
            ],
            [
                'detail_id' => 8,
                'penjualan_id' => 3,
                'barang_id' => 13, // Biskuit
                'harga' => 12000,
                'jumlah' => 4,
            ],
            [
                'detail_id' => 9,
                'penjualan_id' => 3,
                'barang_id' => 5,  // Pelumas
                'harga' => 30000,
                'jumlah' => 2,
            ],
        
            // Transaksi penjualan_id = 4
            [
                'detail_id' => 10,
                'penjualan_id' => 4,
                'barang_id' => 2,  // Buku Tulis
                'harga' => 15000,
                'jumlah' => 6,
            ],
            [
                'detail_id' => 11,
                'penjualan_id' => 4,
                'barang_id' => 12, // Panci Kukus
                'harga' => 95000,
                'jumlah' => 1,
            ],
            [
                'detail_id' => 12,
                'penjualan_id' => 4,
                'barang_id' => 15, // Kipas Angin
                'harga' => 225000,
                'jumlah' => 1,
            ],
        
            // Transaksi penjualan_id = 5
            [
                'detail_id' => 13,
                'penjualan_id' => 5,
                'barang_id' => 6,  // Baterai Motor
                'harga' => 180000,
                'jumlah' => 2,
            ],
            [
                'detail_id' => 14,
                'penjualan_id' => 5,
                'barang_id' => 10, // Rice Cooker
                'harga' => 350000,
                'jumlah' => 1,
            ],
            [
                'detail_id' => 15,
                'penjualan_id' => 5,
                'barang_id' => 1,  // Kacang
                'harga' => 10000,
                'jumlah' => 8,
            ],
        
            // Transaksi penjualan_id = 6
            [
                'detail_id' => 16,
                'penjualan_id' => 6,
                'barang_id' => 14, // Spion Kendaraan
                'harga' => 45000,
                'jumlah' => 2,
            ],
            [
                'detail_id' => 17,
                'penjualan_id' => 6,
                'barang_id' => 8,  // Pena
                'harga' => 5000,
                'jumlah' => 12,
            ],
            [
                'detail_id' => 18,
                'penjualan_id' => 6,
                'barang_id' => 3,  // Sapu
                'harga' => 20000,
                'jumlah' => 3,
            ],
        
            // Transaksi penjualan_id = 7
            [
                'detail_id' => 19,
                'penjualan_id' => 7,
                'barang_id' => 7,  // Rusk Cookies
                'harga' => 25000,
                'jumlah' => 5,
            ],
            [
                'detail_id' => 20,
                'penjualan_id' => 7,
                'barang_id' => 9,  // Penggorengan
                'harga' => 85000,
                'jumlah' => 2,
            ],
            [
                'detail_id' => 21,
                'penjualan_id' => 7,
                'barang_id' => 11, // Setip
                'harga' => 3000,
                'jumlah' => 20,
            ],
        
            // Transaksi penjualan_id = 8
            [
                'detail_id' => 22,
                'penjualan_id' => 8,
                'barang_id' => 13, // Biskuit
                'harga' => 12000,
                'jumlah' => 10,
            ],
            [
                'detail_id' => 23,
                'penjualan_id' => 8,
                'barang_id' => 2,  // Buku Tulis
                'harga' => 15000,
                'jumlah' => 8,
            ],
            [
                'detail_id' => 24,
                'penjualan_id' => 8,
                'barang_id' => 15, // Kipas Angin
                'harga' => 225000,
                'jumlah' => 1,
            ],
        
            // Transaksi penjualan_id = 9
            [
                'detail_id' => 25,
                'penjualan_id' => 9,
                'barang_id' => 5,  // Pelumas
                'harga' => 30000,
                'jumlah' => 3,
            ],
            [
                'detail_id' => 26,
                'penjualan_id' => 9,
                'barang_id' => 12, // Panci Kukus
                'harga' => 95000,
                'jumlah' => 2,
            ],
            [
                'detail_id' => 27,
                'penjualan_id' => 9,
                'barang_id' => 1,  // Kacang
                'harga' => 10000,
                'jumlah' => 6,
            ],
        
            // Transaksi penjualan_id = 10
            [
                'detail_id' => 28,
                'penjualan_id' => 10,
                'barang_id' => 4,  // Blender
                'harga' => 250000,
                'jumlah' => 1,
            ],
            [
                'detail_id' => 29,
                'penjualan_id' => 10,
                'barang_id' => 6,  // Baterai Motor
                'harga' => 180000,
                'jumlah' => 1,
            ],
            [
                'detail_id' => 30,
                'penjualan_id' => 10,
                'barang_id' => 14, // Spion Kendaraan
                'harga' => 45000,
                'jumlah' => 2,
            ],
        ];

        DB::table('t_penjualan_detail')->insert($data);
    }
}
