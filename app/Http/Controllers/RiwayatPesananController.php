<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatPesananController extends Controller
{
    public function index()
    {
        $pesanan = Pesanan::with('detailPesanan');

        if (Auth::user()->role == 'user') {
            $pesanan = $pesanan->where('user_id', Auth::user()->id);
        }

        $pesanan = $pesanan->get();

        return view('riwayat-pesanan.index', compact('pesanan'));
    }
}
