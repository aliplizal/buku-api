<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $data = Buku::all();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $email = $request->header('Authorization'); // <- ambil dari header
        // if($email){
            $request->validate([
                'judul' => 'required|string|max:255',
                'kategori' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'gambar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $path = $request->file('gambar')->store('gambar-buku', 'public');

            Buku::create([
                'judul' => $request->judul,
                'kategori' => $request->kategori,
                'status' => $request->status,
                'gambar' => $path,
                'email' => $email,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil ditambahkan.'
            ]);
        // }
        // return response()->json([
        //     'message' => 'Anda Belum Login.'
        // ], 401);
    }

    public function update(Request $request, $id)
    {
        $email = $request->header('X-User-Email'); // Ganti jika pakai header kustom

        // Validasi data input
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // Cari data berdasarkan id dan email
        $buku = Buku::where('id', $id)
            ->where('email', $email)
            ->first();

        if (!$buku) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        // Update data
        $buku->judul = $request->judul;
        $buku->kategori = $request->kategori;
        $buku->status = $request->status;

        // Jika ada gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($buku->gambar && Storage::disk('public')->exists($buku->gambar)) {
                Storage::disk('public')->delete($buku->gambar);
            }

            // Simpan gambar baru
            $path = $request->file('gambar')->store('gambar-buku', 'public');
            $buku->gambar = $path;
        }

        // Simpan perubahan
        $buku->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
    }


    public function destroy(Request $request, $id)
    {
        $email = $request->header('Authorization'); // Ambil email dari header

        // Cari data berdasarkan id dan email
        $buku = Buku::where('id', $id)
            ->where('email', $email)
            ->first();

        if (!$buku) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        // Hapus file gambar jika ada
        if ($buku->gambar && Storage::disk('public')->exists($buku->gambar)) {
            Storage::disk('public')->delete($buku->gambar);
        }

        // Hapus data dari database
        $buku->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus.'
        ]);
    }
}
