<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                    'harga_modal' => $barang->harga_modal,
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

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'catatan' => 'nullable|string',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'required|exists:barang,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $orderId = 'ORD-' . time();
            $totalHarga = 0;

            $customer = User::findOrFail($request->user_id);
            $pesanan = Pesanan::create([
                'user_id' => $customer->id,
                'order_id' => $orderId,
                'nama' => trim($customer->nama_depan . ' ' . $customer->nama_belakang),
                'no_telepon' => $customer->no_telepon,
                'alamat' => $customer->alamat,
                'total_harga' => 0,
                'status' => 'proses',
                'tanggal' => now(),
                'catatan' => $request->catatan ?? '-',
            ]);

            foreach ($request->barang_id as $index => $barangId) {
                $jumlah = $request->jumlah[$index];
                $barang = Barang::findOrFail($barangId);

                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'barang_id' => $barang->id,
                    'kode_barang' => $barang->kode,
                    'nama_barang' => $barang->nama,
                    'jumlah' => $jumlah,
                    'harga' => $barang->harga,
                    'harga_modal' => $barang->harga_modal,
                ]);

                $barang->stok -= $jumlah;
                $barang->save();

                $totalHarga += $barang->harga * $jumlah;
            }

            $pesanan->update(['total_harga' => $totalHarga]);
        });

        return redirect()->route('riwayat-pesanan.index')->with('success', 'Pesanan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);

        $request->validate([
            'status' => 'required|in:batal,selesai',
            'metode_pembayaran' => 'nullable|in:cash,transfer',
            'bukti_pembayaran' => 'nullable|image|max:2048',
        ]);

        $data = [
            'status' => $request->status,
        ];

        if ($request->status === 'selesai') {
            $request->validate([
                'metode_pembayaran' => 'required|in:cash,transfer',
                'bukti_pembayaran' => $request->metode_pembayaran === 'transfer'
                    ? ($pesanan->bukti_pembayaran ? 'nullable|image|max:2048' : 'required|image|max:2048')
                    : 'nullable|image|max:2048',
            ]);

            $data['metode_pembayaran'] = $request->metode_pembayaran;

            if ($request->metode_pembayaran === 'transfer' && $request->hasFile('bukti_pembayaran')) {
                if ($pesanan->bukti_pembayaran) {
                    Storage::disk('public')->delete($pesanan->bukti_pembayaran);
                }

                $data['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            }

            if ($request->metode_pembayaran === 'cash') {
                if ($pesanan->bukti_pembayaran) {
                    Storage::disk('public')->delete($pesanan->bukti_pembayaran);
                }

                $data['bukti_pembayaran'] = null;
            }
        } else {
            $data['metode_pembayaran'] = null;

            if ($pesanan->bukti_pembayaran) {
                Storage::disk('public')->delete($pesanan->bukti_pembayaran);
            }

            $data['bukti_pembayaran'] = null;
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
