<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    public function index()
    {
        $barang = Barang::take(6)->get();
        $kategori = Kategori::all();
        return view('pesanan.index', compact('kategori', 'barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'catatan' => 'nullable|string',
            'items' => 'required|array',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        $pesanan = DB::transaction(function () use ($request) {
            $orderId = 'ORD-' . time();
            $totalHarga = 0;

            $user = Auth::user();
            $pesanan = Pesanan::create([
                'user_id' => $user->id,
                'order_id' => $orderId,
                'nama' => $user->nama_depan . ' ' . $user->nama_belakang,
                'no_telepon' => $user->no_telepon,
                'alamat' => $user->alamat,
                'total_harga' => 0,
                'status' => 'proses',
                'tanggal' => now(),
                'catatan' => $request->catatan ?? '-',
            ]);

            foreach ($request->items as $item) {
                $barang = Barang::findOrFail($item['barang_id']);
                $subtotal = $barang->harga * $item['jumlah'];

                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'barang_id' => $item['barang_id'],
                    'kode_barang' => $barang->kode,
                    'nama_barang' => $barang->nama,
                    'jumlah' => $item['jumlah'],
                    'harga' => $barang->harga,
                ]);

                $barang->stok -= $item['jumlah'];
                $barang->save();

                $totalHarga += $subtotal;
            }

            $pesanan->update(['total_harga' => $totalHarga]);

            return $pesanan;
        });

        return response()->json([
            'success' => true,
            'pesanan_id' => $pesanan->id
        ]);
    }

    public function update(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);

        $request->validate([
            'status' => 'required|in:batal,selesai',
            'bukti_pembayaran' => 'nullable|image|max:2048', // ⬅️ Validasi upload
        ]);

        $data = [
            'status' => $request->status,
        ];

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            $data['bukti_pembayaran'] = $path;
        }

        $pesanan->update($data);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui');
    }


    public function destroy($id)
    {
        $pesanan = Pesanan::with('detailPesanan')->findOrFail($id);

        foreach ($pesanan->detailPesanan as $detail) {
            $barang = Barang::findOrFail($detail->barang_id);
            $barang->stok += $detail->jumlah;
            $barang->save();
        }
        $pesanan->delete();

        return redirect()->back()->with('success', 'Pesanan berhasil dihapus');
    }

    public function cari(Request $request)
    {
        $query = $request->q;

        $barang = Barang::with('kategori')->when($query, function ($q) use ($query) {
            $q->where('nama', 'like', '%' . $query . '%')
                ->orWhere('kode', 'like', '%' . $query . '%');
        })
            ->take(6)
            ->get();

        return response()->json($barang);
    }

    public function struk($id)
    {
        $pesanan = Pesanan::with('detailPesanan.barang')->findOrFail($id);

        return view('pesanan.struk', compact('pesanan'));
    }
}
