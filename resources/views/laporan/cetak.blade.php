<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Cetak Laporan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        h3,
        h4,
        p {
            margin: 0 0 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        th {
            background: #eee;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .summary-row {
            background: #f5f5f5;
            font-weight: bold;
        }
    </style>
</head>

<body>
    @if (($mode ?? 'periode') === 'tahunan' && !empty($annualReport))
        <h3 class="text-center">Laporan Tahunan Salsa Liwa</h3>
        <h4 class="text-center">Tahun {{ $tahun }}</h4>
        <p class="text-center">
            Bulan:
            {{ collect($bulanDipilih ?? [])->map(fn($bulan) => \Illuminate\Support\Carbon::create()->month($bulan)->translatedFormat('F'))->implode(', ') }}
        </p>

        <table>
            <thead>
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
                        <td>{{ $monthRow['label'] }}</td>
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
                        <td>{{ $summaryRow['label'] }}</td>
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
    @else
        <h4 style="text-align: center;">Laporan {{ ucfirst($jenis) }} Barang</h4>
        <p>Periode: {{ date('d/m/Y', strtotime($dari)) }} - {{ date('d/m/Y', strtotime($sampai)) }}</p>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Order Id</th>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $grandTotal = 0;
                @endphp
                @foreach ($data as $item)
                    @foreach ($item->detailPesanan as $detail)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $item->order_id }}</td>
                            <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                            <td>{{ $detail->kode_barang }} - {{ $detail->nama_barang }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>Rp.{{ number_format($detail->harga, 0, ',', '.') }}</td>
                            <td>Rp.{{ number_format($detail->jumlah * $detail->harga, 0, ',', '.') }}</td>
                        </tr>
                        @php $grandTotal += $detail->jumlah * $detail->harga; @endphp
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-right">Total</th>
                    <th>Rp.{{ number_format($grandTotal, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    @endif
</body>

</html>
