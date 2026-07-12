<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::with('kategori')->get();
        $kategori = Kategori::all();
        return view('barang.index', compact('barang', 'kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:0',
            'batas_stok_minimum' => 'required|integer|min:0',
            'satuan' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'stok_awal' => 'nullable|integer|min:0',
            'harga_beli_awal' => [
                (int) $request->stok_awal > 0 ? 'required' : 'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        $gambar = $request->file('gambar') ? $request->file('gambar')->store('barang', 'public') : null;
        $stokAwal = (int) $request->stok_awal;

        DB::transaction(function () use ($request, $gambar, $stokAwal) {
            $barang = Barang::create([
                'nama' => $request->nama,
                'kode' => $request->kode,
                'kategori_id' => $request->kategori_id,
                'harga' => $request->harga,
                'stok' => 0,
                'batas_stok_minimum' => $request->batas_stok_minimum,
                'gambar' => $gambar,
                'satuan' => $request->satuan
            ]);

            if ($stokAwal > 0) {
                BarangMasuk::create([
                    'barang_id' => $barang->id,
                    'jumlah' => $stokAwal,
                    'remaining_jumlah' => $stokAwal,
                    'harga_beli' => $request->harga_beli_awal,
                    'tanggal_masuk' => now(),
                ]);

                $barang->stok = $stokAwal;
                $barang->save();
            }
        });

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:0',
            'batas_stok_minimum' => 'required|integer|min:0',
            'satuan' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            if ($barang->gambar) {
                Storage::disk('public')->delete($barang->gambar);
            }
            $barang->gambar = $request->file('gambar')->store('barang', 'public');
        }

        $barang->update([
            'nama' => $request->nama,
            'kode' => $request->kode,
            'kategori_id' => $request->kategori_id,
            'harga' => $request->harga,
            'batas_stok_minimum' => $request->batas_stok_minimum,
            'gambar' => $barang->gambar,
            'satuan' => $request->satuan
        ]);

        return redirect()->back()->with('success', 'Barang berhasil diupdate');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        if ($barang->gambar) {
            Storage::disk('public')->delete($barang->gambar);
        }
        $barang->delete();
        return redirect()->back()->with('success', 'Barang berhasil dihapus');
    }
}
