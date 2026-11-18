<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Berita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f5f6fa; }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .table td, .table th { vertical-align: middle; }
        .konten-cell { max-width: 480px; white-space: normal; }
        .toast-container { z-index: 1090; }
        .endpoint-btn { font-size: .8rem; }
        .endpoint-btn code { font-size: .75rem; color: inherit; }
        .config-panel { background: linear-gradient(135deg,#eef3ff,#f8fbff); }
    </style>
</head>
<body>
<div class="container py-4">

    {{-- ===== PANEL KONFIGURASI URL & PORT ===== --}}
    <div class="card mb-4 config-panel">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-gear"></i> Konfigurasi API Endpoint
        </div>
        <div class="card-body">
            <div class="row g-2 align-items-end mb-3">
                <div class="col-md-5">
                    <label class="form-label mb-1 small text-muted">Base URL</label>
                    <input type="text" id="cfgBaseUrl" class="form-control form-control-sm"
                           placeholder="http://127.0.0.1" value="http://127.0.0.1">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1 small text-muted">Port</label>
                    <input type="number" id="cfgPort" class="form-control form-control-sm"
                           placeholder="8000" value="8000" min="1" max="65535">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1 small text-muted">Prefix API</label>
                    <input type="text" id="cfgPrefix" class="form-control form-control-sm"
                           placeholder="/api/beritas" value="/api/beritas">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary btn-sm w-100" id="btnApplyCfg">
                        <i class="bi bi-check2-circle"></i> Terapkan
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" id="btnResetCfg" title="Reset ke default">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </div>

            <div class="mb-2">
                <span class="small text-muted">Preview endpoint:</span>
                <code id="previewEndpoint" class="ms-1">http://127.0.0.1:8000/api/beritas</code>
            </div>

            <hr class="my-3">

            <div class="mb-2 fw-semibold small text-uppercase text-secondary">
                <i class="bi bi-link-45deg"></i> Buka Endpoint GET
            </div>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <button class="btn btn-outline-primary btn-sm endpoint-btn" data-open="/" title="GET semua berita (array objek)">
                    <i class="bi bi-box-arrow-up-right"></i> GET <code>/beritas</code>
                </button>
                <button class="btn btn-outline-primary btn-sm endpoint-btn" data-open="/?simple=true" title="GET array string saja">
                    <i class="bi bi-box-arrow-up-right"></i> GET <code>/beritas?simple=true</code>
                </button>
                <button class="btn btn-outline-info btn-sm endpoint-btn" id="btnOpenSearch" title="GET dengan pencarian q">
                    <i class="bi bi-search"></i> GET <code>/beritas?q=...</code>
                </button>
                <button class="btn btn-outline-info btn-sm endpoint-btn" id="btnOpenKategori" title="GET filter kategori">
                    <i class="bi bi-funnel"></i> GET <code>/beritas?kategori=...</code>
                </button>
                <button class="btn btn-outline-secondary btn-sm endpoint-btn" id="btnOpenDetail" title="GET detail by id">
                    <i class="bi bi-file-earmark-text"></i> GET <code>/beritas/{id}</code>
                </button>
            </div>

            <div class="mb-2 fw-semibold small text-uppercase text-secondary">
                <i class="bi bi-lightning-charge"></i> Aksi Cepat
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-success btn-sm endpoint-btn" id="btnQuickBulk">
                    <i class="bi bi-plus-square"></i> POST <code>/beritas/bulk</code> (contoh teks)
                </button>
                <button class="btn btn-warning btn-sm endpoint-btn" id="btnOpenSwaggerLike">
                    <i class="bi bi-code-slash"></i> Lihat JSON mentah
                </button>
                <button class="btn btn-outline-dark btn-sm endpoint-btn" id="btnCopyEndpoint">
                    <i class="bi bi-clipboard"></i> Salin Base Endpoint
                </button>
            </div>
        </div>
    </div>

    {{-- ===== HEADER ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-newspaper"></i> Manajemen Berita</h3>
        <span class="badge bg-primary" id="totalBadge">0 berita</span>
    </div>

    {{-- ===== FORM CREATE ===== --}}
    <div class="card mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-plus-circle"></i> Tambah Berita
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="tabCreate" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabSingle" type="button">
                        <i class="bi bi-pencil"></i> Satu Berita
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabBulk" type="button">
                        <i class="bi bi-list-ul"></i> Banyak (Multi-baris)
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="tabSingle">
                    <form id="formSingle">
                        <div class="mb-2">
                            <textarea class="form-control" name="konten" rows="3"
                                placeholder="Tulis satu berita di sini..." required></textarea>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="kategori" placeholder="Kategori (opsional)">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="sumber" placeholder="Sumber (opsional)">
                            </div>
                        </div>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </form>
                </div>

                <div class="tab-pane fade" id="tabBulk">
                    <form id="formBulk">
                        <div class="mb-2">
                            <textarea class="form-control" name="teks" rows="6"
placeholder="Tempel banyak berita di sini, satu berita per baris...
Kepadatan kendaraan terjadi di Tol Dalam Kota arah Semanggi pagi ini.
BMKG mengingatkan potensi hujan disertai angin kencang di Jabodetabek.
Harga cabai rawit merah di pasar tradisional Jakarta naik tipis." required></textarea>
                            <small class="text-muted">Setiap baris baru akan menjadi satu berita.</small>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="kategori" placeholder="Kategori (opsional)">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="sumber" placeholder="Sumber (opsional)">
                            </div>
                        </div>
                        <button class="btn btn-success" type="submit">
                            <i class="bi bi-save-all"></i> Simpan Semua
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FILTER + LIST ===== --}}
    <div class="card">
        <div class="card-header bg-white d-flex flex-wrap gap-2 align-items-center">
            <span class="fw-semibold me-2"><i class="bi bi-table"></i> Daftar Berita</span>
            <input id="filterQ" type="text" class="form-control form-control-sm w-auto" placeholder="Cari konten...">
            <input id="filterKategori" type="text" class="form-control form-control-sm w-auto" placeholder="Kategori">
            <button class="btn btn-sm btn-outline-secondary" id="btnResetFilter">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
            <button class="btn btn-sm btn-outline-primary ms-auto" id="btnReload">
                <i class="bi bi-arrow-clockwise"></i> Muat Ulang
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Konten</th>
                        <th style="width:120px">Kategori</th>
                        <th style="width:120px">Sumber</th>
                        <th style="width:160px">Dibuat</th>
                        <th style="width:130px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyBerita">
                    <tr><td colspan="6" class="text-center text-muted py-4">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== MODAL EDIT ===== --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEdit">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Berita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="mb-2">
                        <label class="form-label">Konten</label>
                        <textarea class="form-control" name="konten" rows="4" required></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <input type="text" class="form-control" name="kategori">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sumber</label>
                            <input type="text" class="form-control" name="sumber">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== TOAST ===== --}}
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="toast" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastBody">OK</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* =====================================================
   KONFIGURASI API DINAMIS (URL + PORT + PREFIX)
   ===================================================== */
