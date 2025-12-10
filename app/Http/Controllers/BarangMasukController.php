<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    public function index()
    {
        $barangMasuk = BarangMasuk::with('barang')->get();
        $barang = Barang::all();
        return view('barang_masuk.index', compact('barangMasuk', 'barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|numeric|min:1',
            'harga_beli' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ]);

        BarangMasuk::create([
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'harga_beli' => $request->harga_beli,
            'tanggal_masuk' => $request->tanggal_masuk,
        ]);

        // Update stok barang
        $barang = Barang::findOrFail($request->barang_id);
        $barang->stok += $request->jumlah;
        $barang->save();

        return redirect()->back()->with('success', 'Barang masuk berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|numeric|min:1',
            'harga_beli' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ]);

        $barang = Barang::findOrFail($request->barang_id);
        $barang->stok = $barang->stok - $barangMasuk->jumlah + $request->jumlah;

        $barang->save();

        $barangMasuk->update([
            'barang_id'   => $request->barang_id,
            'jumlah'      => $request->jumlah,
            'harga_beli'  => $request->harga_beli,
            'tanggal_masuk' => $request->tanggal_masuk,
        ]);

        return redirect()->back()->with('success', 'Barang masuk berhasil diupdate');
    }

    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        $barang = Barang::findOrFail($barangMasuk->barang_id);
        $barang->stok -= $barangMasuk->jumlah;
        $barang->save();

        $barangMasuk->delete();

        return redirect()->back()->with('success', 'Barang masuk berhasil dihapus');
    }
}
