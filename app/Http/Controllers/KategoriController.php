<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::latest()->get();
        return view('kategori.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $gambarPath = $request->file('gambar') ? $request->file('gambar')->store('kategori') : null;

        Kategori::create([
            'nama' => $request->nama,
            'gambar' => $gambarPath
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('gambar')) {
            if ($kategori->gambar) {
                Storage::delete($kategori->gambar);
            }
            $kategori->gambar = $request->file('gambar')->store('kategori');
        }

        $kategori->nama = $request->nama;
        $kategori->save();

        return redirect()->back()->with('success', 'Kategori berhasil diubah');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->gambar) {
            Storage::delete($kategori->gambar);
        }

        $kategori->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus');
    }
}
