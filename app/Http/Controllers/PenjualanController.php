<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangModel;
use App\Models\PenjualanModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\DetailPenjualanModel;
use Illuminate\Support\Facades\Storage;
use App\Models\StokModel;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list'  => ['Home', 'Penjualan'],
        ];

        $page = (object) [
            'title' => 'Daftar barang yang tersedia dalam sistem'
        ];

        $activeMenu = 'penjualan';

        $barang = BarangModel::all(); // ambil data untuk dropdown filter

        return view('penjualan.index', compact('breadcrumb', 'page', 'barang', 'activeMenu'));
    }

    public function list(Request $request)
    {
        $query = PenjualanModel::with('user');

        if ($request->filter_pembeli) {
            $query->where('pembeli', 'like', '%' . $request->filter_pembeli . '%');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('user.nama', function ($row) {
                return $row->user->nama ?? '-';
            })
            ->addColumn('aksi', function ($penjualan) {
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show_ajax(string $id)
    {
        $penjualan = PenjualanModel::with(['detail_penjualan.barang', 'user'])->find($id);

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ], 404);
        }

        return view('penjualan.show_ajax', compact('penjualan'));
    }

    public function confirm_ajax(String $id)
    {
        $penjualan = PenjualanModel::find($id);

        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $penjualan = PenjualanModel::find($id);

            if ($penjualan) {
                try {
                    $penjualan->delete();
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data penjualan berhasil dihapus'
                    ]);
                } catch (\Throwable $th) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data gagal dihapus (terdapat relasi dengan tabel lain)'
                    ]);
                }
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function create_ajax()
    {
        $barang = BarangModel::all(); // untuk select barang
        return view('penjualan.create_ajax', compact('barang'));
    }

    public function store_ajax(Request $request)
    {
        $request->validate([
            'pembeli' => 'required|string|max:100',
            'penjualan_tanggal' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:m_barang,barang_id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|integer|min:1',
        ]);

        $tanggalLengkap = Carbon::parse($request->penjualan_tanggal)
            ->setTimeFromTimeString(now()->format('H:i:s'));

        DB::beginTransaction();
        try {
            $penjualan = PenjualanModel::create([
                'pembeli' => $request->pembeli,
                'penjualan_kode' => 'PJ' . time(),
                'penjualan_tanggal' => $tanggalLengkap,
                'created_at' => now(),
                'user_id' => auth()->id(),
            ]);

            foreach ($request->barang_id as $i => $barang_id) {
                $barang = BarangModel::findOrFail($barang_id);

                // Ambil total stok dari tabel stok
                $stokTersedia = StokModel::where('barang_id', $barang_id)->sum('stok_jumlah');

                if ($stokTersedia < $request->jumlah[$i]) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => "Stok barang '{$barang->barang_nama}' tidak mencukupi! (Tersedia: $stokTersedia, Diminta: {$request->jumlah[$i]})"
                    ], 422);
                }

                // Cari entri stok terakhir untuk barang ini
                $latestStok = StokModel::where('barang_id', $barang_id)
                    ->latest('stok_tanggal')  // Urutkan berdasarkan tanggal terbaru
                    ->first();

                if ($latestStok && $latestStok->stok_jumlah > 0) {
                    // Jika jumlah pengurangan lebih kecil atau sama dengan stok terakhir
                    if ($request->jumlah[$i] <= $latestStok->stok_jumlah) {
                        // Update stok terakhir
                        $latestStok->update([
                            'stok_jumlah' => $latestStok->stok_jumlah - $request->jumlah[$i],
                            'stok_tanggal' => now()
                        ]);
                    } else {
                        // Kurangi nilai stok terakhir menjadi nol
                        $latestStok->update([
                            'stok_jumlah' => 0,
                            'stok_tanggal' => now()
                        ]);

                        // Hitung sisa pengurangan
                        $sisaPengurangan = $request->jumlah[$i] - $latestStok->stok_jumlah;

                        // Cari dan update stok lain jika masih ada sisa pengurangan
                        $this->reduceRemainingStock($barang_id, $sisaPengurangan);
                    }
                } else {
                    // Jika tidak ada stok positif terakhir, buat catatan pengurangan
                    StokModel::create([
                        'barang_id' => $barang_id,
                        'stok_jumlah' => -$request->jumlah[$i],
                        'stok_tanggal' => now(),
                        'user_id' => auth()->id()
                    ]);
                }

                // Simpan detail penjualan
                DetailPenjualanModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $barang_id,
                    'harga' => $barang->harga_jual, // ambil dari model BarangModel
                    'jumlah' => $request->jumlah[$i],
                    'created_at'  => now(),
                ]);                
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Penjualan berhasil disimpan.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan penjualan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Tambahkan method baru di controller untuk mengurangi stok secara bertahap
    private function reduceRemainingStock($barang_id, $amount)
    {
        // Dapatkan stok positif yang tersisa, urutkan dari yang terbaru
        $remainingStocks = StokModel::where('barang_id', $barang_id)
            ->where('stok_jumlah', '>', 0)
            ->orderBy('stok_tanggal', 'desc')
            ->get();

        foreach ($remainingStocks as $stock) {
            if ($amount <= 0) break;

            if ($stock->stok_jumlah <= $amount) {
                // Kurangi seluruh stok ini
                $pengurangan = $stock->stok_jumlah;
                $stock->update([
                    'stok_jumlah' => 0,
                    'stok_tanggal' => now()
                ]);
                $amount -= $pengurangan;
            } else {
                // Kurangi sebagian
                $stock->update([
                    'stok_jumlah' => $stock->stok_jumlah - $amount,
                    'stok_tanggal' => now()
                ]);
                $amount = 0;
            }
        }

        // Jika masih ada sisa pengurangan, buat catatan negatif
        if ($amount > 0) {
            StokModel::create([
                'barang_id' => $barang_id,
                'stok_jumlah' => -$amount,
                'stok_tanggal' => now(),
                'user_id' => auth()->id()
            ]);
        }
    }

    public function import()
    {
        return view('penjualan.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_penjualan' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            try {
                $file = $request->file('file_penjualan');
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, false, true, true);

                $insert = [];
                if (count($data) > 1) {
                    foreach ($data as $baris => $value) {
                        if ($baris > 1) { // Skip header
                            $insert[] = [
                                'barang_id' => $value['A'],
                                'user_id' => $value['B'],
                                'penjualan_tanggal' => $value['C'],
                                'penjualan_jumlah'  => $value['D'],
                                'created_at'  => now(),
                            ];
                        }
                    }

                    if (count($insert) > 0) {
                        PenjualanModel::insertOrIgnore($insert);
                        return response()->json([
                            'status' => true,
                            'message' => 'Data berhasil diimport'
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Tidak ada data yang diimport'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'File tidak mengandung data'
                    ]);
                }
            } catch (Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat memproses file',
                    'error' => $e->getMessage()
                ]);
            }
        }

        return redirect('/');
    }

    public function export_excel()
    {
        $penjualan = PenjualanModel::with(['detail_penjualan.barang', 'user'])
            ->orderBy('penjualan_tanggal', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Penjualan');
        $sheet->setCellValue('C1', 'Tanggal Penjualan');
        $sheet->setCellValue('D1', 'Nama Barang');
        $sheet->setCellValue('E1', 'Jumlah');
        $sheet->setCellValue('F1', 'Harga');
        $sheet->setCellValue('G1', 'Diproses Oleh');

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($penjualan as $p) {
            foreach ($p->detail_penjualan as $detail) {
                $sheet->setCellValue('A' . $baris, $no++);
                $sheet->setCellValue('B' . $baris, $p->penjualan_kode);
                $sheet->setCellValue('C' . $baris, $p->penjualan_tanggal);
                $sheet->setCellValue('D' . $baris, $detail->barang->barang_nama ?? '-');
                $sheet->setCellValue('E' . $baris, $detail->jumlah);
                $sheet->setCellValue('F' . $baris, $detail->harga);
                $sheet->setCellValue('G' . $baris, $p->user->nama ?? '-');
                $baris++;
            }
        }

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Penjualan');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Penjualan ' . date('Y-m-d H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $penjualan = PenjualanModel::with(['detail_penjualan.barang', 'user'])
            ->orderBy('penjualan_tanggal', 'desc')
            ->get();

        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data Penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