const CFG_KEY = 'berita_api_cfg';
const CFG_DEFAULT = {
    baseUrl: window.location.origin.split(':').slice(0, 2).join(':') || 'http://127.0.0.1',
    port: window.location.port || '8000',
    prefix: '/api/beritas',
};

function loadCfg() {
    try {
        const saved = JSON.parse(localStorage.getItem(CFG_KEY) || 'null');
        return saved ? { ...CFG_DEFAULT, ...saved } : { ...CFG_DEFAULT };
    } catch { return { ...CFG_DEFAULT }; }
}

let CFG = loadCfg();

function applyCfgToForm() {
    document.getElementById('cfgBaseUrl').value = CFG.baseUrl;
    document.getElementById('cfgPort').value = CFG.port;
    document.getElementById('cfgPrefix').value = CFG.prefix;
    updatePreview();
}

function updatePreview() {
    document.getElementById('previewEndpoint').textContent = buildUrl();
}

/** Bangun full URL: BASE : PORT + PREFIX + path */
function buildUrl(path = '') {
    const base = (CFG.baseUrl || '').replace(/\/+$/, '');
    const port = CFG.port ? `:${CFG.port}` : '';
    const prefix = CFG.prefix.startsWith('/') ? CFG.prefix : '/' + CFG.prefix;
    return `${base}${port}${prefix}${path}`;
}

document.getElementById('btnApplyCfg').addEventListener('click', () => {
    CFG.baseUrl = document.getElementById('cfgBaseUrl').value.trim() || CFG_DEFAULT.baseUrl;
    CFG.port = document.getElementById('cfgPort').value.trim();
    CFG.prefix = document.getElementById('cfgPrefix').value.trim() || '/api/beritas';
    localStorage.setItem(CFG_KEY, JSON.stringify(CFG));
    updatePreview();
    toast('Konfigurasi diterapkan');
    loadBerita();
});

document.getElementById('btnResetCfg').addEventListener('click', () => {
    CFG = { ...CFG_DEFAULT };
    localStorage.removeItem(CFG_KEY);
    applyCfgToForm();
    toast('Konfigurasi direset');
    loadBerita();
});

