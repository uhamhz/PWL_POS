<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['supplier_id' => 1, 'supplier_kode' => 'ABC', 'supplier_nama' => 'PT. ABC', 'supplier_alamat' => 'Jl. Raya ABC, No. 1'],
            ['supplier_id' => 2, 'supplier_kode' => 'XYZ', 'supplier_nama' => 'PT. XYZ', 'supplier_alamat' => 'Jl. Raya XYZ, No. 2'],
            ['supplier_id' => 3, 'supplier_kode' => 'LMN', 'supplier_nama' => 'PT. LMN', 'supplier_alamat' => 'Jl. Raya LMN, No. 3'], 
        ];
        DB::table('m_supplier')->insert($data);
    }
}
