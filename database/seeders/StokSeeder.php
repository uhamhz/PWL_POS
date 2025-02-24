<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'stok_id' => 1,
                'barang_id' => 1,
                'user_id' => 1,
                'stok_tanggal' => '2024-02-20 09:00:00',
                'stok_jumlah' => 50,
            ],
            [
                'stok_id' => 2,
                'barang_id' => 2,
                'user_id' => 2,
                'stok_tanggal' => '2024-02-20 10:15:00',
                'stok_jumlah' => 100,
            ],
            [
                'stok_id' => 3,
                'barang_id' => 3,
                'user_id' => 1,
                'stok_tanggal' => '2024-02-20 11:30:00',
                'stok_jumlah' => 30,
            ],
            [
                'stok_id' => 4,
                'barang_id' => 4,
                'user_id' => 2,
                'stok_tanggal' => '2024-02-21 09:45:00',
                'stok_jumlah' => 15,
            ],
            [
                'stok_id' => 5,
                'barang_id' => 5,
                'user_id' => 3,
                'stok_tanggal' => '2024-02-21 13:20:00',
                'stok_jumlah' => 75,
            ],
            [
                'stok_id' => 6,
                'barang_id' => 6,
                'user_id' => 1,
                'stok_tanggal' => '2024-02-21 14:30:00',
                'stok_jumlah' => 25,
            ],
            [
                'stok_id' => 7,
                'barang_id' => 7,
                'user_id' => 2,
                'stok_tanggal' => '2024-02-22 08:15:00',
                'stok_jumlah' => 80,
            ],
            [
                'stok_id' => 8,
                'barang_id' => 8,
                'user_id' => 3,
                'stok_tanggal' => '2024-02-22 10:45:00',
                'stok_jumlah' => 200,
            ],
            [
                'stok_id' => 9,
                'barang_id' => 9,
                'user_id' => 1,
                'stok_tanggal' => '2024-02-22 13:00:00',
                'stok_jumlah' => 20,
            ],
            [
                'stok_id' => 10,
                'barang_id' => 10,
                'user_id' => 2,
                'stok_tanggal' => '2024-02-23 09:30:00',
                'stok_jumlah' => 15,
            ],
            [
                'stok_id' => 11,
                'barang_id' => 11,
                'user_id' => 3,
                'stok_tanggal' => '2024-02-23 11:20:00',
                'stok_jumlah' => 150,
            ],
            [
                'stok_id' => 12,
                'barang_id' => 12,
                'user_id' => 1,
                'stok_tanggal' => '2024-02-23 14:45:00',
                'stok_jumlah' => 25,
            ],
            [
                'stok_id' => 13,
                'barang_id' => 13,
                'user_id' => 2,
                'stok_tanggal' => '2024-02-24 08:30:00',
                'stok_jumlah' => 100,
            ],
            [
                'stok_id' => 14,
                'barang_id' => 14,
                'user_id' => 3,
                'stok_tanggal' => '2024-02-24 10:15:00',
                'stok_jumlah' => 40,
            ],
            [
                'stok_id' => 15,
                'barang_id' => 15,
                'user_id' => 1,
                'stok_tanggal' => '2024-02-24 13:45:00',
                'stok_jumlah' => 30,
            ],
        ];

        DB::table('t_stok')->insert($data);
    }
}
