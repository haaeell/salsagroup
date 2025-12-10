@extends('layouts.dashboard')

@section('title', 'Pesanan')

@section('content')
    <style>
        /* PRINT MODE */
        @media print {
            body * {
                visibility: hidden;
            }

            #printArea,
            #printArea * {
                visibility: visible !important;
            }

            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 250px !important;
            }
        }

        #printArea table {
            font-size: 12px;
        }

        #printArea hr {
            border-top: 1px dashed #999;
        }
    </style>

    <div class="row">
        <div class="col-md-8">
            <div class="mb-3 d-flex justify-content-between">
                <input type="text" id="searchInput" class="form-control w-50 mr-3" placeholder="🔍 Cari Produk">
                <select id="filterKategori" class="form-control select2 w-25">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategori as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row" id="productList">
                @foreach ($barang as $item)
                    <div class="col-md-4 product-item" data-kategori="{{ $item->kategori_id }}">
                        <div class="card">
                            <div class="card-body p-3">

                                <div class="position-relative">
                                    <img src="{{ asset('storage/' . $item->gambar) }}" class="card-img-top"
                                        style="height:130px; object-fit:cover;" alt="">

                                    {{-- Badge kategori --}}
                                    <div
                                        style="position: absolute; top: 8px; right: 8px; background-color: rgba(255, 60, 0, 0.927); color: white; padding: 2px 8px; border-radius: 8px; font-size: 10px;">
                                        {{ $item->kategori->nama ?? 'Tanpa Kategori' }}
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <h6 class="fw-bold mb-1 text-truncate" title="{{ $item->nama }}">{{ $item->nama }}
                                    </h6>
                                    <small>Stock: {{ $item->stok }}</small><br>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <p class="text-success fw-bold mb-0">Rp
                                            {{ number_format($item->harga, 0, ',', '.') }}</p>
                                        <p style="font-size: 10px" class="mb-0">{{ $item->kode }}</p>
                                    </div>
                                    <button class="btn btn-sm btn-success w-100 mt-auto addToCartBtn"
                                        data-id="{{ $item->id }}" data-nama="{{ $item->kode }}"
                                        data-harga="{{ $item->harga }}">
                                        <i class="fas fa-cart-plus"></i> Tambah
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Kanan: Checkout -->
        <div class="col-md-4">
            <div class="card p-3">
                <h5 class="fw-bold mb-2 text-center">Checkout</h5>
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <td><strong>Nama:</strong> {{ Auth::user()->username }}</td>
                        </tr>
                        <tr>
                            <td><strong>No. Telepon:</strong> {{ Auth::user()->no_telepon }}</td>
                        </tr>
                        <tr>
                            <td><strong>Alamat:</strong> {{ Auth::user()->alamat }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y') }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr class="text-center">
                                <th>Kode</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                        </tbody>
                    </table>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total Harga:</span>
                    <span id="totalHarga">Rp 0</span>
                </div>
                <div class="form-group mt-2">
                    <label for="catatan">Catatan</label>
                    <textarea id="catatan" class="form-control" rows="2"></textarea>
                </div>
                <div class="d-flex flex-column gap-2 mt-3">
                    <button class="btn btn-success w-100 mb-2 btn-checkout"><i class="bi bi-bag-check"></i>
                        Checkout</button>
                    <button class="btn btn-danger w-100"><i class="bi bi-x-circle"></i> Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Struk -->
    <div class="modal fade" id="strukModal" tabindex="-1">
        <div class="modal-dialog modal-sm"> <!-- kecil seperti struk -->
            <div class="modal-content p-3" id="printArea" style="font-size: 13px;">

                <div class="text-center mb-2">
                    <h6 class="fw-bold m-0">SALSA GROUP</h6>
                    <small class="text-muted">Jl. Lampung No.12</small>
                    <hr class="my-2">
                </div>

                <div id="strukContent"></div>

                <hr class="my-2">
                <div class="text-center">
                    <small>Terima kasih 🙏</small><br>
                    <small>Barang yang sudah dibeli tidak dapat dikembalikan</small>
                </div>

                <div class="mt-3 d-flex justify-content-between">
                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="printStruk()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let cart = {};



        function printStruk() {
            window.print();
        }

        function updateCartTable() {
            const $cartItems = $('#cartItems');
            $cartItems.empty();
            let total = 0;

            $.each(cart, function(id, item) {
                const itemTotal = item.harga * item.jumlah;
                total += itemTotal;
                $cartItems.append(`
                <tr class="text-center">
                    <td>${item.nama}</td>
                    <td>
                        <div class="d-flex justify-content-center align-items-center gap-1">
                            <button class="btn btn-sm btn-success decrease" data-id="${id}">-</button>
                            <span class="fw-bold mx-2">${item.jumlah}</span>
                            <button class="btn btn-sm btn-success increase" data-id="${id}">+</button>
                        </div>
                    </td>
                    <td>Rp ${itemTotal.toLocaleString('id-ID')}</td>
                    <td><button class="btn btn-sm btn-danger removeItem" data-id="${id}">🗑</button></td>
                </tr>
            `);
            });

            $('#totalHarga').text('Rp ' + total.toLocaleString('id-ID'));
        }

        $(document).ready(function() {
            // Tambah ke keranjang
            $(document).on('click', '.addToCartBtn', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const harga = parseInt($(this).data('harga'));

                if (!cart[id]) {
                    cart[id] = {
                        nama: nama,
                        harga: harga,
                        jumlah: 1
                    };
                } else {
                    cart[id].jumlah++;
                }

                updateCartTable();
            });

            // Kurangi jumlah
            $(document).on('click', '.decrease', function() {
                const id = $(this).data('id');
                if (cart[id].jumlah > 1) {
                    cart[id].jumlah--;
                } else {
                    delete cart[id];
                }
                updateCartTable();
            });

            // Tambah jumlah
            $(document).on('click', '.increase', function() {
                const id = $(this).data('id');
                cart[id].jumlah++;
                updateCartTable();
            });

            // Hapus item
            $(document).on('click', '.removeItem', function() {
                const id = $(this).data('id');
                delete cart[id];
                updateCartTable();
            });

            // Filter Kategori
            $('#filterKategori').on('change', function() {
                const q = $(this).val();

                if (q.trim() === '') {
                    $.get("{{ route('produk.cari') }}", {
                        q: ''
                    }, function(res) {
                        renderProduk(res);
                    });
                    return;
                }
                $.get("{{ route('produk.cari') }}", {
                    q
                }, function(res) {
                    renderProduk(res);
                });
            });

            // Pencarian
            $('#searchInput').on('keyup', function() {
                const q = $(this).val();

                if (q.trim() === '') {
                    $.get("{{ route('produk.cari') }}", {
                        q: ''
                    }, function(res) {
                        renderProduk(res);
                    });
                    return;
                }
                $.get("{{ route('produk.cari') }}", {
                    q
                }, function(res) {
                    renderProduk(res);
                });
            });

            function renderProduk(res) {
                let html = '';
                res.forEach(item => {
                    html += `
        <div class="col-md-4 product-item" data-kategori="${item.kategori_id}">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                      <div class="position-relative">
                        <img src="/storage/${item.gambar}" class="card-img-top mb-2"
                            style="height:130px; object-fit:cover;" alt="">

                            <div style="position: absolute; top: 8px; right: 8px; background-color: rgba(255, 60, 0, 0.927); color: white; padding: 2px 8px; border-radius: 8px; font-size: 10px;">
                                ${item.kategori?.nama ?? 'Tanpa Kategori'}
                    </div>
                     </div>
                    <div class="mt-auto">
                        <h6 class="fw-bold text-truncate mb-1" title="${item.nama}">${item.nama}</h6>
                        <small>Stock: ${item.stok}</small><br>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <p class="text-success fw-bold mb-0">Rp ${item.harga.toLocaleString('id-ID')}</p>
                            <p style="font-size: 10px" class="mb-0">${item.kode}</p>
                        </div>
                        <button class="btn btn-sm btn-success w-100 mt-2 addToCartBtn"
                            data-id="${item.id}" data-nama="${item.nama}" data-harga="${item.harga}">
                            <i class="fas fa-cart-plus"></i> Tambah
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
                });
                $('#productList').html(html);
            }

            $('.btn-checkout').first().on('click', function() {
                if (Object.keys(cart).length === 0) {
                    return Swal.fire('Keranjang Kosong', 'Silakan tambahkan produk terlebih dahulu.',
                        'warning');
                }

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Pesanan akan disimpan dan tidak bisa dibatalkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Checkout!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const catatan = $('#catatan').val();

                        const items = Object.keys(cart).map(id => ({
                            barang_id: id,
                            jumlah: cart[id].jumlah
                        }));

                        const data = {
                            user_id: {{ auth()->id() }},
                            catatan,
                            items
                        };

                        $.ajax({
                            url: "{{ route('pesanan.store') }}",
                            method: "POST",
                            data: JSON.stringify(data),
                            contentType: "application/json",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                const pesananId = res.pesanan_id;
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pesanan Berhasil Dibuat!',
                                    text: 'Struk akan tampil setelah ini.',
                                    timer: 1800,
                                    showConfirmButton: false
                                }).then(() => {
                                    showStruk(res, cart);
                                });
                                cart = {};
                                updateCartTable();
                            },

                            error: function(err) {
                                Swal.fire('Error', err.responseJSON.message, 'error');
                                console.error(err);
                            }
                        });
                    }
                });
            });


            $('#btnTutup').on('click', function() {
                $('#strukModal').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Checkout!',
                    text: 'Pesanan Anda telah disimpan.',
                    showConfirmButton: false,
                    timer: 2000
                });
            });

            // Tampilkan struk
            function showStruk(data, keranjang) {
                let html = `
        <div class="mb-2">
            <strong>Kasir:</strong> {{ Auth::user()->username }} <br>
            <strong>Tanggal:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}
        </div>
        <hr>
        <table class="table table-borderless">
            <tbody>
    `;

                let total = 0;

                $.each(keranjang, (id, item) => {
                    const subtotal = item.jumlah * item.harga;
                    total += subtotal;

                    html += `
            <tr>
                <td colspan="3"><strong>${item.nama}</strong></td>
            </tr>
            <tr>
                <td>${item.jumlah} x Rp ${item.harga.toLocaleString('id-ID')}</td>
                <td class="text-end">Rp ${subtotal.toLocaleString('id-ID')}</td>
            </tr>
        `;
                });

                html += `
            </tbody>
        </table>
        <hr>
        <h6 class="text-end fw-bold">TOTAL: Rp ${total.toLocaleString('id-ID')}</h6>
    `;

                $("#strukContent").html(html);
                $("#strukModal").modal("show");
            }
        });
    </script>
@endpush
