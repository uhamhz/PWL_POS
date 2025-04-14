<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;


class LevelController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Level',
            'list'  => ['Home', 'Level']
        ];

        $page = (object) [
            'title' => 'Daftar level yang terdaftar dalam sistem'
        ];

        $activeMenu = 'level'; // set menu yang sedang aktif

        $level = LevelModel::all(); // ambil data level untuk filter level

        return view('level.index', [
            'breadcrumb' => $breadcrumb,
            'page'       => $page,
            'level'      => $level,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');

        if ($request->level_id) {
            $levels = $levels->where('level_id', $request->level_id);
        }

        return DataTables::of($levels)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($level) { // menambahkan kolom aksi
                $btn = '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Level',
            'list'  => ['Home', 'Level', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah level baru'
        ];

        $activeMenu = 'level'; // set menu yang sedang aktif

        return view('level.create', [
            'breadcrumb' => $breadcrumb,
            'page'       => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'level_kode' => 'required|string|min:2|unique:m_level,level_kode', // level_kode harus unik
            'level_nama' => 'required|string|max:100' // level_nama wajib diisi dan maksimal 100 karakter
        ]);

        LevelModel::create([
            'level_kode' => $request->level_kode,
            'level_nama' => $request->level_nama
        ]);

        return redirect('/level')->with('success', 'Data level berhasil disimpan');
    }

    public function edit(string $id)
    {
        $level = LevelModel::find($id);

        if (!$level) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Edit Level',
            'list' => ['Home', 'Level', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Level'
        ];

        $activeMenu = 'level'; // Set menu yang sedang aktif

        return view('level.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'level' => $level,
            'activeMenu' => $activeMenu
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            // level_kode harus diisi, unik di tabel m_level kecuali untuk level yang sedang diedit
            'level_kode' => 'required|string|min:2|unique:m_level,level_kode,' . $id . ',level_id',
            'level_nama' => 'required|string|max:100' // level_nama harus diisi dan maksimal 100 karakter
        ]);

        $level = LevelModel::find($id);

        if (!$level) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        $level->update([
            'level_kode' => $request->level_kode,
            'level_nama' => $request->level_nama
        ]);

        return redirect('/level')->with('success', 'Data level berhasil diubah');
    }

    public function destroy(string $id)
    {
        $check = LevelModel::find($id);

        if (!$check) {
            // Mengecek apakah data level dengan ID yang dimaksud ada atau tidak
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        try {
            LevelModel::destroy($id); // Hapus data level

            return redirect('/level')->with('success', 'Data level berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
            return redirect('/level')->with('error', 'Data level gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        return view('level.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required|string|min:3|max:10|unique:m_level,level_kode',
                'level_nama' => 'required|string|min:3|max:50',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ], 422);
            }

            // Simpan data ke database
            LevelModel::create([
                'level_kode' => $request->level_kode,
                'level_nama' => $request->level_nama,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Data level berhasil disimpan'
            ], 201);
        }

        return redirect('/');
    }

    public function show_ajax(string $id)
    {
        $level = LevelModel::find($id); // Hapus with('level')

        if (!$level) {
            return response()->json([
                'status' => false,
                'message' => 'Data level tidak ditemukan'
            ], 404);
        }

        return view('level.show_ajax', compact('level'));
    }

    public function edit_ajax(string $id)
    {
        // Cari level berdasarkan ID
        $level = LevelModel::find($id);

        // Jika level tidak ditemukan, kembalikan tampilan error
        if (!$level) {
            return response()->json([
                'status' => false,
                'message' => 'Level tidak ditemukan'
            ]);
        }

        // Tampilkan view edit dengan data level
        return view('level.edit_ajax', compact('level'));
    }

    public function update_ajax(Request $request, $id)
    {
        // Pastikan request berasal dari AJAX atau JSON
        if (!$request->ajax() && !$request->wantsJson()) {
            return response()->json([
                'status' => false,
                'message' => 'Permintaan tidak valid.'
            ], 400);
        }

        // Aturan validasi
        $rules = [
            'level_kode' => 'required|max:10|unique:m_level,level_kode,' . $id . ',level_id',
            'level_nama' => 'required|max:100'
        ];

        $validator = Validator::make($request->all(), $rules);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ], 422);
        }

        try {
            $level = LevelModel::find($id);

            // Jika level ditemukan
            if ($level) {
                $level->update($request->only(['level_kode', 'level_nama']));

                return response()->json([
                    'status' => true,
                    'message' => 'Data level berhasil diperbarui'
                ]);
            }

            // Jika level tidak ditemukan
            return response()->json([
                'status' => false,
                'message' => 'Data level tidak ditemukan'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function confirm_ajax(String $id)
    {
        $level = LevelModel::find($id);

        return view('level.confirm_ajax', ['level' => $level]);
    }

    public function delete_ajax(Request $request, $id)
    {
        // Cek apakah request berasal dari AJAX atau ingin JSON response
        if ($request->ajax() || $request->wantsJson()) {
            $level = LevelModel::find($id);

            if ($level) {
                try {
                    $level->delete();
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data level berhasil dihapus'
                    ]);
                } catch (\Throwable $th) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Data level gagal dihapus (terdapat relasi dengan tabel lain)'
                    ]);
                }
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data level tidak ditemukan'
                ]);
            }
        }

        return redirect('/level');
    }

    public function import()
    {
        return view('level.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_level' => ['required', 'mimes:xlsx', 'max:1024']
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
                $file = $request->file('file_level');
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
                                'level_kode' => $value['A'],
                                'level_nama' => $value['B'],
                                'created_at'  => now(),
                            ];
                        }
                    }

                    if (count($insert) > 0) {
                        LevelModel::insertOrIgnore($insert);
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
        // ambil data level yang akan di export
        $level = LevelModel::select('level_kode', 'level_nama')->get();

        // load library excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();    // ambil sheet yang aktif

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Level Kode');
        $sheet->setCellValue('C1', 'Level Nama');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);    // bold header

        $no = 1;        // nomor data dimulai dari 1
        $baris = 2;     // baris data dimulai dari baris ke 2
        foreach ($level as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->level_kode);
            $sheet->setCellValue('C' . $baris, $value->level_nama);
            $baris++;
            $no++;
        }

        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
        }

        $sheet->setTitle('Data Level'); // set title sheet

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Level' . date('Y-m-d H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $level = LevelModel::select('level_kode', 'level_nama')->get();

        // use Barryvdh\DomPDF\Facade\Pdf;
        $pdf = Pdf::loadView('level.export_pdf', ['level' => $level]);
        $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data Level' . date('Y-m-d H:i:s') . '.pdf');
    }
}
