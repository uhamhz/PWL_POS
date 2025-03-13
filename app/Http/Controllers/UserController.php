<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

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
      } catch (\Exception $e) {
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
            $user->delete();
            return response()->json([
               'status'  => true,
               'message' => 'Data berhasil dihapus'
            ]);
         } else {
            return response()->json([
               'status'  => false,
               'message' => 'Data tidak ditemukan'
            ]);
         }
      }

      return redirect('/');
   }
}
