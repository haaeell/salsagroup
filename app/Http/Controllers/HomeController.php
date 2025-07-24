<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Auth::user()->role == 'user') {
            $totalPesananSelesai = Pesanan::where('user_id', Auth::user()->id)->where('status', 'selesai')->count();
            $totalPesananProses = Pesanan::where('user_id', Auth::user()->id)->where('status', 'proses')->count();
        }
        return view('home', [
            'totalPembelian'    => Pesanan::where('status', 'selesai')->count(),
            'totalBarang'       => Barang::count(),
            'totalPermintaan'   => Pesanan::where('status', 'proses')->count(),
            'totalUser'         => User::count(),
            'totalBarangMasuk'  => BarangMasuk::count(),
            'stokMenipis'       => Barang::where('stok', '<=', 5)->orderBy('stok')->get(),
            'totalPesananSelesai' => $totalPesananSelesai ?? null,
            'totalPesananProses' => $totalPesananProses ?? null
        ]);
    }
}
