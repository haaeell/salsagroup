@extends('layouts.dashboard')

@section('title')
    Riwayat Pesanan
@endsection
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5 class="mb-0">Data Riwayat Pesanan</h5>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah</button>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover" id="datatable">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Order Id</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center">No Telp</th>
                        <th class="text-center">Alamat</th>
                        <th class="text-center">Catatan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pesanan as $item)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->order_id }}</td>
                            <td><span
                                    class="badge text-white fw-bold bg-{{ $item->status == 'selesai' ? 'success' : 'warning' }}">{{ $item->status }}</span>
                            </td>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->no_telepon }}</td>
                            <td>{{ $item->alamat }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td>
                                @if ($item->status == 'proses')
                                    <button class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#modalDone{{ $item->id }}"><i class="fa fa-check"></i>
                                        Selesaikan
                                        Pesanan</button>
                                @elseif ($item->status == 'selesai')
                                    <button class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#modalCancel{{ $item->id }}"><i class="fa fa-times"></i>
                                        Batalkan
                                        Pesanan</button>
                                @else
                                    <button class="btn btn-danger" disabled><i class="fa fa-times"></i>
                                        Pesanan Batal </button>
                                @endif
                                <button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalDetail{{ $item->id }}">
                                    <i class="fa fa-info"></i> Detail Barang
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($pesanan as $item)
        <div class="modal fade" id="modalDone{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('pesanan.update', $item->id) }}" method="POST" enctype="multipart/form-data"
                    class="modal-content">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Selesaikan Pesanan</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <h4>Anda yakin ingin membatalkan pesanan ini?</h4>
                            <input type="hidden" name="status" value="selesai">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="modalCancel{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('pesanan.update', $item->id) }}" method="POST" enctype="multipart/form-data"
                    class="modal-content">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Batalkan Pesanan</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <h4>Anda yakin ingin membatalkan pesanan ini?</h4>
                            <p>Stok akan di kembalikan ke semula</p>
                            <input type="hidden" name="status" value="batal">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal Detail Produk -->
        <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Produk - Order ID: {{ $item->order_id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($item->detailPesanan->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Nama Produk</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->detailPesanan as $detail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $detail->kode_barang }}</td>
                                                <td>{{ $detail->nama_barang ?? '-' }}</td>
                                                <td>{{ $detail->qty }}/{{ $detail->satuan ?? 'pcs' }}</td>
                                                <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($detail->qty * $detail->harga, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Tidak ada produk dalam pesanan ini.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script></script>
@endpush
