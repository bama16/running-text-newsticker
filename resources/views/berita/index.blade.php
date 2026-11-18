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
    </style>
</head>
<body>
<div class="container py-4">

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
                {{-- Single --}}
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

                {{-- Bulk --}}
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
/* ============ Konfigurasi ============ */
const API = '/api/beritas';
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const HEADERS_JSON = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrf,
};

/* ============ Helper ============ */
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
    return new Date(iso).toLocaleString('id-ID', {
        dateStyle: 'medium', timeStyle: 'short'
    });
}

/* ============ READ (list) ============ */
async function loadBerita() {
    const q = document.getElementById('filterQ').value.trim();
    const kategori = document.getElementById('filterKategori').value.trim();

    const params = new URLSearchParams();
    if (q) params.set('q', q);
    if (kategori) params.set('kategori', kategori);

    const url = params.toString() ? `${API}?${params}` : API;

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

        // Bind event edit & delete
        tbody.querySelectorAll('.btn-edit').forEach(btn =>
            btn.addEventListener('click', () => openEdit(btn.dataset.id, data)));
        tbody.querySelectorAll('.btn-delete').forEach(btn =>
            btn.addEventListener('click', () => hapusBerita(btn.dataset.id)));
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Gagal memuat data.</td></tr>';
        toast('Gagal memuat data', false);
    }
}

/* ============ CREATE — single ============ */
document.getElementById('formSingle').addEventListener('submit', async (e) => {
    e.preventDefault();
    const f = e.target;
    const payload = {
        konten: f.konten.value.trim(),
        kategori: f.kategori.value.trim() || null,
        sumber: f.sumber.value.trim() || null,
    };
    if (!payload.konten) return toast('Konten wajib diisi', false);

    const res = await fetch(API, {
        method: 'POST', headers: HEADERS_JSON, body: JSON.stringify(payload)
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

/* ============ CREATE — bulk ============ */
document.getElementById('formBulk').addEventListener('submit', async (e) => {
    e.preventDefault();
    const f = e.target;
    const payload = {
        teks: f.teks.value,
        kategori: f.kategori.value.trim() || null,
        sumber: f.sumber.value.trim() || null,
    };
    if (!payload.teks.trim()) return toast('Teks wajib diisi', false);

    const res = await fetch(`${API}/bulk`, {
        method: 'POST', headers: HEADERS_JSON, body: JSON.stringify(payload)
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

/* ============ UPDATE ============ */
let currentList = [];

function openEdit(id, list) {
    currentList = list;
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

    const res = await fetch(`${API}/${id}`, {
        method: 'PUT', headers: HEADERS_JSON, body: JSON.stringify(payload)
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

/* ============ DELETE ============ */
async function hapusBerita(id) {
    if (!confirm('Yakin hapus berita ini?')) return;

    const res = await fetch(`${API}/${id}`, {
        method: 'DELETE', headers: HEADERS_JSON
    });
    const json = await res.json();

    if (res.ok && json.success) {
        toast('Berita dihapus');
        loadBerita();
    } else {
        toast(json.message || 'Gagal menghapus', false);
    }
}

/* ============ Filter ============ */
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

/* ============ Init ============ */
loadBerita();
</script>
</body>
</html>