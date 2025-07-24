@extends('layouts.dashboard')

@section('title', 'Laporan')

@section('content')
    <div class="container-fluid">

        <div class="card mb-4">
            <div class="card-header fw-bold">Form Laporan</div>
            <div class="card-body">
                <form action="{{ route('laporan.index') }}" method="GET" class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label for="jenis" class="form-label">Jenis Laporan</label>
                        <select name="jenis" id="jenis" class="form-control select2" required>
                            <option value="">*Pilih Laporan*</option>
                            <option value="pemesanan" {{ request('jenis') == 'pemesanan' ? 'selected' : '' }}>Pemesanan
                            </option>
                            <option value="pembelian" {{ request('jenis') == 'pembelian' ? 'selected' : '' }}>Pembelian
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="dari" class="form-label">Dari Tanggal</label>
                        <input type="date" name="dari" id="dari" value="{{ request('dari') }}"
                            class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="sampai" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="sampai" id="sampai" value="{{ request('sampai') }}"
                            class="form-control" required>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        @if (!empty($data) && count($data) > 0)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">{{ ucfirst($jenis) }} Barang</h5>
                    <a href="{{ route('laporan.cetak', request()->query()) }}" target="_blank" class="btn btn-success">
                        <i class="fa fa-plus"></i> Cetak Laporan
                    </a>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered align-middle" id="datatable">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>Order Id</th>
                                <th>Tanggal</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Total Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $i => $item)
                                @foreach ($item->detailPesanan as $j => $detail)
                                    <tr class="text-start">
                                        <td>{{ $i++ + 1 }}</td>
                                        <td>{{ $item->order_id }}</td>
                                        <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                        <td>{{ $detail->kode_barang }} - {{ $detail->nama_barang }}</td>
                                        <td>{{ $detail->jumlah }}</td>
                                        <td>Rp.{{ number_format($detail->harga, 0, ',', '.') }}</td>
                                        <td>Rp.{{ number_format($detail->jumlah * $detail->harga, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                        <tfoot>
                            @php
                                $total = 0;
                                foreach ($data as $item) {
                                    foreach ($item->detailPesanan as $detail) {
                                        $total += $detail->jumlah * $detail->harga;
                                    }
                                }
                            @endphp
                            <tr>
                                <th colspan="6" class="text-end">Total:</th>
                                <th>Rp.{{ number_format($total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @elseif(request()->all())
            <div class="alert alert-warning">Data tidak ditemukan untuk filter tersebut.</div>
        @endif

    </div>
@endsection
