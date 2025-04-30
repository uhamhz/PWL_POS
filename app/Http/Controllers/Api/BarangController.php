<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangModel;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    // Get all barang
    public function index()
    {
        return response()->json(BarangModel::with('kategori')->get(), 200);
    }

    // Create a new barang
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|integer|exists:m_kategori,kategori_id',
            'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode',
            'barang_nama' => 'required|string|max:100',
            'harga_beli'  => 'required|numeric|min:0',
            'harga_jual'  => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $barang = BarangModel::create($request->all());

        return response()->json($barang, 201);
    }

    // Get a specific barang
    public function show(BarangModel $barang)
    {
        return response()->json($barang->load('kategori'), 200);
    }

    // Update a barang
    public function update(Request $request, BarangModel $barang)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|integer|exists:m_kategori,kategori_id',
            'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode,' . $barang->barang_id . ',barang_id',
            'barang_nama' => 'required|string|max:100',
            'harga_beli'  => 'required|numeric|min:0',
            'harga_jual'  => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $barang->update($request->all());

        return response()->json($barang, 200);
    }

    // Delete a barang
    public function destroy(BarangModel $barang)
    {
        try {
            $barang->delete();

            return response()->json([
                'success' => true,
                'message' => 'Barang deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete barang. It may be related to other data.',
            ], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barang_id' => 'required|exists:m_barang,barang_id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi file gambar
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Handle file upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/barang', $filename, 'public');

            // Update barang record with image path
            $barang = BarangModel::find($request->barang_id);
            $barang->barang_image = $filePath;
            $barang->save();

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => $barang,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'No file uploaded',
        ], 400);
    }
}
