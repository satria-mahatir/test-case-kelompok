@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title')
<span>Dashboard</span>
@endsection

@section('content')
<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon indigo"><i class="bi bi-journal-richtext"></i></div>
            <div class="stat-value" id="totalBuku">–</div>
            <div class="stat-label">Total Buku</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon violet"><i class="bi bi-bookmark-star-fill"></i></div>
            <div class="stat-value" id="totalKategori">–</div>
            <div class="stat-label">Kategori</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon amber"><i class="bi bi-arrow-left-right"></i></div>
            <div class="stat-value" id="totalPinjam">–</div>
            <div class="stat-label">Sedang Dipinjam</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon emerald"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-value" id="totalKembali">–</div>
            <div class="stat-label">Dikembalikan</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Peminjaman Terbaru -->
    <div class="col-lg-8">
        <div class="data-card">
            <div class="data-card-header">
                <h5><i class="bi bi-clock-history"></i> Peminjaman Terbaru</h5>
            </div>
            <div class="data-card-body">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Peminjam</th>
                            <th>Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="recentPeminjaman">
                        <tr><td colspan="4" class="text-center" style="padding:30px;color:var(--text-muted);">Memuat...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-lg-4">
        <div class="data-card mb-3">
            <div class="data-card-header">
                <h5><i class="bi bi-person-vcard-fill"></i> Penulis</h5>
            </div>
            <div class="data-card-body" style="padding:20px;">
                <div class="stat-value" id="totalPenulis" style="font-size:2rem;">–</div>
                <div class="stat-label">Total Penulis Terdaftar</div>
            </div>
        </div>
        <div class="data-card">
            <div class="data-card-header">
                <h5><i class="bi bi-building"></i> Penerbit</h5>
            </div>
            <div class="data-card-body" style="padding:20px;">
                <div class="stat-value" id="totalPenerbit" style="font-size:2rem;">–</div>
                <div class="stat-label">Total Penerbit Terdaftar</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const [bukus, kategoris, peminjaman, penulis, penerbits, dikembalikan] = await Promise.all([
            Api.get('/bukus?per_page=1'),
            Api.get('/kategoris?per_page=1'),
            Api.get('/peminjaman?status=dipinjam&per_page=5'),
            Api.get('/penulis?per_page=1'),
            Api.get('/penerbits?per_page=1'),
            Api.get('/peminjaman?status=dikembalikan&per_page=1'),
        ]);

        document.getElementById('totalBuku').textContent = bukus.pagination.total;
        document.getElementById('totalKategori').textContent = kategoris.pagination.total;
        document.getElementById('totalPinjam').textContent = peminjaman.pagination.total;
        document.getElementById('totalPenulis').textContent = penulis.pagination.total;
        document.getElementById('totalPenerbit').textContent = penerbits.pagination.total;
        document.getElementById('totalKembali').textContent = dikembalikan.pagination.total;

        // Recent peminjaman
        const tbody = document.getElementById('recentPeminjaman');
        if (peminjaman.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center" style="padding:30px;color:var(--text-muted);"><i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>Belum ada peminjaman</td></tr>';
        } else {
            tbody.innerHTML = peminjaman.data.map(p => `
                <tr>
                    <td><strong>${p.nama_peminjam}</strong><br><small style="color:var(--text-muted)">NIS: ${p.nis}</small></td>
                    <td>${p.buku?.judul || '-'}</td>
                    <td>${p.tanggal_pinjam}</td>
                    <td><span class="badge-status ${p.status}">${p.status}</span></td>
                </tr>
            `).join('');
        }
    } catch (e) {
        console.error('Dashboard load error:', e);
        Toast.error('Gagal memuat data dashboard');
    }
});
</script>
@endsection
