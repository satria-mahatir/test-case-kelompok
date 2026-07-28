@extends('layouts.app')

@section('title', 'Peminjaman Buku')

@section('page-title')
<span>Peminjaman Buku</span>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-title">Sedang Dipinjam</div>
            <div class="stat-card-value text-primary" id="statDipinjam">0</div>
            <div class="stat-card-icon"><i class="bi bi-book text-primary"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-title">Dikembalikan</div>
            <div class="stat-card-value text-success" id="statDikembalikan">0</div>
            <div class="stat-card-icon"><i class="bi bi-check-circle text-success"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-title">Terlambat</div>
            <div class="stat-card-value text-danger" id="statTerlambat">0</div>
            <div class="stat-card-icon"><i class="bi bi-exclamation-triangle text-danger"></i></div>
        </div>
    </div>
</div>

<div class="data-card">
    <div class="data-card-header flex-wrap gap-2">
        <h5 class="mb-0">Daftar Peminjaman</h5>
        <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Cari peminjam, NIS...">
            </div>
            <select class="form-select-custom" id="statusFilter" style="width: auto;">
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
                    <!-- Data loaded via JS -->
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
                        <label class="form-label-custom" for="user_id">Akun Peminjam (User App Mobile)</label>
                        <select class="form-select-custom" id="user_id" name="user_id">
                            <option value="">-- Pilih Akun Peminjam (Opsional) --</option>
                        </select>
                        <small class="text-muted" style="font-size:0.75rem;">Pilih akun terdaftar atau isi nama & NIS manual di bawah.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom" for="buku_id">Buku <span class="text-danger">*</span></label>
                        <select class="form-select-custom" id="buku_id" name="buku_id" required>
                            <option value="">Pilih Buku...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom" for="nama_peminjam">Nama Peminjam <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-custom" id="nama_peminjam" name="nama_peminjam" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom" for="nis">NIS / Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-custom" id="nis" name="nis" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom" for="tanggal_pinjam">Tanggal Pinjam <span class="text-danger">*</span></label>
                        <input type="date" class="form-control-custom" id="tanggal_pinjam" name="tanggal_pinjam" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom" for="tanggal_kembali_rencana">Tanggal Rencana Kembali <span class="text-danger">*</span></label>
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
let usersMap = {};

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

    async function loadUsers() {
        try {
            const res = await Api.get('/users?per_page=100');
            const select = document.getElementById('user_id');
            select.innerHTML = '<option value="">-- Pilih Akun Peminjam (Opsional) --</option>';
            usersMap = {};
            if(res.success && res.data) {
                res.data.forEach(u => {
                    usersMap[u.id] = u;
                    select.innerHTML += `<option value="${u.id}">${u.name} (@${u.username}) [${u.role.toUpperCase()}]</option>`;
                });
            }
        } catch (error) {
            console.error('Failed to load users', error);
        }
    }

    document.getElementById('user_id').addEventListener('change', function() {
        const selectedId = this.value;
        if (selectedId && usersMap[selectedId]) {
            document.getElementById('nama_peminjam').value = usersMap[selectedId].name;
            document.getElementById('nis').value = usersMap[selectedId].username;
        }
    });

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
                    const userBadge = item.user 
                        ? `<span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-30 ms-1 px-1 py-0.5" style="font-size:0.68rem;">@${item.user.username}</span>` 
                        : '';

                    tbody.innerHTML += `
                        <tr>
                            <td>${startIndex + index}</td>
                            <td>
                                <div class="fw-bold">${item.nama_peminjam} ${userBadge}</div>
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

                renderPagination(res.pagination, 'paginationContainer');
                bindPagination('paginationContainer', (page) => {
                    currentPage = page;
                    loadData();
                });
            }
        } catch (error) {
            Toast.error('Gagal memuat data peminjaman');
        }
    }

    if (peminjamanModalElement) {
        peminjamanModalElement.addEventListener('show.bs.modal', () => {
            setDateDefaults();
            loadBooks();
            loadUsers();
        });
    }

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('saveBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            const payload = {
                buku_id: document.getElementById('buku_id').value,
                user_id: document.getElementById('user_id').value || null,
                nama_peminjam: document.getElementById('nama_peminjam').value,
                nis: document.getElementById('nis').value,
                tanggal_pinjam: document.getElementById('tanggal_pinjam').value,
                tanggal_kembali_rencana: document.getElementById('tanggal_kembali_rencana').value,
            };

            try {
                const res = await Api.post('/peminjaman', payload);
                if (res.success) {
                    Toast.success('Peminjaman berhasil ditambahkan');
                    peminjamanModal.hide();
                    form.reset();
                    loadData();
                    loadStats();
                }
            } catch (error) {
                Toast.error(error.message || 'Gagal menambahkan peminjaman');
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Simpan';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', debounce((e) => {
            currentSearch = e.target.value;
            currentPage = 1;
            loadData();
        }, 400));
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', (e) => {
            currentStatus = e.target.value;
            currentPage = 1;
            loadData();
        });
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
                Toast.error(error.message || 'Gagal menghapus data');
            }
        }
    };

    loadData();
    loadStats();
});
</script>
@endpush