['cfgBaseUrl', 'cfgPort', 'cfgPrefix'].forEach(id => {
    document.getElementById(id).addEventListener('input', () => {
        CFG.baseUrl = document.getElementById('cfgBaseUrl').value.trim();
        CFG.port = document.getElementById('cfgPort').value.trim();
        CFG.prefix = document.getElementById('cfgPrefix').value.trim();
        updatePreview();
    });
});

/* =====================================================
   TOMBOL BUKA ENDPOINT GET & AKSI CEPAT
   ===================================================== */
// GET statis: /, /?simple=true
document.querySelectorAll('[data-open]').forEach(btn => {
    btn.addEventListener('click', () => {
        window.open(buildUrl(btn.dataset.open), '_blank');
    });
});

// GET /beritas?q=...
document.getElementById('btnOpenSearch').addEventListener('click', () => {
    const q = prompt('Kata kunci pencarian (q):', 'hujan');
    if (q === null) return;
    const path = `/?q=${encodeURIComponent(q)}`;
    window.open(buildUrl(path), '_blank');
});

// GET /beritas?kategori=...
document.getElementById('btnOpenKategori').addEventListener('click', () => {
    const k = prompt('Nama kategori:', 'jakarta');
    if (k === null) return;
    const path = `/?kategori=${encodeURIComponent(k)}`;
    window.open(buildUrl(path), '_blank');
});

// GET /beritas/{id}
document.getElementById('btnOpenDetail').addEventListener('click', () => {
    const id = prompt('ID berita:', '1');
    if (id === null || id.trim() === '') return;
    window.open(buildUrl(`/${encodeURIComponent(id.trim())}`), '_blank');
});

// POST /beritas/bulk — kirim contoh teks (buka di tab baru lewat fetch, lalu tampilkan JSON)
document.getElementById('btnQuickBulk').addEventListener('click', async () => {
    const teks = "Contoh berita baris 1 dari tombol cepat.\nContoh berita baris 2 dari tombol cepat.\nContoh berita baris 3 dari tombol cepat.";
    if (!confirm('Kirim 3 contoh berita ke endpoint POST /beritas/bulk?')) return;

    try {
        const res = await fetch(buildUrl('/bulk'), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ teks, kategori: 'contoh', sumber: 'quick-action' })
        });
        const json = await res.json();
        if (res.ok && json.success) {
            toast(`${json.total} berita terkirim`);
            loadBerita();
        } else {
            toast(json.message || 'Gagal kirim bulk', false);
        }
    } catch (e) {
        toast('Gagal konek ke API', false);
    }
});

// Lihat JSON mentah (tab baru)
document.getElementById('btnOpenSwaggerLike').addEventListener('click', () => {
    window.open(buildUrl('/?simple=false'), '_blank');
});

// Salin base endpoint ke clipboard
document.getElementById('btnCopyEndpoint').addEventListener('click', async () => {
    const url = buildUrl();
    try {
        await navigator.clipboard.writeText(url);
        toast('Endpoint disalin: ' + url);
    } catch {
        toast('Gagal menyalin', false);
    }
});

/* =====================================================
   CRUD — pakai buildUrl() agar mengikuti konfigurasi
   ===================================================== */
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const HEADERS_JSON = () => ({
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrf,
});

function toast(msg, ok = true) {
    const el = document.getElementById('toast');
    el.classList.toggle('bg-success', ok);
    el.classList.toggle('bg-danger', !ok);
    document.getElementById('toastBody').textContent = msg;
    bootstrap.Toast.getOrCreateInstance(el, { delay: 2500 }).show();
}

function escapeHtml(s) {
    return (s ?? '').toString()
        .replaceAll('&', '&amp;').replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;').replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function fmtTanggal(iso) {
    if (!iso) return '-';
    return new Date(iso).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
}

