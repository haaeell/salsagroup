@extends('layouts.dashboard')

@section('title', 'Riwayat Pesanan')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Riwayat Pesanan</h5>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah</button>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover" id="datatable">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Order Id</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>No Telp</th>
                        <th>Alamat</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($pesanan as $item)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->order_id }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $item->status == 'selesai' ? 'success' : 'warning' }} text-white fw-bold">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->no_telepon }}</td>
                            <td>{{ $item->alamat }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td class="d-flex flex-column gap-2">
                                @if ($item->status == 'proses')
                                    <button class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#modalDone{{ $item->id }}">
                                        <i class="fa fa-check"></i> Selesaikan
                                    </button>
                                @elseif ($item->status == 'selesai')
                                    <button class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#modalCancel{{ $item->id }}">
                                        <i class="fa fa-times"></i> Batalkan
                                    </button>
                                @else
                                    <button class="btn btn-danger" disabled>
                                        <i class="fa fa-times"></i> Pesanan Batal
                                    </button>
                                @endif

                                <button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalDetail{{ $item->id }}">
                                    <i class="fa fa-info"></i> Detail
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
                <form action="{{ route('pesanan.update', $item->id) }}" method="POST" class="modal-content">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title text-success">Selesaikan Pesanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <h5 class="text-center">Yakin ingin menyelesaikan pesanan ini?</h5>
                        <input type="hidden" name="status" value="selesai">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button class="btn btn-success">Selesaikan</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="modalCancel{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('pesanan.update', $item->id) }}" method="POST" class="modal-content">
                    @csrf @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title text-warning">Batalkan Pesanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h5 class="text-center">Yakin ingin membatalkan pesanan ini?</h5>
                        <p class="text-center text-muted">Stok akan dikembalikan ke semula.</p>
                        <input type="hidden" name="status" value="batal">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button class="btn btn-warning">Batalkan</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Produk – Order: {{ $item->order_id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($item->detailPesanan->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
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
                                                <td>{{ $detail->jumlah }}/{{ $detail->satuan ?? 'pcs' }}</td>
                                                <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($detail->jumlah * $detail->harga, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center">Tidak ada produk untuk pesanan ini.</p>
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
