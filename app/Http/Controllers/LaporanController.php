<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $data = [];
        $jenis = $request->jenis;

        if ($request->filled(['dari', 'sampai', 'jenis'])) {
            switch ($jenis) {
                case 'pembelian':
                    $data = Pesanan::where('status', 'selesai')->whereBetween('tanggal', [$request->dari, $request->sampai])->get();
                    break;
                case 'pemesanan':
                    $data = Pesanan::where('status', 'proses')->whereBetween('tanggal', [$request->dari, $request->sampai])->get();
                    break;
                case 'barang_masuk':
                    $data = BarangMasuk::whereBetween('tanggal', [$request->dari, $request->sampai])->get();
                    break;
            }
        }

        return view('laporan.index', compact('data', 'jenis'));
    }




    public function cetak(Request $request)
    {
        $jenis = $request->jenis;
        $dari = $request->dari;
        $sampai = $request->sampai;

        if ($jenis === 'pemesanan') {
            $data = Pesanan::where('status', 'proses')->with('detailPesanan')
                ->whereBetween('tanggal', [$dari, $sampai])
                ->get();
        } elseif ($jenis === 'pembelian') {
            $data = Pesanan::where('status', 'selesai')->with('detailPembelian')
                ->whereBetween('tanggal', [$dari, $sampai])
                ->get();
        } else {
            return abort(404);
        }

        $pdf = Pdf::loadView('laporan.cetak', compact('data', 'jenis', 'dari', 'sampai'));
        return $pdf->stream('laporan-' . $jenis . '.pdf');
    }
}
