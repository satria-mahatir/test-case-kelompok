@extends('layouts.app')

@section('title', 'Peminjaman & Pengembalian')

@section('page-title')
<span>Peminjaman</span>
@endsection

@section('content')
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card amber">
            <div class="icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="info">
                <h6>Total Dipinjam</h6>
                <h3 id="statDipinjam">-</h3>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card emerald">
            <div class="icon"><i class="bi bi-check-circle"></i></div>
            <div class="info">
                <h6>Total Dikembalikan</h6>
                <h3 id="statDikembalikan">-</h3>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card rose">
            <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="info">
                <h6>Total Terlambat</h6>
                <h3 id="statTerlambat">-</h3>
            </div>
        </div>
    </div>
</div>

<div class="data-card">
    <div class="data-card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Data Peminjaman</h5>
        <div class="d-flex gap-2 align-items-center">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="form-control-custom" placeholder="Cari peminjam...">
            </div>
            <select id="statusFilter" class="form-select-custom w-auto">
                <option value="">Semua Status</option>
                <option value="dipinjam">Dipinjam</option>
                <option value="dikembalikan">Dikembalikan</option>
                <option value="terlambat">Terlambat</option>
            </select>
            <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#peminjamanModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah
            </button>
        </div>
    </div>
    <div class="data-card-body">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Peminjam</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Rencana Kembali</th>
                        <th>Tgl Dikembalikan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="peminjamanTbody">
                    <!-- Data will be loaded here -->
                </tbody>
            </table>
        </div>
        <div id="paginationContainer" class="pagination-wrapper mt-3"></div>
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="peminjamanModal" tabindex="-1" aria-labelledby="peminjamanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="peminjamanModalLabel">Tambah Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="peminjamanForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-custom" for="buku_id">Buku</label>
                        <select class="form-select-custom" id="buku_id" name="buku_id" required>
                            <option value="">Pilih Buku...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom" for="nama_peminjam">Nama Peminjam</label>
                        <input type="text" class="form-control-custom" id="nama_peminjam" name="nama_peminjam" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom" for="nis">NIS</label>
                        <input type="text" class="form-control-custom" id="nis" name="nis" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom" for="tanggal_pinjam">Tanggal Pinjam</label>
                        <input type="date" class="form-control-custom" id="tanggal_pinjam" name="tanggal_pinjam" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom" for="tanggal_kembali_rencana">Tanggal Rencana Kembali</label>
                        <input type="date" class="form-control-custom" id="tanggal_kembali_rencana" name="tanggal_kembali_rencana" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-accent" id="saveBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let currentPage = 1;
    let currentSearch = '';
    let currentStatus = '';

    const peminjamanModalElement = document.getElementById('peminjamanModal');
    let peminjamanModal;
    if (peminjamanModalElement) {
        peminjamanModal = new bootstrap.Modal(peminjamanModalElement);
    }
    const form = document.getElementById('peminjamanForm');
    const tbody = document.getElementById('peminjamanTbody');
    const paginationContainer = document.getElementById('paginationContainer');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');

    function setDateDefaults() {
        const today = new Date();
        const dateStr = today.toISOString().split('T')[0];
        document.getElementById('tanggal_pinjam').value = dateStr;
        
        const nextWeek = new Date(today);
        nextWeek.setDate(nextWeek.getDate() + 7);
        const nextWeekStr = nextWeek.toISOString().split('T')[0];
        document.getElementById('tanggal_kembali_rencana').value = nextWeekStr;
    }

    async function loadBooks() {
        try {
            const res = await Api.get('/bukus?per_page=100');
            const select = document.getElementById('buku_id');
            select.innerHTML = '<option value="">Pilih Buku...</option>';
            if(res.success && res.data) {
                res.data.forEach(b => {
                    select.innerHTML += `<option value="${b.id}">${b.judul} (Stok: ${b.stok})</option>`;
                });
            }
        } catch (error) {
            console.error('Failed to load books', error);
        }
    }

    async function loadStats() {
        try {
            const dipinjam = await Api.get('/peminjaman?status=dipinjam&per_page=1');
            if (dipinjam.success) document.getElementById('statDipinjam').textContent = dipinjam.pagination.total;
            
            const dikembalikan = await Api.get('/peminjaman?status=dikembalikan&per_page=1');
            if (dikembalikan.success) document.getElementById('statDikembalikan').textContent = dikembalikan.pagination.total;
            
            const terlambat = await Api.get('/peminjaman?status=terlambat&per_page=1');
            if (terlambat.success) document.getElementById('statTerlambat').textContent = terlambat.pagination.total;
        } catch (error) {
            console.error('Failed to load stats', error);
        }
    }

    async function loadData() {
        showTableLoading('peminjamanTbody', 8);
        try {
            const res = await Api.get(`/peminjaman?page=${currentPage}&search=${encodeURIComponent(currentSearch)}&status=${encodeURIComponent(currentStatus)}`);
            if (res.success) {
                tbody.innerHTML = '';
                if (res.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state">Tidak ada data peminjaman</div></td></tr>`;
                    paginationContainer.innerHTML = '';
                    return;
                }

                let startIndex = (res.pagination.current_page - 1) * res.pagination.per_page + 1;
                res.data.forEach((item, index) => {
                    let aksiHtml = '';
                    if (item.status === 'dipinjam' || item.status === 'terlambat') {
                        aksiHtml += `<button class="btn-sm-action text-primary me-1" onclick="kembalikanBuku(${item.id})" title="Kembalikan"><i class="bi bi-box-arrow-in-left"></i></button>`;
                    }
                    aksiHtml += `<button class="btn-sm-action text-danger" onclick="hapusPeminjaman(${item.id})" title="Hapus"><i class="bi bi-trash"></i></button>`;

                    const tglKembali = item.tanggal_pengembalian ? item.tanggal_pengembalian : '-';

                    tbody.innerHTML += `
                        <tr>
                            <td>${startIndex + index}</td>
                            <td>
                                <div class="fw-bold">${item.nama_peminjam}</div>
                                <small class="text-muted">${item.nis}</small>
                            </td>
                            <td>${item.buku ? item.buku.judul : '-'}</td>
                            <td>${item.tanggal_pinjam}</td>
                            <td>${item.tanggal_kembali_rencana}</td>
                            <td>${tglKembali}</td>
                            <td><span class="badge-status ${item.status}">${item.status.toUpperCase()}</span></td>
                            <td>${aksiHtml}</td>
                        </tr>
                    `;
                });

                renderPagination(paginationContainer, res.pagination);
                bindPagination(paginationContainer, (page) => {
                    currentPage = page;
                    loadData();
                });
            }
        } catch (error) {
            Toast.error('Gagal memuat data');
            tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state">Gagal memuat data</div></td></tr>`;
        }
    }

    window.kembalikanBuku = async function(id) {
        const confirmed = await confirmKembalikan('Apakah Anda yakin ingin mengembalikan buku ini?');
        if (confirmed) {
            try {
                const res = await Api.patch(`/peminjaman/${id}/kembalikan`);
                if (res.success) {
                    Toast.success('Buku berhasil dikembalikan');
                    loadData();
                    loadStats();
                }
            } catch (error) {
                Toast.error(error.message || 'Gagal mengembalikan buku');
            }
        }
    };

    window.hapusPeminjaman = async function(id) {
        if (await confirmDelete()) {
            try {
                const res = await Api.delete(`/peminjaman/${id}`);
                if (res.success) {
                    Toast.success('Data berhasil dihapus');
                    if (tbody.children.length === 1 && currentPage > 1) currentPage--;
                    loadData();
                    loadStats();
                }
            } catch (error) {
                Toast.error('Gagal menghapus data');
            }
        }
    };

    if (searchInput) {
        searchInput.addEventListener('input', debounce((e) => {
            currentSearch = e.target.value;
            currentPage = 1;
            loadData();
        }, 500));
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', (e) => {
            currentStatus = e.target.value;
            currentPage = 1;
            loadData();
        });
    }

    if (peminjamanModalElement) {
        peminjamanModalElement.addEventListener('show.bs.modal', () => {
            resetForm('peminjamanForm');
            setDateDefaults();
        });
    }

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = getFormData('peminjamanForm');
            const btn = document.getElementById('saveBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
            btn.disabled = true;

            try {
                const res = await Api.post('/peminjaman', data);
                if (res.success) {
                    Toast.success('Data berhasil disimpan');
                    if (peminjamanModal) peminjamanModal.hide();
                    loadData();
                    loadStats();
                    loadBooks();
                }
            } catch (error) {
                if (error.status === 422) {
                    if (error.errors) {
                        showValidationErrors(error.errors, 'peminjamanForm');
                    }
                    if (error.message && error.message.toLowerCase().includes('stok')) {
                        Toast.error(error.message);
                    }
                } else {
                    Toast.error(error.message || 'Terjadi kesalahan');
                }
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    }

    loadBooks();
    loadStats();
    loadData();
});
</script>
@endpush
