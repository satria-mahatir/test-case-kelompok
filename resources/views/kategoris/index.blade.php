@extends('layouts.app')

@section('title', 'Data Kategori')

@section('page-title')
    <span>Data Kategori</span>
@endsection

@section('content')
<div class="data-card">
    <div class="data-card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-tags me-2"></i>Data Kategori</h5>
        <div class="d-flex gap-2">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Cari kategori...">
            </div>
            <button class="btn btn-accent" onclick="openCreateModal()">
                <i class="bi bi-plus-lg"></i> Tambah Kategori
            </button>
        </div>
    </div>
    <div class="data-card-body">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Nama Kategori</th>
                        <th>Dibuat</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody id="kategoriTbody">
                    <!-- Data will be loaded here -->
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper mt-3" id="paginationWrapper"></div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="kategoriModal" tabindex="-1" aria-labelledby="kategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="kategoriForm" onsubmit="saveKategori(event)">
                <div class="modal-header">
                    <h5 class="modal-title" id="kategoriModalLabel">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="kategoriId" name="id">
                    
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label-custom">Nama Kategori</label>
                        <input type="text" class="form-control form-control-custom" id="nama_kategori" name="nama_kategori">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-accent" id="btnSave">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let modalInstance = null;

    document.addEventListener('DOMContentLoaded', function() {
        modalInstance = new bootstrap.Modal(document.getElementById('kategoriModal'));
        
        loadData();

        document.getElementById('searchInput').addEventListener('input', debounce(function() {
            currentPage = 1;
            loadData();
        }, 500));
    });

    async function loadData(page = 1) {
        currentPage = page;
        const search = document.getElementById('searchInput').value;
        const url = `/kategoris?page=${page}&search=${encodeURIComponent(search)}`;
        
        showTableLoading('kategoriTbody', 4);

        try {
            const response = await Api.get(url);
            
            if (response.success) {
                renderTable(response.data);
                renderPagination(response.pagination, 'paginationWrapper');
                bindPagination('paginationWrapper', loadData);
            } else {
                Toast.error('Gagal mengambil data kategori');
            }
        } catch (error) {
            console.error(error);
            Toast.error('Terjadi kesalahan pada server');
        }
    }

    function renderTable(data) {
        const tbody = document.getElementById('kategoriTbody');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center empty-state">Tidak ada data ditemukan</td></tr>`;
            return;
        }

        data.forEach((item, index) => {
            const rowNumber = (currentPage - 1) * 10 + index + 1;
            const date = new Date(item.created_at).toLocaleDateString('id-ID', {
                year: 'numeric', month: 'long', day: 'numeric'
            });
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${rowNumber}</td>
                <td>${item.nama_kategori}</td>
                <td>${date}</td>
                <td>
                    <button class="btn btn-sm-action btn-warning me-1" onclick='editData(${JSON.stringify(item).replace(/'/g, "&#39;")})' title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm-action btn-danger" onclick="deleteData(${item.id})" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function openCreateModal() {
        resetForm('kategoriForm');
        document.getElementById('kategoriId').value = '';
        document.getElementById('kategoriModalLabel').innerText = 'Tambah Kategori';
        
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        
        modalInstance.show();
    }

    function editData(item) {
        resetForm('kategoriForm');
        
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        document.getElementById('kategoriId').value = item.id;
        document.getElementById('nama_kategori').value = item.nama_kategori || '';

        document.getElementById('kategoriModalLabel').innerText = 'Edit Kategori';
        modalInstance.show();
    }

    async function saveKategori(event) {
        event.preventDefault();
        
        const form = document.getElementById('kategoriForm');
        const data = getFormData('kategoriForm');
        const id = document.getElementById('kategoriId').value;
        const isEdit = id !== '';
        
        const btnSave = document.getElementById('btnSave');
        const originalText = btnSave.innerHTML;
        btnSave.disabled = true;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        try {
            let response;
            if (isEdit) {
                response = await Api.put(`/kategoris/${id}`, data);
            } else {
                response = await Api.post('/kategoris', data);
            }

            if (response.success) {
                Toast.success(`Kategori berhasil ${isEdit ? 'diperbarui' : 'ditambahkan'}`);
                modalInstance.hide();
                loadData(currentPage);
            } else {
                if (response.errors) {
                    showValidationErrors(response.errors, 'kategoriForm');
                    Toast.error('Silakan periksa kembali form anda');
                } else {
                    Toast.error(response.message || 'Gagal menyimpan data');
                }
            }
        } catch (error) {
            console.error(error);
            Toast.error('Terjadi kesalahan sistem');
        } finally {
            btnSave.disabled = false;
            btnSave.innerHTML = originalText;
        }
    }

    function deleteData(id) {
        confirmDelete(async () => {
            try {
                const response = await Api.delete(`/kategoris/${id}`);
                if (response.success) {
                    Toast.success('Kategori berhasil dihapus');
                    loadData(currentPage);
                } else {
                    Toast.error(response.message || 'Gagal menghapus data');
                }
            } catch (error) {
                console.error(error);
                Toast.error('Terjadi kesalahan sistem');
            }
        });
    }
</script>
@endsection
