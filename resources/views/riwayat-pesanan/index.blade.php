@extends('layouts.dashboard')

@section('title', 'Riwayat Pesanan')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Riwayat Pesanan</h5>
            @if (Auth::user()->role == 'admin')
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus"></i> Tambah
                </button>
            @endif
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
                        <th>Metode Bayar</th>
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
                            <td>
                                @if ($item->metode_pembayaran)
                                    <span class="badge bg-info text-white">
                                        {{ ucfirst($item->metode_pembayaran) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $item->catatan }}</td>
                            <td class="d-flex flex-column gap-2">
                                @if ($item->status == 'proses')
                                    <button class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#modalDone{{ $item->id }}">
                                        <i class="fa fa-check"></i> Selesaikan
                                    </button>
                                    <button class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#modalCancel{{ $item->id }}">
                                        <i class="fa fa-times"></i> Batalkan
                                    </button>
                                @elseif ($item->status == 'selesai')
                                    <p class="text-success mb-0"><i class="fa fa-check"></i> Pesanan Selesai</p>
                                @else
                                    <p class="text-danger"><i class="fa fa-times"></i> Pesanan Batal</p>
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

    @if (Auth::user()->role == 'admin')
        <!-- Modal Tambah Pesanan (Admin) -->
        <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('pesanan.storeAdmin') }}" method="POST" class="modal-content shadow-sm rounded-3"
                    id="formTambahPesanan">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-bag-plus-fill me-2"></i> Tambah Pesanan Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select select2 w-100" required>
                                <option value="">-- Pilih Customer --</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}">{{ trim($u->nama_depan . ' ' . $u->nama_belakang) }}
                                        ({{ $u->username }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Produk <span class="text-danger">*</span></label>
                            <div id="itemRows"></div>
                            <button type="button" class="btn btn-sm btn-success mt-2" id="btnTambahItem">
                                <i class="bi bi-plus-circle"></i> Tambah Produk
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="d-flex justify-content-end fw-bold">
                            <span>Total: <span id="totalHargaTambah">Rp 0</span></span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Tutup
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-check2-circle"></i> Simpan Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @foreach ($pesanan as $item)
        <div class="modal fade" id="modalDone{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('pesanan.update', $item->id) }}" method="POST" enctype="multipart/form-data"
                    class="modal-content shadow-sm rounded-3">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-check-circle-fill me-2"></i> Selesaikan Pesanan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        {{-- Status --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Pesanan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-list-check"></i></span>
                                <select name="status" class="form-control status-pesanan-select" required>
                                    <option value="selesai" {{ $item->status == 'selesai' ? 'selected' : '' }}>Selesai
                                    </option>
                                    <option value="batal" {{ $item->status == 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 metode-pembayaran-group">
                            <label class="form-label fw-semibold">Metode Pembayaran</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-credit-card-2-front"></i></span>
                                <select name="metode_pembayaran" class="form-control metode-pembayaran-select">
                                    <option value="">-- Pilih Metode Pembayaran --</option>
                                    <option value="cash"
                                        {{ $item->metode_pembayaran === 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="transfer"
                                        {{ $item->metode_pembayaran === 'transfer' ? 'selected' : '' }}>Transfer</option>
                                </select>
                            </div>
                            <small class="text-muted">Pilih metode pembayaran untuk pesanan yang diselesaikan.</small>
                        </div>

                        {{-- Upload Bukti Pembayaran --}}
                        <div class="mb-3 bukti-pembayaran-group">
                            <label class="form-label fw-semibold">Upload Bukti Pembayaran</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-cloud-upload"></i></span>
                                <input type="file" name="bukti_pembayaran" class="form-control bukti-pembayaran-input"
                                    accept="image/*">
                            </div>
                            <small class="text-muted bukti-pembayaran-help">Upload wajib jika metode pembayaran transfer.</small>
                        </div>

                        {{-- Preview bukti sebelumnya --}}
                        @if ($item->bukti_pembayaran)
                            <div class="mb-3 text-center bukti-pembayaran-preview">
                                <label class="fw-semibold d-block mb-2">Bukti Pembayaran Sebelumnya:</label>
                                <img src="{{ asset('storage/' . $item->bukti_pembayaran) }}"
                                    class="img-thumbnail rounded shadow-sm" width="180">
                            </div>
                        @endif

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Tutup
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check2-circle"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <div class="modal fade" id="modalCancel{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('pesanan.update', $item->id) }}" method="POST"
                    class="modal-content rounded-3 shadow-sm">
                    @csrf @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title text-dark">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Batalkan Pesanan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center">
                        <h5>Yakin ingin membatalkan pesanan ini?</h5>
                        <p class="text-muted">
                            Stok barang akan dikembalikan seperti semula.
                        </p>
                        <input type="hidden" name="status" value="batal">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Tutup
                        </button>
                        <button class="btn btn-warning">
                            <i class="bi bi-trash-fill"></i> Batalkan Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content rounded-3 shadow-sm">

                    <div class="modal-header text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-receipt-cutoff me-2"></i>
                            Detail Pesanan – {{ $item->order_id }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        @if ($item->detailPesanan->count() > 0)
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Metode Pembayaran:</strong>
                                    {{ $item->metode_pembayaran ? ucfirst($item->metode_pembayaran) : '-' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Bukti Pembayaran:</strong>
                                    @if ($item->bukti_pembayaran)
                                        <a href="{{ asset('storage/' . $item->bukti_pembayaran) }}" target="_blank">
                                            Lihat Bukti
                                        </a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="table-responsive mt-2">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead class="table-light">
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
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endsection

@if (Auth::user()->role == 'admin')
    @push('scripts')
        <script>
            const daftarBarang = @json($barang);

        function syncMetodePembayaran(modal) {
            const status = modal.find('.status-pesanan-select').val();
            const metodeGroup = modal.find('.metode-pembayaran-group');
            const metodeSelect = modal.find('.metode-pembayaran-select');
            const buktiGroup = modal.find('.bukti-pembayaran-group');
            const buktiInput = modal.find('.bukti-pembayaran-input');
            const buktiHelp = modal.find('.bukti-pembayaran-help');
            const preview = modal.find('.bukti-pembayaran-preview');

            if (status !== 'selesai') {
                metodeGroup.hide();
                buktiGroup.hide();
                preview.hide();
                metodeSelect.prop('required', false);
                buktiInput.prop('required', false);
                return;
            }

            metodeGroup.show();
            preview.show();
            metodeSelect.prop('required', true);

            if (metodeSelect.val() === 'transfer') {
                buktiGroup.show();
                buktiInput.prop('required', !preview.length);
                buktiHelp.text('Upload wajib jika metode pembayaran transfer.');
            } else {
                buktiGroup.hide();
                buktiInput.prop('required', false);
                buktiInput.val('');
            }
        }

        function optionsBarang(selectedId) {
            let html = '<option value="">-- Pilih Produk --</option>';
            daftarBarang.forEach(b => {
                const selected = selectedId == b.id ? 'selected' : '';
                html +=
                    `<option value="${b.id}" data-harga="${b.harga}" ${selected}>${b.nama} (Stok: ${b.stok})</option>`;
            });
            return html;
        }

        function hitungTotalTambah() {
            let total = 0;
            $('#itemRows .item-row').each(function() {
                const harga = parseInt($(this).find('.select-barang option:selected').data('harga')) || 0;
                const jumlah = parseInt($(this).find('.input-jumlah').val()) || 0;
                total += harga * jumlah;
            });
            $('#totalHargaTambah').text('Rp ' + total.toLocaleString('id-ID'));
        }

        function tambahBarisItem() {
            const row = $(`
            <div class="row item-row align-items-center mb-2">
                <div class="col-md-7">
                    <select name="barang_id[]" class="form-select select-barang" required>
                        ${optionsBarang()}
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="jumlah[]" class="form-control input-jumlah" min="1" value="1" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-hapus-item w-100"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        `);
            $('#itemRows').append(row);
        }

        $(document).ready(function() {
            tambahBarisItem();

            $('#btnTambahItem').on('click', function() {
                tambahBarisItem();
            });

            $(document).on('click', '.btn-hapus-item', function() {
                if ($('#itemRows .item-row').length > 1) {
                    $(this).closest('.item-row').remove();
                    hitungTotalTambah();
                }
            });

            $(document).on('change', '.select-barang', hitungTotalTambah);
            $(document).on('input', '.input-jumlah', hitungTotalTambah);
            $(document).on('change', '.status-pesanan-select, .metode-pembayaran-select', function() {
                syncMetodePembayaran($(this).closest('.modal'));
            });

            $('.modal[id^="modalDone"]').each(function() {
                syncMetodePembayaran($(this));
            });

            $('#modalTambah').on('hidden.bs.modal', function() {
                $('#itemRows').empty();
                tambahBarisItem();
                $('#formTambahPesanan')[0].reset();
                hitungTotalTambah();
            });
        });
        </script>
    @endpush
@endif
