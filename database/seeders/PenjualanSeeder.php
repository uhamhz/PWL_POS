<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'penjualan_id' => 1,
                'user_id' => 3,
                'pembeli' => 'Budi Santoso',
                'penjualan_kode' => 'PJ001',
                'penjualan_tanggal' => '2024-02-20 10:30:00',
            ],
            [
                'penjualan_id' => 2,
                'user_id' => 3,
                'pembeli' => 'Siti Rahayu',
                'penjualan_kode' => 'PJ002',
                'penjualan_tanggal' => '2024-02-20 14:15:00',
            ],
            [
                'penjualan_id' => 3,
                'user_id' => 3,
                'pembeli' => 'Ahmad Hidayat',
                'penjualan_kode' => 'PJ003',
                'penjualan_tanggal' => '2024-02-21 09:45:00',
            ],
            [
                'penjualan_id' => 4,
                'user_id' => 3,
                'pembeli' => 'Dewi Pratiwi',
                'penjualan_kode' => 'PJ004',
                'penjualan_tanggal' => '2024-02-21 13:20:00',
            ],
            [
                'penjualan_id' => 5,
                'user_id' => 3,
                'pembeli' => 'Rudi Hermawan',
                'penjualan_kode' => 'PJ005',
                'penjualan_tanggal' => '2024-02-22 11:30:00',
            ],
            [
                'penjualan_id' => 6,
                'user_id' => 3,
                'pembeli' => 'Nina Susanti',
                'penjualan_kode' => 'PJ006',
                'penjualan_tanggal' => '2024-02-22 15:45:00',
            ],
            [
                'penjualan_id' => 7,
                'user_id' => 3,
                'pembeli' => 'Andi Wijaya',
                'penjualan_kode' => 'PJ007',
                'penjualan_tanggal' => '2024-02-23 10:15:00',
            ],
            [
                'penjualan_id' => 8,
                'user_id' => 3,
                'pembeli' => 'Maya Putri',
                'penjualan_kode' => 'PJ008',
                'penjualan_tanggal' => '2024-02-23 14:30:00',
            ],
            [
                'penjualan_id' => 9,
                'user_id' => 3,
                'pembeli' => 'Doni Kusuma',
                'penjualan_kode' => 'PJ009',
                'penjualan_tanggal' => '2024-02-24 09:20:00',
            ],
            [
                'penjualan_id' => 10,
                'user_id' => 3,
                'pembeli' => 'Rina Fitriani',
                'penjualan_kode' => 'PJ010',
                'penjualan_tanggal' => '2024-02-24 13:45:00',
            ],
        ];

        DB::table('t_penjualan')->insert($data);
    }
}
