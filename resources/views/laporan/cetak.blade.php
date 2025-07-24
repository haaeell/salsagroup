<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Cetak Laporan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>
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
                $grand_total = 0;
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
                    @php $grand_total += $detail->jumlah * $detail->harga; @endphp
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" style="text-align:right;">Total</th>
                <th>Rp.{{ number_format($grand_total, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
