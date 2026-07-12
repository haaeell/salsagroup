@extends('layouts.dashboard')

@section('title', 'Laporan')

@section('content')
    <style>
        .viz-root {
            --surface-1: #fcfcfb;
            --surface-2: #f4f2ec;
            --text-primary: #0b0b0b;
            --text-secondary: #52514e;
            --text-muted: #898781;
            --gridline: #e1e0d9;
            --baseline: #c3c2b7;
            --series-1: #2a78d6;
            --series-2: #1baf7a;
            --series-3: #eda100;
            --series-4: #008300;
            --series-5: #4a3aa7;
            --series-6: #e34948;
            --good: #0ca30c;
        }

        .stats-grid {
            margin-bottom: 1.5rem;
        }

        .stats-grid>[class*='col-'] {
            display: flex;
        }

        .stat-tile {
            background: var(--surface-1);
            border: 1px solid var(--gridline);
            border-radius: 20px;
            padding: 20px 22px;
            height: 100%;
            width: 100%;
            min-height: 190px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 20px;
            box-shadow: 0 14px 30px rgba(27, 35, 52, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-tile:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 36px rgba(27, 35, 52, 0.1);
        }

        .stat-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .stat-copy {
            min-width: 0;
        }

        .stat-tile .stat-label {
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.45;
            margin-bottom: 0;
        }

        .stat-note {
            color: var(--text-muted);
            font-size: 12px;
            margin-top: 6px;
        }

        .stat-tile .stat-value {
            color: var(--text-primary);
            font-size: clamp(1.85rem, 1.5rem + 0.8vw, 2.65rem);
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -0.03em;
            margin: 0;
            word-break: break-word;
        }

        .stat-tile .stat-value.currency {
            font-size: clamp(1.7rem, 1.35rem + 0.8vw, 2.45rem);
            line-height: 1.12;
        }

        .stat-tile .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 22px;
            color: #fff;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .chart-card {
            background: var(--surface-1);
            border: 1px solid var(--gridline);
            border-radius: 20px;
            padding: 22px 24px;
            height: 100%;
            box-shadow: 0 14px 30px rgba(27, 35, 52, 0.05);
        }

        .chart-card .chart-title {
            color: var(--text-primary);
            font-weight: 800;
            font-size: 1.15rem;
            margin-bottom: 6px;
        }

        .chart-card .chart-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 18px;
        }

        .table-card {
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid var(--gridline);
            box-shadow: 0 14px 30px rgba(27, 35, 52, 0.05);
        }

        .table-card .card-header {
            background: linear-gradient(180deg, #fffdf8 0%, var(--surface-2) 100%);
            border-bottom: 1px solid var(--gridline);
            padding: 18px 24px;
        }

        .table-card .card-body {
            padding: 0;
        }

        .table-card .table {
            margin-bottom: 0;
        }

        .table-card .table-responsive {
            padding: 0 24px 24px;
        }

        .annual-table {
            min-width: 1100px;
        }

        .annual-table thead th {
            white-space: nowrap;
            vertical-align: middle;
            text-align: center;
        }

        .annual-table tbody th,
        .annual-table tbody td,
        .annual-table tfoot th,
        .annual-table tfoot td {
            vertical-align: middle;
        }

        .annual-table .summary-row {
            background: #fbfaf6;
            font-weight: 700;
        }

        @media (max-width: 991.98px) {
            .stat-tile {
                min-height: 170px;
            }
        }

        @media (max-width: 575.98px) {
            .stat-tile {
                min-height: auto;
                padding: 18px;
                border-radius: 18px;
            }

            .stat-tile .stat-icon {
                width: 48px;
                height: 48px;
                border-radius: 14px;
                font-size: 20px;
            }

            .chart-card,
            .table-card .card-header,
            .table-card .table-responsive {
                padding-left: 18px;
                padding-right: 18px;
            }
        }
    </style>

    <div class="container-fluid viz-root">

        <div class="card mb-4">
            <div class="card-header fw-bold">Form Laporan</div>
            <div class="card-body">
                <form action="{{ route('laporan.index') }}" method="GET" class="row align-items-end g-3">
                    <div class="col-md-3">
                        <label for="mode" class="form-label">Mode Laporan</label>
                        <select name="mode" id="mode" class="form-control" required>
                            <option value="periode" {{ $mode === 'periode' ? 'selected' : '' }}>Periode Tanggal</option>
                            <option value="tahunan" {{ $mode === 'tahunan' ? 'selected' : '' }}>Tahunan per Bulan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="jenis" class="form-label">Jenis Laporan</label>
                        <select name="jenis" id="jenis" class="form-control select2" required>
                            <option value="">*Pilih Laporan*</option>
                            <option value="pemesanan" {{ $jenis == 'pemesanan' ? 'selected' : '' }}>
                                Pemesanan (Proses)</option>
                            <option value="pembelian" {{ $jenis == 'pembelian' ? 'selected' : '' }}>
                                Pembelian (Selesai)</option>
                            <option value="barang_masuk" {{ $jenis == 'barang_masuk' ? 'selected' : '' }}>
                                Barang Masuk</option>
                        </select>
                    </div>
                    <div class="col-md-2 period-filter">
                        <label for="dari" class="form-label">Dari Tanggal</label>
                        <input type="date" name="dari" id="dari" value="{{ $dari }}"
                            class="form-control" required>
                    </div>
                    <div class="col-md-2 period-filter">
                        <label for="sampai" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="sampai" id="sampai" value="{{ $sampai }}"
                            class="form-control" required>
                    </div>
                    <div class="col-md-2 annual-filter">
                        <label for="tahun" class="form-label">Tahun</label>
                        <select name="tahun" id="tahun" class="form-control">
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}" {{ (int) $tahun === (int) $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 annual-filter">
                        <label for="bulan" class="form-label">Pilih Bulan</label>
                        <select name="bulan[]" id="bulan" class="form-control select2" multiple>
                            @foreach ($monthOptions as $option)
                                <option value="{{ $option['value'] }}"
                                    {{ in_array($option['value'], $bulanDipilih ?? [], true) ? 'selected' : '' }}>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Boleh pilih satu atau beberapa bulan sekaligus.</small>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        @if (!empty($data) && count($data) > 0)
            @if ($mode === 'tahunan' && $annualReport)
                <div class="row stats-grid">
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="stat-tile">
                            <div class="stat-head">
                                <div class="stat-copy">
                                    <div class="stat-label">Periode Tahunan</div>
                                    <div class="stat-note">Bulan terpilih pada tahun {{ $tahun }}</div>
                                </div>
                                <div class="stat-icon" style="background: var(--series-1);"><i
                                        class="bi bi-calendar-range"></i></div>
                            </div>
                            <div class="stat-value">{{ count($bulanDipilih) }} Bulan</div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="stat-tile">
                            <div class="stat-head">
                                <div class="stat-copy">
                                    <div class="stat-label">Total Pendapatan</div>
                                    <div class="stat-note">Akumulasi semua kategori</div>
                                </div>
                                <div class="stat-icon" style="background: var(--good);"><i
                                        class="bi bi-cash-stack"></i></div>
                            </div>
                            <div class="stat-value currency">Rp
                                {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="stat-tile">
                            <div class="stat-head">
                                <div class="stat-copy">
                                    <div class="stat-label">Total Laba</div>
                                    <div class="stat-note">Selisih penjualan dan modal</div>
                                </div>
                                <div class="stat-icon" style="background: var(--series-2);"><i
                                        class="bi bi-graph-up-arrow"></i></div>
                            </div>
                            <div class="stat-value currency">Rp
                                {{ number_format($summary['total_laba'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 table-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Laporan Tahunan {{ $tahun }}</h5>
                        <a href="{{ route('laporan.cetak', ['mode' => $mode, 'jenis' => $jenis, 'tahun' => $tahun, 'bulan' => $bulanDipilih]) }}"
                            target="_blank" class="btn btn-success">
                            <i class="fa fa-print"></i> Cetak Laporan Tahunan
                        </a>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered annual-table">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">Bulan</th>
                                    @foreach ($annualReport['categories'] as $category)
                                        <th colspan="3">{{ strtoupper($category) }}</th>
                                    @endforeach
                                    <th colspan="3">SEMUA ITEM</th>
                                </tr>
                                <tr>
                                    @foreach ($annualReport['categories'] as $category)
                                        <th>Pendapatan</th>
                                        <th>Modal</th>
                                        <th>Laba</th>
                                    @endforeach
                                    <th>Pendapatan</th>
                                    <th>Modal</th>
                                    <th>Laba</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($annualReport['months'] as $monthRow)
                                    <tr>
                                        <th>{{ $monthRow['label'] }}</th>
                                        @foreach ($annualReport['categories'] as $category)
                                            @php $metric = $monthRow['categories'][$category]; @endphp
                                            <td>{{ number_format($metric['pendapatan'], 0, ',', '.') }}</td>
                                            <td>{{ number_format($metric['modal'], 0, ',', '.') }}</td>
                                            <td>{{ number_format($metric['laba'], 0, ',', '.') }}</td>
                                        @endforeach
                                        <td>{{ number_format($monthRow['overall']['pendapatan'], 0, ',', '.') }}</td>
                                        <td>{{ number_format($monthRow['overall']['modal'], 0, ',', '.') }}</td>
                                        <td>{{ number_format($monthRow['overall']['laba'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                @foreach ($annualReport['summary_rows'] as $summaryRow)
                                    <tr class="summary-row">
                                        <th>{{ $summaryRow['label'] }}</th>
                                        @foreach ($annualReport['categories'] as $category)
                                            @php $metric = $summaryRow['categories'][$category]; @endphp
                                            <td>{{ number_format($metric['pendapatan'], 0, ',', '.') }}</td>
                                            <td>{{ number_format($metric['modal'], 0, ',', '.') }}</td>
                                            <td>{{ number_format($metric['laba'], 0, ',', '.') }}</td>
                                        @endforeach
                                        <td>{{ number_format($summaryRow['overall']['pendapatan'], 0, ',', '.') }}</td>
                                        <td>{{ number_format($summaryRow['overall']['modal'], 0, ',', '.') }}</td>
                                        <td>{{ number_format($summaryRow['overall']['laba'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <div class="chart-card">
                            <div class="chart-title">Grafik Laba Tahun {{ $tahun }}</div>
                            <div class="chart-subtitle">Perbandingan laba bulanan per kategori untuk bulan yang dipilih</div>
                            <canvas id="chartAnnualLaba" height="95"></canvas>
                        </div>
                    </div>
                </div>
            @elseif ($jenis !== 'barang_masuk')
                <div class="row stats-grid">
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="stat-tile">
                            <div class="stat-head">
                                <div class="stat-copy">
                                    <div class="stat-label">Total Pesanan</div>
                                    <div class="stat-note">Jumlah transaksi pada periode ini</div>
                                </div>
                                <div class="stat-icon" style="background: var(--series-1);"><i
                                        class="bi bi-bag-check-fill"></i></div>
                            </div>
                            <div class="stat-value">{{ number_format($summary['total_pesanan'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="stat-tile">
                            <div class="stat-head">
                                <div class="stat-copy">
                                    <div class="stat-label">Total Pendapatan</div>
                                    <div class="stat-note">Akumulasi penjualan yang tercatat</div>
                                </div>
                                <div class="stat-icon" style="background: var(--good);"><i
                                        class="bi bi-cash-stack"></i></div>
                            </div>
                            <div class="stat-value currency">Rp
                                {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @if ($jenis === 'pembelian')
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="stat-tile">
                                <div class="stat-head">
                                    <div class="stat-copy">
                                        <div class="stat-label">Total Modal</div>
                                        <div class="stat-note">Biaya pokok seluruh transaksi</div>
                                    </div>
                                    <div class="stat-icon" style="background: var(--series-6);"><i
                                            class="bi bi-wallet2"></i></div>
                                </div>
                                <div class="stat-value currency">Rp
                                    {{ number_format($summary['total_modal'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="stat-tile">
                                <div class="stat-head">
                                    <div class="stat-copy">
                                        <div class="stat-label">Total Laba</div>
                                        <div class="stat-note">Selisih pendapatan dan modal</div>
                                    </div>
                                    <div class="stat-icon" style="background: var(--series-2);"><i
                                            class="bi bi-piggy-bank-fill"></i></div>
                                </div>
                                <div class="stat-value currency">Rp
                                    {{ number_format($summary['total_laba'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endif
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="stat-tile">
                            <div class="stat-head">
                                <div class="stat-copy">
                                    <div class="stat-label">Item Terjual</div>
                                    <div class="stat-note">Total unit yang keluar dari stok</div>
                                </div>
                                <div class="stat-icon" style="background: var(--series-3);"><i
                                        class="bi bi-box-seam-fill"></i></div>
                            </div>
                            <div class="stat-value">
                                {{ number_format($summary['total_item_terjual'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="stat-tile">
                            <div class="stat-head">
                                <div class="stat-copy">
                                    <div class="stat-label">Rata-rata / Transaksi</div>
                                    <div class="stat-note">Nilai penjualan per order</div>
                                </div>
                                <div class="stat-icon" style="background: var(--series-5);"><i
                                        class="bi bi-graph-up-arrow"></i></div>
                            </div>
                            <div class="stat-value currency">Rp
                                {{ number_format($summary['rata_rata_transaksi'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Charts --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-7">
                        <div class="chart-card">
                            <div class="chart-title">Tren Pendapatan Harian</div>
                            <div class="chart-subtitle">Periode {{ date('d/m/Y', strtotime($dari)) }} -
                                {{ date('d/m/Y', strtotime($sampai)) }}</div>
                            <canvas id="chartTrend" height="110"></canvas>
                            <table class="visually-hidden" aria-hidden="false">
                                <caption>Tren Pendapatan Harian</caption>
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Pendapatan</th>
                                        <th>Jumlah Pesanan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($trend as $row)
                                        <tr>
                                            <td>{{ $row['tanggal'] }}</td>
                                            <td>{{ $row['total'] }}</td>
                                            <td>{{ $row['jumlah_pesanan'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="chart-card">
                            <div class="chart-title">Kategori Terlaris</div>
                            <div class="chart-subtitle">Berdasarkan total nilai penjualan</div>
                            <canvas id="chartKategori" height="110"></canvas>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <div class="chart-card">
                            <div class="chart-title">5 Produk Terlaris</div>
                            <div class="chart-subtitle">Berdasarkan jumlah unit terjual</div>
                            <canvas id="chartTopProduk" height="80"></canvas>
                        </div>
                    </div>
                </div>
            @else
                <div class="row stats-grid">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stat-tile">
                            <div class="stat-head">
                                <div class="stat-copy">
                                    <div class="stat-label">Total Transaksi Masuk</div>
                                    <div class="stat-note">Jumlah pencatatan barang masuk</div>
                                </div>
                                <div class="stat-icon" style="background: var(--series-1);"><i
                                        class="bi bi-box-arrow-in-down"></i></div>
                            </div>
                            <div class="stat-value">{{ number_format($summary['total_pesanan'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stat-tile">
                            <div class="stat-head">
                                <div class="stat-copy">
                                    <div class="stat-label">Total Unit Masuk</div>
                                    <div class="stat-note">Akumulasi kuantitas stok masuk</div>
                                </div>
                                <div class="stat-icon" style="background: var(--series-3);"><i
                                        class="bi bi-box-seam-fill"></i></div>
                            </div>
                            <div class="stat-value">
                                {{ number_format($summary['total_item_terjual'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <div class="chart-card">
                            <div class="chart-title">Tren Barang Masuk Harian</div>
                            <div class="chart-subtitle">Periode {{ date('d/m/Y', strtotime($dari)) }} -
                                {{ date('d/m/Y', strtotime($sampai)) }}</div>
                            <canvas id="chartTrend" height="90"></canvas>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Detail table --}}
                <div class="card mb-4 table-card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">
                        {{ $mode === 'tahunan' ? 'Detail Laporan Tahunan' : 'Detail ' . ucfirst(str_replace('_', ' ', $jenis)) }}
                    </h5>
                    @if ($mode === 'periode' && $jenis !== 'barang_masuk')
                        <a href="{{ route('laporan.cetak', ['mode' => $mode, 'jenis' => $jenis, 'dari' => $dari, 'sampai' => $sampai]) }}"
                            target="_blank" class="btn btn-success">
                            <i class="fa fa-print"></i> Cetak Laporan
                        </a>
                    @endif
                </div>
                <div class="card-body table-responsive">
                    @if ($jenis === 'barang_masuk')
                        <table class="table table-bordered align-middle" id="datatable">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $i => $item)
                                    <tr class="text-start">
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ date('d/m/Y', strtotime($item->tanggal_masuk)) }}</td>
                                        <td>{{ $item->barang->nama ?? '-' }}</td>
                                        <td>{{ $item->jumlah }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
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
                                    @if ($jenis === 'pembelian')
                                        <th>Laba</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($data as $item)
                                    @foreach ($item->detailPesanan as $detail)
                                        <tr class="text-start">
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $item->order_id }}</td>
                                            <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                            <td>{{ $detail->kode_barang }} - {{ $detail->nama_barang }}</td>
                                            <td>{{ $detail->jumlah }}</td>
                                            <td>Rp.{{ number_format($detail->harga, 0, ',', '.') }}</td>
                                            <td>Rp.{{ number_format($detail->jumlah * $detail->harga, 0, ',', '.') }}</td>
                                            @if ($jenis === 'pembelian')
                                                <td>Rp.{{ number_format($detail->jumlah * ($detail->harga - $detail->harga_modal), 0, ',', '.') }}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-end">Total:</th>
                                    <th>Rp.{{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</th>
                                    @if ($jenis === 'pembelian')
                                        <th>Rp.{{ number_format($summary['total_laba'], 0, ',', '.') }}</th>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    @endif
                </div>
            </div>
        @elseif(request()->all())
            <div class="alert alert-warning">Data tidak ditemukan untuk filter tersebut.</div>
        @endif

    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const modeSelect = document.getElementById('mode');
            const jenisSelect = document.getElementById('jenis');
            const periodFilters = document.querySelectorAll('.period-filter');
            const annualFilters = document.querySelectorAll('.annual-filter');

            function toggleReportMode() {
                const isTahunan = modeSelect && modeSelect.value === 'tahunan';

                periodFilters.forEach((element) => {
                    element.style.display = isTahunan ? 'none' : '';
                    element.querySelectorAll('input').forEach((input) => {
                        input.required = !isTahunan;
                    });
                });

                annualFilters.forEach((element) => {
                    element.style.display = isTahunan ? '' : 'none';
                    element.querySelectorAll('select').forEach((select) => {
                        if (select.name === 'tahun') {
                            select.required = isTahunan;
                        }
                    });
                });

                if (isTahunan && jenisSelect && jenisSelect.value === 'barang_masuk') {
                    jenisSelect.value = 'pembelian';
                    if (window.jQuery) {
                        window.jQuery(jenisSelect).trigger('change');
                    }
                }
            }

            if (modeSelect) {
                modeSelect.addEventListener('change', toggleReportMode);
                toggleReportMode();
            }
        })();
    </script>
@endpush

@if (!empty($data) && count($data) > 0)
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            (function() {
                const root = document.querySelector('.viz-root');
                const style = getComputedStyle(root);
                const colors = {
                    series1: style.getPropertyValue('--series-1').trim(),
                    series2: style.getPropertyValue('--series-2').trim(),
                    series3: style.getPropertyValue('--series-3').trim(),
                    series4: style.getPropertyValue('--series-4').trim(),
                    series5: style.getPropertyValue('--series-5').trim(),
                    series6: style.getPropertyValue('--series-6').trim(),
                    text: style.getPropertyValue('--text-secondary').trim(),
                    muted: style.getPropertyValue('--text-muted').trim(),
                    grid: style.getPropertyValue('--gridline').trim(),
                };
                const palette = [colors.series1, colors.series2, colors.series3, colors.series4, colors.series5,
                    colors.series6, '#6b7280', '#f97316', '#14b8a6', '#e11d48'
                ];

                Chart.defaults.font.family = 'system-ui, -apple-system, "Segoe UI", sans-serif';
                Chart.defaults.color = colors.text;

                @if ($mode === 'tahunan' && $annualReport)
                    const annualChart = @json($annualReport['chart']);
                    new Chart(document.getElementById('chartAnnualLaba'), {
                        type: 'line',
                        data: {
                            labels: annualChart.labels,
                            datasets: annualChart.datasets.map((dataset, index) => ({
                                label: dataset.label,
                                data: dataset.data,
                                borderColor: palette[index % palette.length],
                                backgroundColor: palette[index % palette.length] + '1a',
                                pointRadius: 4,
                                pointHoverRadius: 5,
                                borderWidth: 2,
                                fill: false,
                                tension: 0.25,
                            }))
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'right'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(ctx) {
                                            return ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: colors.grid
                                    },
                                    ticks: {
                                        callback: function(val) {
                                            return val.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            }
                        }
                    });
                @else
                    const trend = @json($trend);
                    const trendLabels = trend.map(r => {
                        const parsed = new Date(r.tanggal);
                        if (Number.isNaN(parsed.getTime())) {
                            return r.tanggal;
                        }

                        return parsed.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short'
                        });
                    });

                    new Chart(document.getElementById('chartTrend'), {
                        type: 'line',
                        data: {
                            labels: trendLabels,
                            datasets: [{
                                label: '{{ $jenis === 'barang_masuk' ? 'Jumlah Masuk' : 'Pendapatan' }}',
                                data: trend.map(r => r.total),
                                borderColor: colors.series1,
                                backgroundColor: colors.series1 + '1a',
                                borderWidth: 2,
                                pointRadius: 4,
                                pointBackgroundColor: colors.series1,
                                pointBorderColor: style.getPropertyValue('--surface-1').trim(),
                                pointBorderWidth: 2,
                                fill: true,
                                tension: 0.3,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(ctx) {
                                            const val = ctx.parsed.y;
                                            return '{{ $jenis === 'barang_masuk' ? '' : 'Rp ' }}' + val
                                                .toLocaleString('id-ID');
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: colors.grid
                                    },
                                    ticks: {
                                        callback: function(val) {
                                            return val.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            }
                        }
                    });

                    @if ($jenis !== 'barang_masuk')
                        const kategoriData = @json($kategoriBreakdown);
                        new Chart(document.getElementById('chartKategori'), {
                            type: 'doughnut',
                            data: {
                                labels: kategoriData.map(k => k.kategori),
                                datasets: [{
                                    data: kategoriData.map(k => k.total),
                                    backgroundColor: palette,
                                    borderColor: style.getPropertyValue('--surface-1').trim(),
                                    borderWidth: 2,
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            boxWidth: 10,
                                            padding: 12
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(ctx) {
                                                return ctx.label + ': Rp ' + ctx.parsed.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                }
                            }
                        });

                        const topProduk = @json($topProduk);
                        new Chart(document.getElementById('chartTopProduk'), {
                            type: 'bar',
                            data: {
                                labels: topProduk.map(p => p.nama),
                                datasets: [{
                                    label: 'Unit Terjual',
                                    data: topProduk.map(p => p.jumlah),
                                    backgroundColor: colors.series1,
                                    borderRadius: 4,
                                    maxBarThickness: 40,
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(ctx) {
                                                return ctx.parsed.y.toLocaleString('id-ID') + ' unit';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            display: false
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: colors.grid
                                        },
                                        ticks: {
                                            callback: function(val) {
                                                return val.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    @endif
                @endif
            })();
        </script>
    @endpush
@endif
