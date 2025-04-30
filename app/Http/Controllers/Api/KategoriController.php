<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    // Get all categories
    public function index()
    {
        return response()->json(KategoriModel::all(), 200);
    }

    // Create a new category
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_kode' => 'required|string|min:2|unique:m_kategori,kategori_kode',
            'kategori_nama' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $kategori = KategoriModel::create([
            'kategori_kode' => $request->kategori_kode,
            'kategori_nama' => $request->kategori_nama,
        ]);

        return response()->json($kategori, 201);
    }

    // Get a specific category
    public function show(KategoriModel $category)
    {
        return response()->json($category, 200);
    }

    // Update a category
    public function update(Request $request, KategoriModel $category)
    {
        $validator = Validator::make($request->all(), [
            'kategori_kode' => 'required|string|min:2|unique:m_kategori,kategori_kode,' . $category->kategori_id . ',kategori_id',
            'kategori_nama' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category->update([
            'kategori_kode' => $request->kategori_kode,
            'kategori_nama' => $request->kategori_nama,
        ]);

        return response()->json($category, 200);
    }

    // Delete a category
    public function destroy(KategoriModel $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ], 200);
    }
}