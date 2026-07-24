@extends('layouts.app')

@section('title', 'Data Penulis')

@section('page-title')
<span>Data Penulis</span>
@endsection

@section('content')

<div class="data-card mb-4">
    <div class="data-card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="mb-0 text-white"><i class="bi bi-person-vcard-fill me-2"></i>Data Penulis</h5>
        <div class="d-flex gap-2 align-items-center">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="form-control form-control-custom" placeholder="Cari penulis...">
            </div>
            <button class="btn btn-accent" onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-1"></i> Tambah
            </button>
        </div>
    </div>
    <div class="data-card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Nama Penulis</th>
                        <th>Email</th>
                        <th>Dibuat</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="penulisTbody">
                    <!-- Data will be loaded via JS -->
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top border-secondary pagination-wrapper">
            <div id="paginationContainer" class="d-flex justify-content-end"></div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="penulisModal" tabindex="-1" aria-labelledby="penulisModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content data-card border-0">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-white" id="penulisModalLabel">Tambah Penulis</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="penulisForm">
                    <input type="hidden" id="penulisId" name="id">
                    
                    <div class="mb-3">
                        <label for="nama_penulis" class="form-label form-label-custom">Nama Penulis</label>
                        <input type="text" class="form-control form-control-custom" id="nama_penulis" name="nama_penulis" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label form-label-custom">Email</label>
                        <input type="email" class="form-control form-control-custom" id="email" name="email">
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-accent" id="btnSave" onclick="saveData()">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const API_ENDPOINT = '/penulis';
    const modal = new bootstrap.Modal(document.getElementById('penulisModal'));
    const form = document.getElementById('penulisForm');
    
    let currentPage = 1;
    let currentSearch = '';
    
    document.addEventListener('DOMContentLoaded', () => {
        loadData();
        
        document.getElementById('searchInput').addEventListener('input', debounce((e) => {
            currentSearch = e.target.value;
            currentPage = 1;
            loadData();
        }, 500));
    });
    
    async function loadData() {
        showTableLoading('penulisTbody', 5);
        
        try {
            const response = await Api.get(`${API_ENDPOINT}?search=${currentSearch}&page=${currentPage}&per_page=10`);
            
            if (response.success) {
                renderTable(response.data);
                if (response.pagination) {
                    renderPagination('paginationContainer', response.pagination);
                    bindPagination('paginationContainer', (page) => {
                        currentPage = page;
                        loadData();
                    });
                }
            }
        } catch (error) {
            Toast.error('Gagal memuat data');
            document.getElementById('penulisTbody').innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger py-4">
                        <div class="empty-state">
                            <i class="bi bi-exclamation-triangle display-4 mb-2"></i>
                            <p>Terjadi kesalahan saat memuat data.</p>
                        </div>
                    </td>
                </tr>
            `;
        }
    }
    
    function renderTable(data) {
        const tbody = document.getElementById('penulisTbody');
        
        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-inbox display-4 mb-3 text-secondary"></i>
                            <p class="text-muted">Tidak ada data penulis ditemukan</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = data.map((item, index) => {
            const rowNumber = (currentPage - 1) * 10 + index + 1;
            const date = new Date(item.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            
            return `
                <tr>
                    <td>${rowNumber}</td>
                    <td>${item.nama_penulis}</td>
                    <td>${item.email || '-'}</td>
                    <td>${date}</td>
                    <td>
                        <div class="d-flex gap-1 justify-content-center">
                            <button class="btn btn-sm btn-sm-action btn-outline-info" onclick="editData(${item.id})" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-sm-action btn-outline-danger" onclick="deleteData(${item.id})" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }
    
    function openCreateModal() {
        resetForm('penulisForm');
        document.getElementById('penulisId').value = '';
        document.getElementById('penulisModalLabel').innerText = 'Tambah Penulis';
        
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        modal.show();
    }
    
    async function editData(id) {
        try {
            const response = await Api.get(`${API_ENDPOINT}/${id}`);
            
            if (response.success || response.data) {
                const data = response.data || response;
                
                resetForm('penulisForm');
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                
                document.getElementById('penulisId').value = data.id;
                document.getElementById('nama_penulis').value = data.nama_penulis;
                document.getElementById('email').value = data.email || '';
                
                document.getElementById('penulisModalLabel').innerText = 'Edit Penulis';
                modal.show();
            }
        } catch (error) {
            Toast.error('Gagal mengambil data penulis');
        }
    }
    
    async function saveData() {
        const id = document.getElementById('penulisId').value;
        const formData = getFormData('penulisForm');
        
        const btnSave = document.getElementById('btnSave');
        const originalText = btnSave.innerHTML;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
        btnSave.disabled = true;
        
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        try {
            let response;
            if (id) {
                response = await Api.put(`${API_ENDPOINT}/${id}`, formData);
            } else {
                response = await Api.post(API_ENDPOINT, formData);
            }
            
            if (response.success || response.data) {
                Toast.success(id ? 'Data berhasil diperbarui' : 'Data berhasil ditambahkan');
                modal.hide();
                loadData();
            }
        } catch (error) {
            if (error.errors) {
                showValidationErrors(error.errors, 'penulisForm');
            } else {
                Toast.error(error.message || 'Gagal menyimpan data');
            }
        } finally {
            btnSave.innerHTML = originalText;
            btnSave.disabled = false;
        }
    }
    
    async function deleteData(id) {
        const confirmed = await confirmDelete('Apakah Anda yakin ingin menghapus data penulis ini?');
        if (!confirmed) return;
        
        try {
            const response = await Api.delete(`${API_ENDPOINT}/${id}`);
            if (response.success || response.message) {
                Toast.success('Data berhasil dihapus');
                loadData();
            }
        } catch (error) {
            Toast.error(error.message || 'Gagal menghapus data');
        }
    }
</script>
@endpush
