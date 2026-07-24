/**
 * Perpustakaan Digital - Main JavaScript
 * API Client, Toast, Modal & Pagination Utilities
 */

const API_BASE = '/api/v1';

/* ============ API Client ============ */
const Api = {
    async request(method, endpoint, body = null) {
        const opts = {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        };
        if (body) opts.body = JSON.stringify(body);
        const res = await fetch(`${API_BASE}${endpoint}`, opts);
        const data = await res.json();
        if (!res.ok) throw { status: res.status, ...data };
        return data;
    },
    get(endpoint) { return this.request('GET', endpoint); },
    post(endpoint, body) { return this.request('POST', endpoint, body); },
    put(endpoint, body) { return this.request('PUT', endpoint, body); },
    patch(endpoint, body) { return this.request('PATCH', endpoint, body); },
    delete(endpoint) { return this.request('DELETE', endpoint); },
};

/* ============ Toast Notifications (Ultra Modern 2026) ============ */
const Toast = {
    container: null,
    init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        }
    },
    show(message, type = 'success', duration = 3500) {
        this.init();
        const icons = {
            success: 'bi-check-lg',
            error: 'bi-x-lg',
            warning: 'bi-exclamation-lg',
            info: 'bi-info-lg'
        };
        const toast = document.createElement('div');
        toast.className = `toast-item ${type}`;
        toast.innerHTML = `
            <div class="toast-body">
                <div class="toast-icon">
                    <i class="bi ${icons[type] || 'bi-info-lg'}"></i>
                </div>
                <span>${message}</span>
            </div>
            <button class="toast-close" title="Tutup">&times;</button>
            <div class="toast-progress">
                <div class="toast-progress-bar" style="animation-duration: ${duration}ms;"></div>
            </div>
        `;

        this.container.appendChild(toast);

        const timer = setTimeout(() => {
            if (toast.parentNode) {
                toast.style.animation = 'toastFadeOut 0.35s ease forwards';
                setTimeout(() => toast.remove(), 350);
            }
        }, duration);

        toast.querySelector('.toast-close').addEventListener('click', () => {
            clearTimeout(timer);
            toast.style.animation = 'toastFadeOut 0.25s ease forwards';
            setTimeout(() => toast.remove(), 250);
        });
    },
    success(msg, duration) { this.show(msg, 'success', duration); },
    error(msg, duration) { this.show(msg, 'error', duration); },
    warning(msg, duration) { this.show(msg, 'warning', duration); },
    info(msg, duration) { this.show(msg, 'info', duration); },
};

/* ============ Pagination Renderer ============ */
function renderPagination(pagination, containerId) {
    if (typeof pagination === 'string' || (pagination && pagination.nodeType)) {
        const temp = pagination;
        pagination = containerId;
        containerId = temp;
    }
    if (!pagination) return '';
    const current_page = pagination.current_page || 1;
    const last_page = pagination.last_page || 1;
    const total = pagination.total || 0;
    const per_page = pagination.per_page || 10;
    const from = total > 0 ? (current_page - 1) * per_page + 1 : 0;
    const to = Math.min(current_page * per_page, total);

    let html = `<div class="pagination-wrapper">`;
    html += `<div class="pagination-info">Menampilkan <strong>${from}–${to}</strong> dari <strong>${total}</strong> data</div>`;
    html += `<div class="pagination-btns">`;

    html += `<button class="page-btn" data-page="${current_page - 1}" ${current_page <= 1 ? 'disabled' : ''}><i class="bi bi-chevron-left"></i></button>`;

    let startPage = Math.max(1, current_page - 2);
    let endPage = Math.min(last_page, current_page + 2);

    if (startPage > 1) {
        html += `<button class="page-btn" data-page="1">1</button>`;
        if (startPage > 2) html += `<button class="page-btn" disabled>…</button>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<button class="page-btn ${i === current_page ? 'active' : ''}" data-page="${i}">${i}</button>`;
    }

    if (endPage < last_page) {
        if (endPage < last_page - 1) html += `<button class="page-btn" disabled>…</button>`;
        html += `<button class="page-btn" data-page="${last_page}">${last_page}</button>`;
    }

    html += `<button class="page-btn" data-page="${current_page + 1}" ${current_page >= last_page ? 'disabled' : ''}><i class="bi bi-chevron-right"></i></button>`;
    html += `</div></div>`;

    if (containerId) {
        const el = typeof containerId === 'string' ? document.getElementById(containerId) : containerId;
        if (el) el.innerHTML = html;
    }

    return html;
}

function bindPagination(container, callback) {
    const el = typeof container === 'string' ? document.getElementById(container) : container;
    if (!el) return;
    el.querySelectorAll('.page-btn[data-page]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!btn.disabled && btn.dataset.page) callback(parseInt(btn.dataset.page));
        });
    });
}

/* ============ Search Debounce ============ */
function debounce(fn, delay = 400) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

/* ============ Confirm Action Modal (Bootstrap Modal 2026) ============ */
let confirmModalInstance = null;
let confirmResolve = null;

