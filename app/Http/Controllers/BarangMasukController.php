<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        DB::transaction(function () use ($request) {
            BarangMasuk::create([
                'barang_id' => $request->barang_id,
                'jumlah' => $request->jumlah,
                'remaining_jumlah' => $request->jumlah,
                'harga_beli' => $request->harga_beli,
                'tanggal_masuk' => $request->tanggal_masuk,
            ]);

            $barang = Barang::findOrFail($request->barang_id);
            $barang->stok += $request->jumlah;
            $barang->save();
        });

        return redirect()->back()->with('success', 'Barang masuk berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|numeric|min:1',
            'harga_beli' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $id) {
            $barangMasuk = BarangMasuk::lockForUpdate()->findOrFail($id);
            $jumlahTerpakai = $barangMasuk->jumlah - $barangMasuk->remaining_jumlah;

            if ($request->jumlah < $jumlahTerpakai) {
                abort(422, 'Jumlah barang masuk tidak boleh lebih kecil dari jumlah yang sudah terjual.');
            }

            if ((int) $request->barang_id !== (int) $barangMasuk->barang_id && $jumlahTerpakai > 0) {
                abort(422, 'Barang tidak bisa diganti karena batch ini sudah dipakai transaksi FIFO.');
            }

            $barangLama = Barang::findOrFail($barangMasuk->barang_id);
            $barangLama->stok -= $barangMasuk->remaining_jumlah;
            $barangLama->save();

            $remainingBaru = $request->jumlah - $jumlahTerpakai;
            $barang = Barang::findOrFail($request->barang_id);
            $barang->stok += $remainingBaru;
            $barang->save();

            $barangMasuk->update([
                'barang_id'   => $request->barang_id,
                'jumlah'      => $request->jumlah,
                'remaining_jumlah' => $remainingBaru,
                'harga_beli'  => $request->harga_beli,
                'tanggal_masuk' => $request->tanggal_masuk,
            ]);
        });

        return redirect()->back()->with('success', 'Barang masuk berhasil diupdate');
    }

    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        if ($barangMasuk->remaining_jumlah < $barangMasuk->jumlah) {
            return redirect()->back()->with('error', 'Barang masuk tidak bisa dihapus karena sebagian stoknya sudah terjual.');
        }

        $barang = Barang::findOrFail($barangMasuk->barang_id);
        $barang->stok -= $barangMasuk->remaining_jumlah;
        $barang->save();

        $barangMasuk->delete();

        return redirect()->back()->with('success', 'Barang masuk berhasil dihapus');
    }
}
