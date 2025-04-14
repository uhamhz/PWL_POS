<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\User;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class UserController extends Controller
{
   // Menampilkan halaman awal user
   public function index()
   {
      $breadcrumb = (object) [
         'title' => 'Daftar User',
         'list'  => ['Home', 'User']
      ];

      $page = (object) [
         'title' => 'Daftar user yang terdaftar dalam sistem'
      ];

      $activeMenu = 'user'; // set menu yang sedang aktif

      $level = LevelModel::all(); // ambil data level untuk filter level

      return view('user.index', [
         'breadcrumb' => $breadcrumb,
         'page'       => $page,
         'level'      => $level,
         'activeMenu' => $activeMenu
      ]);
   }

   public function indexProfil()
   {
      $breadcrumb = (object) [
         'title' => 'Data Pribadi',
         'list'  => ['Home', 'Data Pribadi']
      ];

      $page = (object) [
         'title' => 'Daftar user yang terdaftar dalam sistem'
      ];

      $activeMenu = 'profil'; // set menu yang sedang aktif

      $level = LevelModel::all(); // ambil data level untuk filter level

      return view('profil.index', [
         'breadcrumb' => $breadcrumb,
         'page'       => $page,
         'level'      => $level,
         'activeMenu' => $activeMenu
      ]);
   }

   // Ambil data user dalam bentuk json untuk datatables
   // Ambil data user dalam bentuk JSON untuk DataTables
   public function list(Request $request)
   {
      $users = UserModel::select('user_id', 'username', 'nama', 'level_id')
         ->with('level');

      // Filter data user berdasarkan level_id
      if ($request->level_id) {
         $users->where('level_id', $request->level_id);
      }

      return DataTables::of($users)
         ->addIndexColumn() // Menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
         ->addColumn('aksi', function ($user) { // Menambahkan kolom aksi
            $btn = '<button onclick="modalAction(\'' . url('/user/' . $user->user_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
            $btn .= '<button onclick="modalAction(\'' . url('/user/' . $user->user_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
            $btn .= '<button onclick="modalAction(\'' . url('/user/' . $user->user_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
            return $btn;
         })
         ->rawColumns(['aksi']) // Memberitahu bahwa kolom aksi adalah HTML
         ->make(true);
   }

   // Menampilkan halaman form tambah user
   public function create()
   {
      $breadcrumb = (object) [
         'title' => 'Tambah User',
         'list' => ['Home', 'User', 'Tambah']
      ];

      $page = (object) [
         'title' => 'Tambah user baru'
      ];

      $level = LevelModel::all(); // ambil data level untuk ditampilkan di form
      $activeMenu = 'user'; // set menu yang sedang aktif

      return view('user.create', [
         'breadcrumb' => $breadcrumb,
         'page' => $page,
         'level' => $level,
         'activeMenu' => $activeMenu
      ]);
   }

   // Menyimpan data user baru
   public function store(Request $request)
   {
      $request->validate([
         // username harus diisi, berupa string, minimal 3 karakter, dan bernilai unik di tabel m_user kolom username
         'username' => 'required|string|min:3|unique:m_user,username',
         'nama' => 'required|string|max:100', // nama harus diisi, berupa string, dan maksimal 100 karakter
         'password' => 'required|min:5', // password harus diisi dan minimal 5 karakter
         'level_id' => 'required|integer' // level_id harus diisi dan berupa angka
      ]);

      UserModel::create([
         'username' => $request->username,
         'nama' => $request->nama,
         'password' => bcrypt($request->password), // password dienkripsi sebelum disimpan
         'level_id' => $request->level_id
      ]);

      return redirect('/user')->with('success', 'Data user berhasil disimpan');
   }

   // Menampilkan detail user
   public function show(string $id)
   {
      $user = UserModel::with('level')->find($id);

      $breadcrumb = (object) [
         'title' => 'Detail User',
         'list' => ['Home', 'User', 'Detail']
      ];

      $page = (object) [
         'title' => 'Detail user'
      ];

      $activeMenu = 'user'; // set menu yang sedang aktif

      return view('user.show', [
         'breadcrumb' => $breadcrumb,
         'page' => $page,
         'user' => $user,
         'activeMenu' => $activeMenu
      ]);
   }

   // Menampilkan halaman form edit user
   public function edit(string $id)
   {
      $user = UserModel::find($id);
      $level = LevelModel::all();

      $breadcrumb = (object) [
         'title' => 'Edit User',
         'list' => ['Home', 'User', 'Edit']
      ];

      $page = (object) [
         'title' => 'Edit user'
      ];

      $activeMenu = 'user'; // set menu yang sedang aktif

      return view('user.edit', [
         'breadcrumb' => $breadcrumb,
         'page' => $page,
         'user' => $user,
         'level' => $level,
         'activeMenu' => $activeMenu
      ]);
   }

   // Menyimpan perubahan data user
   public function update(Request $request, string $id)
   {
      $request->validate([
         // username harus diisi, berupa string, minimal 3 karakter,
         // dan bernilai unik di tabel m_user kolom username kecuali untuk user dengan id yang sedang diedit
         'username' => 'required|string|min:3|unique:m_user,username,' . $id . ',user_id',
         'nama' => 'required|string|max:100', // nama harus diisi, berupa string, dan maksimal 100 karakter
         'password' => 'nullable|min:5', // password bisa diisi (minimal 5 karakter) dan bisa tidak diisi
         'level_id' => 'required|integer' // level_id harus diisi dan berupa angka
      ]);

      UserModel::find($id)->update([
         'username' => $request->username,
         'nama' => $request->nama,
         'password' => $request->password ? bcrypt($request->password) : UserModel::find($id)->password,
         'level_id' => $request->level_id
      ]);

      return redirect('/user')->with('success', 'Data user berhasil diubah');
   }

   // Menghapus data user
   public function destroy(string $id)
   {
      $check = UserModel::find($id);

      if (!$check) {
         // Untuk mengecek apakah data user dengan ID yang dimaksud ada atau tidak
         return redirect('/user')->with('error', 'Data user tidak ditemukan');
      }

      try {
         UserModel::destroy($id); // Hapus data user

         return redirect('/user')->with('success', 'Data user berhasil dihapus');
      } catch (\Illuminate\Database\QueryException $e) {
         // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
         return redirect('/user')->with('error', 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
      }
   }

   public function create_ajax()
   {
      $level = LevelModel::select('level_id', 'level_nama')->get();

      return view('user.create_ajax')
         ->with('level', $level);
   }

   public function store_ajax(Request $request)
   {
      // cek apakah request berupa ajax
      if ($request->ajax() || $request->wantsJson()) {
         $rules = [
            'level_id'  => 'required|integer',
            'username'  => 'required|string|min:3|unique:m_user,username',
            'nama'      => 'required|string|max:100',
            'password'  => 'required|min:6'
         ];

         // use Illuminate\Support\Facades\Validator;
         $validator = Validator::make($request->all(), $rules);

         if ($validator->fails()) {
            return response()->json([
               'status'  => false, // response status, false: error/gagal, true: berhasil
               'message' => 'Validasi Gagal',
               'msgField' => $validator->errors(), // pesan error validasi
            ]);
         }

         UserModel::create($request->all());
         return response()->json([
            'status'  => true,
            'message' => 'Data user berhasil disimpan'
         ]);
      }

      redirect('/');
   }

   public function edit_ajax(string $id)
   {
      // Cari user berdasarkan ID
      $user = UserModel::find($id);

      // Jika user tidak ditemukan, kembalikan tampilan error
      if (!$user) {
         return response()->json([
            'status' => false,
            'message' => 'User tidak ditemukan'
         ]);
      }

      // Ambil daftar level untuk dropdown
      $level = LevelModel::select('level_id', 'level_nama')->get();

      // Tampilkan view edit dengan data user dan level
      return view('user.edit_ajax', compact('user', 'level'));
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
         'level_id' => 'required|integer',
         'username' => 'required|max:20|unique:m_user,username,' . $id . ',user_id',
         'nama' => 'required|max:100',
         'password' => 'nullable|min:6|max:20'
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
         $user = UserModel::find($id);

         // Jika user ditemukan
         if ($user) {
            // Jika password kosong, hapus dari request
            $data = $request->except(['password']);
            if ($request->filled('password')) {
               $data['password'] = bcrypt($request->password); // Enkripsi password
            }

            $user->update($data);

            return response()->json([
               'status' => true,
               'message' => 'Data berhasil diupdate'
            ]);
         }

         // Jika user tidak ditemukan
         return response()->json([
            'status' => false,
            'message' => 'Data tidak ditemukan'
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
      $user = UserModel::find($id);

      return view('user.confirm_ajax', ['user' => $user]);
   }

   public function delete_ajax(Request $request, $id)
   {
      // Cek apakah request berasal dari AJAX atau ingin JSON response
      if ($request->ajax() || $request->wantsJson()) {
         $user = UserModel::find($id);

         if ($user) {
            try {
               $user->delete();
               return response()->json([
                  'status'  => true,
                  'message' => 'Data berhasil dihapus'
               ]);
            } catch (\Throwable $th) {
               return response()->json([
                  'status' => false,
                  'message' => 'Data gagal dihapus (terdapat relasi dengan tabel lain)',
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
      $user = UserModel::with('level')->find($id);

      if (!$user) {
         return response()->json([
            'status' => false,
            'message' => 'Data user tidak ditemukan'
         ], 404);
      }

      return view('user.show_ajax', compact('user'));
   }

   public function uploadFoto(Request $request)
   {
      $request->validate([
         'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
      ]);

      $user = auth()->user(); // instance UserModel

      if ($request->hasFile('foto_profil')) {
         $file = $request->file('foto_profil');
         $filename = time() . '_' . $file->getClientOriginalName();

         // simpan file
         $file->move(public_path('uploads/profil'), $filename);

         /** @var \App\Models\User $user **/
         // update ke database
         $user->foto_profil = 'uploads/profil/' . $filename;
         $user->save();
      }

      return back()->with('success', 'Foto berhasil diupload!');
   }

   public function import()
   {
      return view('user.import');
   }

   public function import_ajax(Request $request)
   {
      if ($request->ajax() || $request->wantsJson()) {
         $rules = [
            'file_user' => ['required', 'mimes:xlsx', 'max:1024']
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
            $file = $request->file('file_user');
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
                        'level_id' => $value['A'],
                        'nama' => $value['B'],
                        'username' => $value['C'],
                        'created_at'  => now(),
                     ];
                  }
               }

               if (count($insert) > 0) {
                  UserModel::insertOrIgnore($insert);
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
      // ambil data user yang akan di export
      $user = UserModel::select('level_id', 'nama', 'username')->get();

      // load library excel
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();    // ambil sheet yang aktif

      $sheet->setCellValue('A1', 'No');
      $sheet->setCellValue('B1', 'Level User');
      $sheet->setCellValue('C1', 'Nama');
      $sheet->setCellValue('D1', 'Username');

      $sheet->getStyle('A1:F1')->getFont()->setBold(true);    // bold header

      $no = 1;        // nomor data dimulai dari 1
      $baris = 2;     // baris data dimulai dari baris ke 2
      foreach ($user as $key => $value) {
         $sheet->setCellValue('A' . $baris, $no);
         $sheet->setCellValue('B' . $baris, $value->level->level_nama);
         $sheet->setCellValue('C' . $baris, $value->nama);
         $sheet->setCellValue('D' . $baris, $value->username);
         $baris++;
         $no++;
      }

      foreach (range('A', 'D') as $columnID) {
         $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
      }

      $sheet->setTitle('Data User'); // set title sheet

      $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
      $filename = 'Data User' . date('Y-m-d H:i:s') . '.xlsx';

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
      $user = UserModel::select('level_id', 'nama', 'username')->get();

      // use Barryvdh\DomPDF\Facade\Pdf;
      $pdf = Pdf::loadView('user.export_pdf', ['user' => $user]);
      $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
      $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
      $pdf->render();

      return $pdf->stream('Data User ' . date('Y-m-d H:i:s') . '.pdf');
   }
}
