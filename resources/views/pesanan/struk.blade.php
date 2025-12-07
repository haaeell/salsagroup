<!DOCTYPE html>
<html>

<head>
    <title>Struk Pembelian</title>
</head>

<body>

    <h3>Struk Pembelian</h3>
    <p><strong>Nama:</strong> {{ $pesanan->user->username }}</p>
    <p><strong>No. HP:</strong> {{ $pesanan->user->no_telepon }}</p>
    <p><strong>Alamat:</strong> {{ $pesanan->user->alamat }}</p>
    <hr>

    <table border="1" width="100%" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp

            @foreach ($pesanan->detailPesanan as $detail)
                @php
                    $subtotal = $detail->jumlah * $detail->harga;
                    $total += $subtotal;
                @endphp

                <tr>
                    <td>{{ $detail->nama_barang }}</td>
                    <td>{{ $detail->jumlah }}</td>
                    <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="text-align:right;">Total: Rp {{ number_format($total, 0, ',', '.') }}</h3>

    <script>
        window.print();
    </script>

</body>

</html>
