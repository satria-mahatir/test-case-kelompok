@extends('layouts.app')

@section('title', 'Data Buku')

@section('page-title')
    <span>Data Buku</span>
@endsection

@section('content')
<div class="data-card">
    <div class="data-card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-journal-richtext me-2"></i>Data Buku</h5>
        <div class="d-flex gap-2">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Cari buku...">
            </div>
            <button class="btn btn-accent" onclick="openCreateModal()">
                <i class="bi bi-plus-lg"></i> Tambah Buku
            </button>
        </div>
    </div>
    <div class="data-card-body">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Judul</th>
                        <th>ISBN</th>
                        <th>Penulis</th>
                        <th>Penerbit</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody id="bukuTbody">
                    <!-- Data will be loaded here -->
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper mt-3" id="paginationWrapper"></div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="bukuModal" tabindex="-1" aria-labelledby="bukuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="bukuForm" onsubmit="saveBuku(event)">
                <div class="modal-header">
                    <h5 class="modal-title" id="bukuModalLabel">Tambah Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="bukuId" name="id">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="judul" class="form-label-custom">Judul Buku</label>
                            <input type="text" class="form-control form-control-custom" id="judul" name="judul">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="kategori_id" class="form-label-custom">Kategori</label>
                            <select class="form-select form-select-custom" id="kategori_id" name="kategori_id">
                                <option value="">Pilih Kategori</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="penulis_id" class="form-label-custom">Penulis</label>
                            <select class="form-select form-select-custom" id="penulis_id" name="penulis_id">
                                <option value="">Pilih Penulis</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="penerbit_id" class="form-label-custom">Penerbit</label>
                            <select class="form-select form-select-custom" id="penerbit_id" name="penerbit_id">
                                <option value="">Pilih Penerbit</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="isbn" class="form-label-custom">ISBN</label>
                            <input type="text" class="form-control form-control-custom" id="isbn" name="isbn">
                        </div>
                        <div class="col-md-4">
                            <label for="tahun_terbit" class="form-label-custom">Tahun Terbit</label>
                            <input type="number" class="form-control form-control-custom" id="tahun_terbit" name="tahun_terbit">
                        </div>
                        <div class="col-md-4">
                            <label for="stok" class="form-label-custom">Stok</label>
                            <input type="number" class="form-control form-control-custom" id="stok" name="stok">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="deskripsi" class="form-label-custom">Deskripsi</label>
                            <textarea class="form-control form-control-custom" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
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
        modalInstance = new bootstrap.Modal(document.getElementById('bukuModal'));
        
        loadData();
        loadSelectOptions();

        document.getElementById('searchInput').addEventListener('input', debounce(function() {
            currentPage = 1;
            loadData();
        }, 500));
    });

    async function loadData(page = 1) {
        currentPage = page;
        const search = document.getElementById('searchInput').value;
        const url = `/bukus?page=${page}&search=${encodeURIComponent(search)}`;
        
        showTableLoading('bukuTbody', 8);

        try {
            const response = await Api.get(url);
            
            if (response.success) {
                renderTable(response.data);
                renderPagination(response.pagination, 'paginationWrapper');
                bindPagination('paginationWrapper', loadData);
            } else {
                Toast.error('Gagal mengambil data buku');
            }
        } catch (error) {
            console.error(error);
            Toast.error('Terjadi kesalahan pada server');
        }
    }

    function renderTable(data) {
        const tbody = document.getElementById('bukuTbody');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center empty-state">Tidak ada data ditemukan</td></tr>`;
            return;
        }

        data.forEach((item, index) => {
            const rowNumber = (currentPage - 1) * 10 + index + 1;
            const stokColor = item.stok > 0 ? 'text-success' : 'text-danger';
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${rowNumber}</td>
                <td>${item.judul}</td>
                <td>${item.isbn || '-'}</td>
                <td>${item.penulis ? item.penulis.nama_penulis : '-'}</td>
                <td>${item.penerbit ? item.penerbit.nama_penerbit : '-'}</td>
                <td>${item.kategori ? item.kategori.nama_kategori : '-'}</td>
                <td class="${stokColor} fw-bold">${item.stok}</td>
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

    async function loadSelectOptions() {
        try {
            const [kategoriRes, penulisRes, penerbitRes] = await Promise.all([
                Api.get('/kategoris?per_page=100'),
                Api.get('/penulis?per_page=100'),
                Api.get('/penerbits?per_page=100')
            ]);

            if (kategoriRes.success) populateSelect('kategori_id', kategoriRes.data, 'id', 'nama_kategori');
            if (penulisRes.success) populateSelect('penulis_id', penulisRes.data, 'id', 'nama_penulis');
            if (penerbitRes.success) populateSelect('penerbit_id', penerbitRes.data, 'id', 'nama_penerbit');
        } catch (error) {
            console.error('Error loading options', error);
        }
    }

    function populateSelect(elementId, data, valueKey, textKey) {
        const select = document.getElementById(elementId);
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueKey];
            option.textContent = item[textKey];
            select.appendChild(option);
        });
    }

    function openCreateModal() {
        resetForm('bukuForm');
        document.getElementById('bukuId').value = '';
        document.getElementById('bukuModalLabel').innerText = 'Tambah Buku';
        
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        
        modalInstance.show();
    }

    function editData(item) {
        resetForm('bukuForm');
        
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        document.getElementById('bukuId').value = item.id;
        document.getElementById('judul').value = item.judul || '';
        document.getElementById('kategori_id').value = item.kategori ? item.kategori.id : (item.kategori_id || '');
        document.getElementById('penulis_id').value = item.penulis ? item.penulis.id : (item.penulis_id || '');
        document.getElementById('penerbit_id').value = item.penerbit ? item.penerbit.id : (item.penerbit_id || '');
        document.getElementById('isbn').value = item.isbn || '';
        document.getElementById('tahun_terbit').value = item.tahun_terbit || '';
        document.getElementById('stok').value = item.stok || '0';
        document.getElementById('deskripsi').value = item.deskripsi || '';

        document.getElementById('bukuModalLabel').innerText = 'Edit Buku';
        modalInstance.show();
    }

    async function saveBuku(event) {
        event.preventDefault();
        
        const form = document.getElementById('bukuForm');
        const data = getFormData('bukuForm');
        const id = document.getElementById('bukuId').value;
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
                response = await Api.put(`/bukus/${id}`, data);
            } else {
                response = await Api.post('/bukus', data);
            }

            if (response.success) {
                Toast.success(`Buku berhasil ${isEdit ? 'diperbarui' : 'ditambahkan'}`);
                modalInstance.hide();
                loadData(currentPage);
            } else {
                if (response.errors) {
                    showValidationErrors(response.errors, 'bukuForm');
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
                const response = await Api.delete(`/bukus/${id}`);
                if (response.success) {
                    Toast.success('Buku berhasil dihapus');
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
