@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    @if (Auth::user()->role == 'admin')
        {{-- Kartu Statistik --}}
        <div class="row d-flex mb-4">
            <div class="col-md-4 col-lg-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fa fa-shopping-cart fa-2x text-primary mb-2"></i>
                        <h6 class="mb-1">Total Pembelian</h6>
                        <h5>{{ $totalPembelian }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fa fa-box fa-2x text-success mb-2"></i>
                        <h6 class="mb-1">Jumlah Barang</h6>
                        <h5>{{ $totalBarang }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fa fa-truck-loading fa-2x text-warning mb-2"></i>
                        <h6 class="mb-1">Permintaan Barang</h6>
                        <h5>{{ $totalPermintaan }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fa fa-users fa-2x text-info mb-2"></i>
                        <h6 class="mb-1">Jumlah User</h6>
                        <h5>{{ $totalUser }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fa fa-boxes fa-2x text-secondary mb-2"></i>
                        <h6 class="mb-1">Barang Masuk</h6>
                        <h5>{{ $totalBarangMasuk }}</h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Info Stok Menipis --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Info Stok Menipis</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Kode</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stokMenipis as $index => $barang)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $barang->nama }}</td>
                                    <td>{{ $barang->kode }}</td>
                                    <td class="text-danger fw-bold">{{ $barang->stok }}</td>
                                    <td>{{ $barang->satuan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada barang dengan stok menipis.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if (Auth::user()->role == 'user')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Selamat Datang, {{ Auth::user()->username }}!</h5>
                    </div>
                    <div class="card-body">
                        <p>Anda dapat melakukan pemesanan barang melalui menu Daftar Produk</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row d-flex mb-4">
            <div class="col-md-6 col-lg-6 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fa fa-shopping-cart fa-2x text-primary mb-2"></i>
                        <h6 class="mb-1">Total Pesanan Selesai</h6>
                        <h5>{{ $totalPesananSelesai }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fa fa-box fa-2x text-success mb-2"></i>
                        <h6 class="mb-1">Total Pesanan Proses</h6>
                        <h5>{{ $totalPesananProses }}</h5>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
