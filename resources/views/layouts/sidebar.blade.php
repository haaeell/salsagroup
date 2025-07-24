<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">SALSA GROUP</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">St</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class="{{ request()->is('home') ? 'active' : '' }}">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @if (Auth::user()->role == 'admin')
                <li class="menu-header">Kategori</li>
                <li class="{{ request()->is('kategori') ? 'active' : '' }}">
                    <a href="{{ route('kategori.index') }}" class="nav-link">
                        <i class="bi bi-tags"></i>
                        <span>Kategori</span>
                    </a>
                </li>
                <li class="{{ request()->is('barang') ? 'active' : '' }}">
                    <a href="{{ route('barang.index') }}" class="nav-link">
                        <i class="bi bi-box"></i>
                        <span>Barang</span>
                    </a>
                </li>
                <li class="{{ request()->is('barang-masuk') ? 'active' : '' }}">
                    <a href="{{ route('barang-masuk.index') }}" class="nav-link">
                        <i class="bi bi-arrow-up"></i>
                        <span>Barang Masuk</span>
                    </a>
                </li>
                <li class="{{ request()->is('riwayat-pesanan') ? 'active' : '' }}">
                    <a href="{{ route('riwayat-pesanan.index') }}" class="nav-link">
                        <i class="bi bi-book"></i>
                        <span>Pesanan</span>
                    </a>
                </li>
                <li class="{{ request()->is('laporan') ? 'active' : '' }}">
                    <a href="{{ route('laporan.index') }}" class="nav-link">
                        <i class="bi bi-book"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                <li class="{{ request()->is('users') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>
                </li>
            @endif

            @if (Auth::user()->role == 'user')
                <li class="{{ request()->is('pesanan') ? 'active' : '' }}">
                    <a href="{{ route('pesanan.index') }}" class="nav-link">
                        <i class="bi bi-book"></i>
                        <span>Daftar Produk</span>
                    </a>
                </li>
                <li class="{{ request()->is('riwayat-pesanan') ? 'active' : '' }}">
                    <a href="{{ route('riwayat-pesanan.index') }}" class="nav-link">
                        <i class="bi bi-book"></i>
                        <span>Riwayat</span>
                    </a>
                </li>
            @endif
        </ul>
    </aside>
</div>