function initConfirmModal() {
    if (document.getElementById('customConfirmModal')) return;

    const modalHtml = `
    <div class="modal fade" id="customConfirmModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center p-3" id="confirmModalCard" style="background: var(--bg-card); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 18px; box-shadow: 0 12px 45px rgba(0,0,0,0.7);">
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <div id="confirmModalIconBg" style="width: 58px; height: 58px; background: rgba(239, 68, 68, 0.15); color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 1.75rem; box-shadow: 0 0 20px rgba(239, 68, 68, 0.2);">
                            <i id="confirmModalIcon" class="bi bi-trash3-fill"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2 text-white" id="confirmModalTitle" style="font-size: 1.1rem;">Konfirmasi Hapus</h5>
                    <p class="text-secondary mb-4" id="confirmModalText" style="font-size: 0.84rem; color: #9394a5; line-height: 1.4;">Apakah Anda yakin ingin menghapus data ini?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal" style="border-radius: 8px; font-size:0.85rem;">Batal</button>
                        <button type="button" class="btn btn-danger btn-sm px-3" id="confirmModalActionBtn" style="border-radius: 8px; font-size:0.85rem;">Ya, Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modalEl = document.getElementById('customConfirmModal');
    confirmModalInstance = new bootstrap.Modal(modalEl);

    document.getElementById('confirmModalActionBtn').addEventListener('click', () => {
        if (confirmResolve) {
            const resolve = confirmResolve;
            confirmResolve = null;
            confirmModalInstance.hide();
            resolve(true);
        }
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        if (confirmResolve) {
            const resolve = confirmResolve;
            confirmResolve = null;
            resolve(false);
        }
    });
}

function confirmAction(options = {}) {
    initConfirmModal();

    const title = options.title || 'Konfirmasi';
    const text = options.text || 'Apakah Anda yakin?';
    const confirmText = options.confirmText || 'Ya, Lanjutkan';
    const type = options.type || 'danger';

    const titleEl = document.getElementById('confirmModalTitle');
    const textEl = document.getElementById('confirmModalText');
    const iconBgEl = document.getElementById('confirmModalIconBg');
    const iconEl = document.getElementById('confirmModalIcon');
    const btnEl = document.getElementById('confirmModalActionBtn');
    const cardEl = document.getElementById('confirmModalCard');

    titleEl.innerText = title;
    textEl.innerText = text;
    btnEl.innerText = confirmText;

    if (type === 'success') {
        cardEl.style.borderColor = 'rgba(16, 185, 129, 0.4)';
        iconBgEl.style.background = 'rgba(16, 185, 129, 0.15)';
        iconBgEl.style.color = '#10b981';
        iconBgEl.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.25)';
        iconEl.className = options.icon || 'bi bi-arrow-return-left';
        btnEl.className = 'btn btn-success btn-sm px-3';
        btnEl.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        btnEl.style.border = 'none';
        btnEl.style.boxShadow = '0 4px 15px rgba(16, 185, 129, 0.4)';
    } else {
        cardEl.style.borderColor = 'rgba(239, 68, 68, 0.4)';
        iconBgEl.style.background = 'rgba(239, 68, 68, 0.15)';
        iconBgEl.style.color = '#ef4444';
        iconBgEl.style.boxShadow = '0 0 20px rgba(239, 68, 68, 0.25)';
        iconEl.className = options.icon || 'bi bi-trash3-fill';
        btnEl.className = 'btn btn-danger btn-sm px-3';
        btnEl.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
        btnEl.style.border = 'none';
        btnEl.style.boxShadow = '0 4px 15px rgba(239, 68, 68, 0.4)';
    }

    return new Promise((resolve) => {
        confirmResolve = async (confirmed) => {
            if (confirmed && typeof options.callback === 'function') {
                await options.callback();
            }
            resolve(confirmed);
        };
        confirmModalInstance.show();
    });
}

function confirmDelete(param, callback) {
    let cb = typeof param === 'function' ? param : callback;
    let message = typeof param === 'string' ? param : 'Apakah Anda yakin ingin menghapus data ini? Data yang terhapus tidak bisa dikembalikan.';
    return confirmAction({
        title: 'Konfirmasi Hapus',
        text: message,
        confirmText: 'Ya, Hapus',
        type: 'danger',
        icon: 'bi bi-trash3-fill',
        callback: cb
    });
}

function confirmKembalikan(param, callback) {
    let cb = typeof param === 'function' ? param : callback;
    let message = typeof param === 'string' ? param : 'Apakah Anda yakin ingin mengembalikan buku ini?';
    return confirmAction({
        title: 'Konfirmasi Pengembalian',
        text: message,
        confirmText: 'Ya, Kembalikan',
        type: 'success',
        icon: 'bi bi-arrow-return-left',
        callback: cb
    });
}

/* ============ Form Helpers ============ */
function getFormData(formId) {
    const form = document.getElementById(formId);
    if (!form) return {};
    const data = {};
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        if (input.name) {
            let val = input.value.trim();
            if (input.type === 'number' || input.dataset.type === 'integer') {
                val = val !== '' ? parseInt(val) : null;
            }
            if (val !== '' && val !== null) data[input.name] = val;
        }
    });
    return data;
}

function resetForm(formId) {
    const form = document.getElementById(formId);
    if (form) form.reset();
}

function showValidationErrors(errors, formId) {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

    if (!errors) return;

    Object.entries(errors).forEach(([field, messages]) => {
        const input = document.querySelector(`${formId ? '#' + formId + ' ' : ''}[name="${field}"]`);
        if (input) {
            input.classList.add('is-invalid');
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.style.color = '#ef4444';
            feedback.style.fontSize = '0.78rem';
            feedback.style.marginTop = '4px';
            feedback.textContent = Array.isArray(messages) ? messages[0] : messages;
            if (input.parentNode) {
                input.parentNode.appendChild(feedback);
            }
        }
    });
}

/* ============ Sidebar Toggle ============ */
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
});

/* ============ Loading Skeleton ============ */
function showTableLoading(tbodyId, cols = 5) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    let html = '';
    for (let i = 0; i < 5; i++) {
        html += '<tr>';
        for (let j = 0; j < cols; j++) {
            const w = 40 + Math.random() * 50;
            html += `<td><div class="skeleton" style="width:${w}%;height:14px;"></div></td>`;
        }
        html += '</tr>';
    }
    tbody.innerHTML = html;
}
