<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Perpustakaan Digital - Manajemen Buku & Peminjaman">
    <title>@yield('title', 'Perpustakaan Digital') — digitallib</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-book-half"></i></div>
            <div>
                <div class="brand-text">digitallib</div>
                <div class="brand-sub">Perpustakaan Digital</div>
            </div>
        </a>

        <nav class="sidebar-nav">
            <div class="nav-label">Menu Utama</div>
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </div>

            <div class="nav-label" style="margin-top: 10px;">Master Data</div>
            <div class="nav-item">
                <a href="{{ route('bukus.index') }}" class="nav-link {{ request()->routeIs('bukus.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-richtext"></i> Buku
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('kategoris.index') }}" class="nav-link {{ request()->routeIs('kategoris.*') ? 'active' : '' }}">
                    <i class="bi bi-bookmark-star-fill"></i> Kategori
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('penulis.index') }}" class="nav-link {{ request()->routeIs('penulis.*') ? 'active' : '' }}">
                    <i class="bi bi-person-vcard-fill"></i> Penulis
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('penerbits.index') }}" class="nav-link {{ request()->routeIs('penerbits.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Penerbit
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i> Kelola Akun
                </a>
            </div>

            <div class="nav-label" style="margin-top: 10px;">Transaksi</div>
            <div class="nav-item">
                <a href="{{ route('peminjaman.index') }}" class="nav-link {{ request()->routeIs('peminjaman.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i> Peminjaman
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
                <div class="page-title">@yield('page-title', '<span>Dashboard</span>')</div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span style="font-size:0.78rem; color:var(--text-muted);">
                    <i class="bi bi-circle-fill" style="color:#22c55e; font-size:0.5rem;"></i>
                    API Connected
                </span>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- App JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
