@extends('layouts.app')

@section('title', 'Data Penerbit')

@section('page-title')
<span>Data Penerbit</span>
@endsection

@section('content')

<div class="data-card mb-4">
    <div class="data-card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="mb-0 text-white"><i class="bi bi-building me-2"></i>Data Penerbit</h5>
        <div class="d-flex gap-2 align-items-center">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="form-control form-control-custom" placeholder="Cari penerbit...">
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
                        <th>Nama Penerbit</th>
                        <th>Kota</th>
                        <th>Dibuat</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="penerbitTbody">
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
<div class="modal fade" id="penerbitModal" tabindex="-1" aria-labelledby="penerbitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content data-card border-0">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-white" id="penerbitModalLabel">Tambah Penerbit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="penerbitForm">
                    <input type="hidden" id="penerbitId" name="id">
                    
                    <div class="mb-3">
                        <label for="nama_penerbit" class="form-label form-label-custom">Nama Penerbit</label>
                        <input type="text" class="form-control form-control-custom" id="nama_penerbit" name="nama_penerbit" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="kota" class="form-label form-label-custom">Kota</label>
                        <input type="text" class="form-control form-control-custom" id="kota" name="kota">
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
    const API_ENDPOINT = '/penerbits';
    const modal = new bootstrap.Modal(document.getElementById('penerbitModal'));
    const form = document.getElementById('penerbitForm');
    
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
        showTableLoading('penerbitTbody', 5);
        
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
            document.getElementById('penerbitTbody').innerHTML = `
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
        const tbody = document.getElementById('penerbitTbody');
        
        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-inbox display-4 mb-3 text-secondary"></i>
                            <p class="text-muted">Tidak ada data penerbit ditemukan</p>
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
                    <td>${item.nama_penerbit}</td>
                    <td>${item.kota || '-'}</td>
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
        resetForm('penerbitForm');
        document.getElementById('penerbitId').value = '';
        document.getElementById('penerbitModalLabel').innerText = 'Tambah Penerbit';
        
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        modal.show();
    }
    
    async function editData(id) {
        try {
            const response = await Api.get(`${API_ENDPOINT}/${id}`);
            
            if (response.success || response.data) {
                const data = response.data || response;
                
                resetForm('penerbitForm');
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                
                document.getElementById('penerbitId').value = data.id;
                document.getElementById('nama_penerbit').value = data.nama_penerbit;
                document.getElementById('kota').value = data.kota || '';
                
                document.getElementById('penerbitModalLabel').innerText = 'Edit Penerbit';
                modal.show();
            }
        } catch (error) {
            Toast.error('Gagal mengambil data penerbit');
        }
    }
    
    async function saveData() {
        const id = document.getElementById('penerbitId').value;
        const formData = getFormData('penerbitForm');
        
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
                showValidationErrors(error.errors, 'penerbitForm');
            } else {
                Toast.error(error.message || 'Gagal menyimpan data');
            }
        } finally {
            btnSave.innerHTML = originalText;
            btnSave.disabled = false;
        }
    }
    
    async function deleteData(id) {
        const confirmed = await confirmDelete('Apakah Anda yakin ingin menghapus data penerbit ini?');
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
