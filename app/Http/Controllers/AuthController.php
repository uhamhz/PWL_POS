<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\UserModel;



class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) { // jika sudah login, maka redirect ke halaman home
            return redirect('/');
        }
        return view('auth.login');
    }
    public function postlogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $credentials = $request->only('username', 'password');
            if (Auth::attempt($credentials)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Login Berhasil',
                    'redirect' => url('/')
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Login Gagal'
            ]);
        }
        return redirect('login');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->ajax()) {
            return response()->json(['message' => 'Logout berhasil']);
        }

        return redirect('login');
    }
    public function register()
    {
        if (Auth::check()) { // Jika sudah login, langsung ke halaman home
            return redirect('/');
        }
        return view('auth.register'); // Pastikan Anda memiliki file view register.blade.php
    }


    public function postRegister(Request $request)
    {
        // Log untuk debugging
        Log::info('Data Register:', $request->all());

        // Validasi input
        $request->validate([
            'username' => 'required|min:3|max:20|unique:m_user,username',
            'nama' => 'required|min:3|max:50',
            'password' => 'required|min:6|confirmed'
        ]);

        // Simpan ke database
        $user = UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => bcrypt($request->password), 
            'level_id' => 5
        ]);        
        
        // Login user setelah registrasi
        Auth::login($user);

        return response()->json([
            'status' => true,
            'message' => 'Registrasi Berhasil',
            'redirect' => url('/')
        ]);
    }
}