/* ===== READ ===== */
async function loadBerita() {
    const q = document.getElementById('filterQ').value.trim();
    const kategori = document.getElementById('filterKategori').value.trim();

    const params = new URLSearchParams();
    if (q) params.set('q', q);
    if (kategori) params.set('kategori', kategori);

    const url = buildUrl(params.toString() ? `/?${params}` : '/');

    const tbody = document.getElementById('tbodyBerita');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Memuat...</td></tr>';

    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        const data = json.data ?? [];

        document.getElementById('totalBadge').textContent = `${data.length} berita`;

        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Belum ada berita.</td></tr>';
            return;
        }

        tbody.innerHTML = data.map((b, i) => `
            <tr>
                <td>${i + 1}</td>
                <td class="konten-cell">${escapeHtml(b.konten)}</td>
                <td>${b.kategori ? `<span class="badge bg-info text-dark">${escapeHtml(b.kategori)}</span>` : '-'}</td>
                <td>${b.sumber ? escapeHtml(b.sumber) : '-'}</td>
                <td><small class="text-muted">${fmtTanggal(b.created_at)}</small></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary btn-edit" data-id="${b.id}" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${b.id}" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        tbody.querySelectorAll('.btn-edit').forEach(btn =>
            btn.addEventListener('click', () => openEdit(btn.dataset.id, data)));
        tbody.querySelectorAll('.btn-delete').forEach(btn =>
            btn.addEventListener('click', () => hapusBerita(btn.dataset.id)));
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Gagal memuat data.</td></tr>';
        toast('Gagal memuat data', false);
    }
}

/* ===== CREATE single ===== */
document.getElementById('formSingle').addEventListener('submit', async (e) => {
    e.preventDefault();
    const f = e.target;
    const payload = {
        konten: f.konten.value.trim(),
        kategori: f.kategori.value.trim() || null,
        sumber: f.sumber.value.trim() || null,
    };
    if (!payload.konten) return toast('Konten wajib diisi', false);

    const res = await fetch(buildUrl('/'), {
        method: 'POST', headers: HEADERS_JSON(), body: JSON.stringify(payload)
    });
    const json = await res.json();

    if (res.ok && json.success) {
        toast('Berita tersimpan');
        f.reset();
        loadBerita();
    } else {
        toast(json.message || 'Gagal menyimpan', false);
    }
});

/* ===== CREATE bulk ===== */
document.getElementById('formBulk').addEventListener('submit', async (e) => {
    e.preventDefault();
    const f = e.target;
    const payload = {
        teks: f.teks.value,
        kategori: f.kategori.value.trim() || null,
        sumber: f.sumber.value.trim() || null,
    };
    if (!payload.teks.trim()) return toast('Teks wajib diisi', false);

    const res = await fetch(buildUrl('/bulk'), {
        method: 'POST', headers: HEADERS_JSON(), body: JSON.stringify(payload)
    });
    const json = await res.json();

    if (res.ok && json.success) {
        toast(`${json.total} berita tersimpan`);
        f.reset();
        loadBerita();
    } else {
        toast(json.message || 'Gagal menyimpan bulk', false);
    }
});

/* ===== UPDATE ===== */
function openEdit(id, list) {
    const b = list.find(x => String(x.id) === String(id));
    if (!b) return;

    const form = document.getElementById('formEdit');
    form.id.value = b.id;
    form.konten.value = b.konten ?? '';
    form.kategori.value = b.kategori ?? '';
    form.sumber.value = b.sumber ?? '';

    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEdit')).show();
}

document.getElementById('formEdit').addEventListener('submit', async (e) => {
    e.preventDefault();
    const f = e.target;
    const id = f.id.value;
    const payload = {
        konten: f.konten.value.trim(),
        kategori: f.kategori.value.trim() || null,
        sumber: f.sumber.value.trim() || null,
    };
    if (!payload.konten) return toast('Konten wajib diisi', false);

    const res = await fetch(buildUrl(`/${id}`), {
        method: 'PUT', headers: HEADERS_JSON(), body: JSON.stringify(payload)
    });
    const json = await res.json();

    if (res.ok && json.success) {
        toast('Berita diperbarui');
        bootstrap.Modal.getInstance(document.getElementById('modalEdit')).hide();
        loadBerita();
    } else {
        toast(json.message || 'Gagal memperbarui', false);
    }
});

/* ===== DELETE ===== */
async function hapusBerita(id) {
    if (!confirm('Yakin hapus berita ini?')) return;

    const res = await fetch(buildUrl(`/${id}`), {
        method: 'DELETE', headers: HEADERS_JSON()
    });
    const json = await res.json();

    if (res.ok && json.success) {
        toast('Berita dihapus');
        loadBerita();
    } else {
        toast(json.message || 'Gagal menghapus', false);
    }
}

/* ===== Filter ===== */
let debounce;
document.getElementById('filterQ').addEventListener('input', () => {
    clearTimeout(debounce);
    debounce = setTimeout(loadBerita, 350);
});
document.getElementById('filterKategori').addEventListener('input', () => {
    clearTimeout(debounce);
    debounce = setTimeout(loadBerita, 350);
});
document.getElementById('btnResetFilter').addEventListener('click', () => {
    document.getElementById('filterQ').value = '';
    document.getElementById('filterKategori').value = '';
    loadBerita();
});
document.getElementById('btnReload').addEventListener('click', loadBerita);

/* ===== Init ===== */
applyCfgToForm();
loadBerita();
</script>
</body>
</html>