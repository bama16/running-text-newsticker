# 📰 Berita API + Frontend (Laravel)

Aplikasi sederhana untuk **menginput, melihat, mengedit, dan menghapus berita** —
tersedia lewat **halaman web** (mudah dipakai) dan **REST API** (untuk integrasi).

- **Backend**: Laravel 10/11
- **Frontend**: Blade + Bootstrap 5 (tanpa npm)
- **Database**: MySQL (atau SQLite)

---

## ✅ Prasyarat

Pastikan sudah terpasang:

| Tools     | Versi minimal |
|-----------|---------------|
| PHP       | 8.1+          |
| Composer  | 2.x           |
| MySQL     | 5.7+ / 8.x    |
| Git       | (opsional)    |

---

## 🚀 Instalasi (Langkah Cepat)

```bash
# 1. Masuk folder project
cd berita-api

# 2. Install dependency
composer install

# 3. Copy file environment
cp .env.example .env

# 4. Generate app key
php artisan key:generate
```

### Konfigurasi Database

Edit file `.env`, sesuaikan bagian ini:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=berita_api
DB_USERNAME=root
DB_PASSWORD=
```

> Buat dulu database `berita_api` di MySQL/phpMyAdmin.

### Migrasi & (Opsional) Seed

```bash
# Buat tabel
php artisan migrate

# (Opsional) isi 5 data contoh
php artisan db:seed --class=BeritaSeeder
```

### Jalankan Server

```bash
php artisan serve
```

Buka browser:

👉 **http://127.0.0.1:8000/berita**

---

## 🖥️ Cara Pakai Halaman Web

Halaman utama: `/berita`

### 1. Tambah **satu** berita

1. Pada tab **"Satu Berita"**
2. Tulis berita di textarea
3. Isi **Kategori** & **Sumber** (opsional)
4. Klik **Simpan** ✅

### 2. Tambah **banyak** berita sekaligus

1. Pindah ke tab **"Banyak (Multi-baris)"**
2. Tempel teks — **1 baris = 1 berita**

   Contoh:
   ```
   Kepadatan kendaraan terjadi di Tol Dalam Kota arah Semanggi pagi ini.
   BMKG mengingatkan potensi hujan disertai angin kencang di Jabodetabek.
   Harga cabai rawit merah di pasar tradisional Jakarta naik tipis.
   ```
3. Isi Kategori & Sumber (opsional)
4. Klik **Simpan Semua** ✅
5. Muncul notifikasi: **"3 berita tersimpan"**

### 3. Lihat & cari berita

- Semua berita tampil di tabel
- **Cari konten** → ketik di kolom "Cari konten..."
- **Filter kategori** → ketik di kolom "Kategori"
- Klik **Reset** untuk bersihkan filter
- Klik **Muat Ulang** untuk refresh manual

### 4. Edit berita

1. Klik ikon ✏️ pada baris yang ingin diubah
2. Modal edit terbuka
3. Ubah teks → klik **Simpan Perubahan** ✅

### 5. Hapus berita

1. Klik ikon 🗑️ pada baris
2. Konfirmasi → data terhapus ✅

---

## 🔌 Cara Pakai API (untuk Developer)

Base URL: `http://127.0.0.1:8000/api`

### Daftar Endpoint

| Method | Endpoint               | Fungsi                          |
|--------|------------------------|---------------------------------|
| GET    | `/beritas`             | List semua berita (array objek) |
| GET    | `/beritas?simple=true` | List (array string saja)        |
| GET    | `/beritas?q=hujan`     | Cari konten                     |
| GET    | `/beritas?kategori=x`  | Filter kategori                 |
| GET    | `/beritas/{id}`        | Detail 1 berita                 |
| POST   | `/beritas`             | Buat 1 berita                   |
| POST   | `/beritas/bulk`        | Buat banyak (multi-baris)       |
| PUT    | `/beritas/{id}`        | Update berita                   |
| DELETE | `/beritas/{id}`        | Hapus berita                    |

### Contoh cURL

**Tambah satu berita:**

```bash
curl -X POST http://127.0.0.1:8000/api/beritas \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"konten":"Berita pertama.","kategori":"jakarta"}'
```

**Tambah banyak sekaligus:**

```bash
curl -X POST http://127.0.0.1:8000/api/beritas/bulk \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"teks":"Baris satu.\nBaris dua.\nBaris tiga."}'
```

**Ambil semua berita (array):**

```bash
curl -H "Accept: application/json" http://127.0.0.1:8000/api/beritas
```

**Ambil hanya teks (array string):**

```bash
curl -H "Accept: application/json" "http://127.0.0.1:8000/api/beritas?simple=true"
```

**Update:**

```bash
curl -X PUT http://127.0.0.1:8000/api/beritas/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"konten":"Teks baru."}'
```

**Hapus:**

```bash
curl -X DELETE http://127.0.0.1:8000/api/beritas/1 \
  -H "Accept: application/json"
```

---

## 📂 Struktur Penting

```
app/
├── Http/
│   ├── Controllers/Api/BeritaController.php   ← logika CRUD
│   ├── Requests/                               ← validasi
│   └── Resources/BeritaResource.php            ← format response
└── Models/Berita.php                           ← model

database/migrations/*_create_beritas_table.php   ← skema tabel

routes/
├── api.php    ← endpoint /api/beritas
└── web.php    ← halaman /berita

resources/views/berita/index.blade.php          ← tampilan frontend
```

---

## 🧪 Menjalankan Test (opsional)

```bash
php artisan test
```

---

## ❗ Troubleshooting

| Masalah | Solusi |
|---------|--------|
| `419 Page Expired` | Pastikan pakai header `X-CSRF-TOKEN` (sudah otomatis di frontend) |
| `SQLSTATE[HY000] [1049]` | Database `berita_api` belum dibuat |
| Halaman kosong / API error | Cek `storage/logs/laravel.log`, jalankan `php artisan config:clear` |
| Perubahan `.env` tidak terbaca | `php artisan config:clear && php artisan cache:clear` |
| Tabel belum ada | `php artisan migrate` |
| Route `/api/*` 404 | Jalankan `php artisan install:api` (Laravel 11) |

---

## 📝 Lisensi

Bebas dipakai untuk keperluan belajar & internal.