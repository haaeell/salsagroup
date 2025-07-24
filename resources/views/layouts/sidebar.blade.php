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
            {{-- <li class="menu-header">Data Wisata</li>
            <li class="{{ request()->is('kategori') ? 'active' : '' }}">
                <a href="{{ route('kategori.index') }}" class="nav-link">
                    <i class="bi bi-tags"></i>
                    <span>Kategori</span>
                </a>
            </li>
            <li class="{{ request()->is('wisata') ? 'active' : '' }}">
                <a href="{{ route('wisata.index') }}" class="nav-link">
                    <i class="bi bi-map"></i>
                    <span>Destinasi Wisata</span>
                </a>
            </li>
            <li class="{{ request()->is('wisatawan') ? 'active' : '' }}">
                <a href="{{ route('wisatawan') }}" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span>Wisatawan</span>
                </a>
            </li>
            <li class="{{ request()->is('artikels') ? 'active' : '' }}">
                <a href="{{ route('artikels.index') }}" class="nav-link">
                    <i class="bi bi-book"></i>
                    <span>Artikel</span>
                </a>
            </li> --}}
    </aside>
</div>
