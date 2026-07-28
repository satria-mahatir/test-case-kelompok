@extends('layouts.app')

@section('title', 'Kelola Akun Pengguna')

@section('page-title')
<span>Kelola Akun Pengguna</span>
@endsection

@section('content')

<div class="data-card mb-4">
    <div class="data-card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="mb-0 text-white"><i class="bi bi-person-gear me-2"></i>Data Akun Pengguna</h5>
        <div class="d-flex gap-2 align-items-center">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Cari nama atau username...">
            </div>
            <button class="btn btn-accent" onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-1"></i> Tambah Akun
            </button>
        </div>
    </div>
    <div class="data-card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>NAMA LENGKAP</th>
                        <th>USERNAME</th>
                        <th>ROLE / LEVEL</th>
                        <th>TANGGAL DIBUAT</th>
                        <th width="120" class="text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <div class="text-muted mt-2">Memuat data akun...</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="p-3 border-top border-secondary border-opacity-10" id="paginationContainer"></div>
</div>

<!-- Modal Form User (Tambah / Edit) -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px;">
            <div class="modal-header border-bottom border-secondary border-opacity-20 px-4 py-3">
                <h5 class="modal-title text-white fw-bold" id="userModalTitle">Tambah Akun Pengguna</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm">
                <div class="modal-body p-4">
                    <input type="hidden" id="userId">
                    
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-medium mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="name" class="form-control form-control-custom" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-medium mb-1">Username (Untuk Login - Tanpa Gmail) <span class="text-danger">*</span></label>
                        <input type="text" id="username" class="form-control form-control-custom" placeholder="Contoh: peminjam1, petugas1, admin" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-medium mb-1">Password <span id="passwordHelp" class="text-danger">*</span></label>
                        <input type="password" id="password" class="form-control form-control-custom" placeholder="Masukkan password minimal 6 karakter">
                        <small class="text-muted" id="passwordNote" style="font-size: 0.75rem; display: none;">Kosongkan password jika tidak ingin mengubahnya.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-medium mb-1">Role / Level Akses</label>
                        <select id="role" class="form-select form-control-custom">
                            <option value="peminjam">Peminjam (Anggota App Mobile)</option>
                            <option value="petugas">Petugas Perpustakaan (Web)</option>
                            <option value="admin">Administrator (Web & System)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-20 px-4 py-3">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-accent btn-sm px-4" id="btnSaveUser">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let currentPage = 1;
    const API_ENDPOINT = '/users';
    let userModalInstance = null;

    document.addEventListener('DOMContentLoaded', () => {
        userModalInstance = new bootstrap.Modal(document.getElementById('userModal'));
        loadData();

        document.getElementById('searchInput').addEventListener('input', debounce(() => {
            currentPage = 1;
            loadData();
        }, 400));

        document.getElementById('userForm').addEventListener('submit', handleSubmit);
    });

    async function loadData(page = 1) {
        currentPage = page;
        const tbody = document.getElementById('userTableBody');
        const search = document.getElementById('searchInput').value;

        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>`;

        try {
            const response = await Api.get(`${API_ENDPOINT}?page=${page}&search=${encodeURIComponent(search)}`);
            if (response.success && response.data.length > 0) {
                let html = '';
                response.data.forEach((item, index) => {
                    const rowNum = ((response.pagination.current_page - 1) * response.pagination.per_page) + index + 1;
                    
                    let roleBadge = '';
                    if (item.role === 'admin') {
                        roleBadge = `<span class="badge bg-danger bg-opacity-20 text-danger border border-danger border-opacity-30 px-2 py-1" style="font-size:0.72rem;">Admin</span>`;
                    } else if (item.role === 'petugas') {
                        roleBadge = `<span class="badge bg-info bg-opacity-20 text-info border border-info border-opacity-30 px-2 py-1" style="font-size:0.72rem;">Petugas</span>`;
                    } else {
                        roleBadge = `<span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-30 px-2 py-1" style="font-size:0.72rem;">Peminjam</span>`;
                    }
                    
                    html += `
                        <tr>
                            <td>${rowNum}</td>
                            <td class="fw-semibold text-white">${escapeHtml(item.name)}</td>
                            <td><code class="text-accent" style="font-size:0.85rem;">${escapeHtml(item.username)}</code></td>
                            <td>${roleBadge}</td>
                            <td class="text-secondary" style="font-size:0.8rem;">${item.created_at || '-'}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-sm-action btn-outline-warning me-1" onclick="openEditModal(${item.id})" title="Edit Akun">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-sm-action btn-outline-danger" onclick="deleteData(${item.id})" title="Hapus Akun">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
                renderPagination(response.pagination, 'paginationContainer');
                bindPagination('paginationContainer', loadData);
            } else {
                tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state"><i class="bi bi-person-x"></i><p>Tidak ada akun pengguna ditemukan</p></div></td></tr>`;
                document.getElementById('paginationContainer').innerHTML = '';
            }
        } catch (error) {
            console.error(error);
            Toast.error('Gagal memuat data akun');
            tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state"><p>Terjadi kesalahan jaringan</p></div></td></tr>`;
        }
    }

    function openCreateModal() {
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('userModalTitle').innerText = 'Tambah Akun Pengguna';
        document.getElementById('role').value = 'peminjam';
        document.getElementById('passwordHelp').innerText = '*';
        document.getElementById('passwordNote').style.display = 'none';
        document.getElementById('password').required = true;
        userModalInstance.show();
    }

    async function openEditModal(id) {
        try {
            const res = await Api.get(`${API_ENDPOINT}/${id}`);
            if (res.success && res.data) {
                const user = res.data;
                document.getElementById('userId').value = user.id;
                document.getElementById('name').value = user.name;
                document.getElementById('username').value = user.username;
                document.getElementById('password').value = '';
                document.getElementById('passwordHelp').innerText = '';
                document.getElementById('passwordNote').style.display = 'block';
                document.getElementById('password').required = false;
                document.getElementById('role').value = user.role || 'peminjam';

                document.getElementById('userModalTitle').innerText = 'Edit Akun Pengguna';
                userModalInstance.show();
            }
        } catch (error) {
            Toast.error('Gagal mengambil data akun');
        }
    }

    async function handleSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('userId').value;
        const btnSave = document.getElementById('btnSaveUser');
        const originalText = btnSave.innerHTML;

        const payload = {
            name: document.getElementById('name').value,
            username: document.getElementById('username').value,
            role: document.getElementById('role').value,
        };

        const password = document.getElementById('password').value;
        if (password) {
            payload.password = password;
        }

        btnSave.disabled = true;
        btnSave.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...`;

        try {
            const res = id ? await Api.put(`${API_ENDPOINT}/${id}`, payload) : await Api.post(API_ENDPOINT, payload);
            if (res.success) {
                Toast.success(id ? 'Akun berhasil diperbarui' : 'Akun berhasil dibuat');
                userModalInstance.hide();
                loadData(currentPage);
            } else {
                Toast.error(res.message || 'Gagal menyimpan data akun');
            }
        } catch (error) {
            Toast.error(error.message || 'Terjadi kesalahan sistem');
        } finally {
            btnSave.disabled = false;
            btnSave.innerHTML = originalText;
        }
    }

    function deleteData(id) {
        confirmDelete('Apakah Anda yakin ingin menghapus akun pengguna ini?', async () => {
            try {
                const res = await Api.delete(`${API_ENDPOINT}/${id}`);
                if (res.success) {
                    Toast.success('Akun berhasil dihapus');
                    loadData(currentPage);
                } else {
                    Toast.error(res.message || 'Gagal menghapus akun');
                }
            } catch (error) {
                Toast.error(error.message || 'Terjadi kesalahan sistem');
            }
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>"']/g, function(m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
        });
    }
</script>
@endsection
