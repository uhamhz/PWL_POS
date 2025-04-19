<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\BarangModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

use Illuminate\Http\Request;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Stok Barang',
            'list'  => ['Home', 'Stok Barang']
        ];

        $page = (object) [
            'title' => 'Daftar barang yang tersedia dalam sistem'
        ];

        $activeMenu = 'stok';

        $barang = BarangModel::all(); // ambil data untuk dropdown filter

        return view('stok.index', compact('breadcrumb', 'page', 'barang', 'activeMenu'));
    }



    public function list(Request $request)
    {
        $data = StokModel::with(['barang', 'user']); // pastikan relasi 'barang' & 'user' ada

        // filter jika ada input filter_barang
        if ($request->filled('filter_barang')) {
            $data->where('barang_id', $request->filter_barang);
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($stok) {
                $btn = '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function confirm_ajax(String $id)
    {
        $stok = StokModel::find($id);

        return view('stok.confirm_ajax', ['stok' => $stok]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $stok = StokModel::find($id);

            if ($stok) {
                try {
                    $stok->delete();
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data stok berhasil dihapus'
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

    public function show_ajax(string $id)
    {
        $stok = StokModel::with('barang')->find($id);

        if (!$stok) {
            return response()->json([
                'status' => false,
                'message' => 'Data stok tidak ditemukan'
            ], 404);
        }

        return view('stok.show_ajax', compact('stok'));
    }

    public function create_ajax()
    {
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();
        return view('stok.create_ajax')->with('barang', $barang);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_id'    => 'required|exists:m_barang,barang_id',
                'stok_tanggal' => 'required|date',
                'stok_jumlah'  => 'required|integer|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            // Gabungkan tanggal dari input dengan jam sekarang (dari server)
            $tanggalInput = $request->stok_tanggal;
            $waktuSekarang = date('H:i:s');
            $tanggalLengkap = $tanggalInput . ' ' . $waktuSekarang;

            $stok = new StokModel();
            $stok->barang_id    = $request->barang_id;
            $stok->stok_tanggal = $tanggalLengkap; // waktu otomatis dari server
            $stok->stok_jumlah  = $request->stok_jumlah;
            $stok->user_id      = auth()->user()->id ?? 1;
            $stok->save();

            return response()->json([
                'status'  => true,
                'message' => 'Data stok berhasil disimpan'
            ]);
        }

        return redirect('/');
    }


    public function import()
    {
        return view('stok.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
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
                $file = $request->file('file_stok');
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
                                'stok_tanggal' => $value['C'],
                                'stok_jumlah'  => $value['D'],
                                'created_at'  => now(),
                            ];
                        }
                    }

                    if (count($insert) > 0) {
                        StokModel::insertOrIgnore($insert);
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
        $stok = StokModel::with(['barang', 'user'])
            ->orderBy('stok_tanggal', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Tanggal Stok');
        $sheet->setCellValue('D1', 'Jumlah');
        $sheet->setCellValue('E1', 'Input Oleh');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($stok as $s) {
            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValue('B' . $baris, $s->barang->barang_nama ?? '-');
            $sheet->setCellValue('C' . $baris, $s->stok_tanggal);
            $sheet->setCellValue('D' . $baris, $s->stok_jumlah);
            $sheet->setCellValue('E' . $baris, $s->user->nama ?? '-');
            $baris++;
        }

        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Stok');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Stok ' . date('Y-m-d H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $stok = StokModel::select('barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->orderBy('stok_tanggal', 'desc')
            ->with(['barang.kategori', 'user'])
            ->get();

        // use Barryvdh\DomPDF\Facade\Pdf;
        $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data Stok ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
