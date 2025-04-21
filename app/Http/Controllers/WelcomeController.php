<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    /**
     * Menampilkan halaman dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Data breadcrumb untuk navigasi
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Dashboard']
        ];

        // Menu aktif pada sidebar
        $activeMenu = 'dashboard';

        // Statistik jumlah total barang
        $totalBarang = \App\Models\BarangModel::count();

        // Statistik jumlah total stok
        $totalStok = \App\Models\StokModel::sum('stok_jumlah');

        // Statistik jumlah total penjualan
        $totalPenjualan = \App\Models\PenjualanModel::count();

        // Data untuk grafik penjualan bulanan
        $penjualanBulanan = \App\Models\PenjualanModel::selectRaw('MONTH(penjualan_tanggal) as bulan, COUNT(*) as jumlah')
            ->groupByRaw('MONTH(penjualan_tanggal)')
            ->pluck('jumlah', 'bulan')
            ->toArray();

        $bulan = range(1, 12);
        $dataChart = [];
        foreach ($bulan as $b) {
            $dataChart[] = $penjualanBulanan[$b] ?? 0;
        }

        // Menghitung total pendapatan dari detail penjualan
        $totalPendapatan = DB::table('t_penjualan_detail')
            ->join('t_penjualan', 't_penjualan_detail.penjualan_id', '=', 't_penjualan.penjualan_id')
            ->sum(DB::raw('t_penjualan_detail.harga * t_penjualan_detail.jumlah'));

        // Mengambil daftar barang dengan stok minimal (<= 10)
        $stokMinimal = \App\Models\BarangModel::select(
            'm_barang.*',
            DB::raw('(SELECT COALESCE(SUM(stok_jumlah), 0) FROM t_stok WHERE barang_id = m_barang.barang_id) as stok_tersedia')
        )
            ->havingRaw('stok_tersedia <= 10')
            ->orderBy('stok_tersedia', 'asc')
            ->limit(5)
            ->get();

        // Mengambil daftar barang terlaris berdasarkan jumlah terjual
        $penjualanTerlaris = \App\Models\DetailPenjualanModel::select(
            'barang_id',
            DB::raw('SUM(jumlah) as total_jumlah')
        )
            ->groupBy('barang_id')
            ->orderByDesc('total_jumlah')
            ->with('barang') // Memuat relasi dengan model Barang
            ->limit(6)
            ->get();

        // Mengambil daftar penjualan terbaru
        $penjualanTerbaru = \App\Models\PenjualanModel::with('user') // Memuat relasi dengan model User
            ->latest()
            ->limit(5)
            ->get();

        // Mengirim data ke view
        return view('welcome', compact(
            'breadcrumb',
            'activeMenu',
            'totalBarang',
            'totalStok',
            'totalPenjualan',
            'dataChart',
            'penjualanTerbaru',
            'stokMinimal',
            'penjualanTerlaris',
            'totalPendapatan'
        ));
    }
}
