# Fitur Scraping Artikel dari URL

Fitur ini memungkinkan admin untuk mengambil data artikel dari website lain secara otomatis.

## Fitur-Fitur

### Backend (Admin Panel)
- ✅ Input URL artikel dari website lain
- ✅ Tombol "Ambil Data" untuk fetch otomatis
- ✅ Preview data sebelum disimpan
- ✅ Modifikasi data jika diperlukan
- ✅ Checkbox untuk menandai artikel eksternal

### Data yang Diambil
1. **Judul** - Dari `og:title`, `<title>`, atau `<h1>`
2. **Deskripsi** - Dari `og:description`, `meta description`, atau paragraf pertama
3. **Thumbnail** - Dari `og:image`, `twitter:image`, atau gambar pertama di konten

### Frontend (Halaman Artikel)
- ✅ Menampilkan artikel dengan thumbnail
- ✅ Card design yang menarik dengan hover effect
- ✅ Tombol "Baca Selengkapnya" aktif ke link asli
- ✅ Menampilkan sumber website di bawah tombol
- ✅ Responsive grid layout

---

## Panduan Setup

### 1. Update Database

Jalankan query SQL di bawah untuk menambah kolom:

```sql
ALTER TABLE artikel ADD COLUMN IF NOT EXISTS url_sumber VARCHAR(255) DEFAULT NULL;
ALTER TABLE artikel ADD COLUMN IF NOT EXISTS thumbnail VARCHAR(255) DEFAULT NULL;
ALTER TABLE artikel ADD COLUMN IF NOT EXISTS is_eksternal BOOLEAN DEFAULT FALSE;
```

Atau jalankan file migration:
```bash
mysql -u root ayam_ketawa < database_migration.sql
```

### 2. File-File yang Ditambah/Diubah

**File Baru:**
- `admin/ArtikelScraper.php` - Class untuk scraping URL

**File yang Diubah:**
- `admin/tambah_artikel.php` - Form dengan fitur fetch URL
- `pages/artikel.php` - Tampilan artikel dengan thumbnail
- `assets/css/style.css` - Styling baru untuk artikel card

---

## Cara Menggunakan

### Admin Panel - Tambah Artikel dari URL

1. Buka halaman "Tambah Artikel" di admin panel
2. Di bagian "Atau Ambil dari URL Artikel Lain":
   - Masukkan URL artikel
   - Klik tombol "Ambil Data"
3. Sistem akan menampilkan preview data (judul, deskripsi, thumbnail)
4. Modifikasi data jika diperlukan
5. Klik "Simpan" untuk menyimpan ke database

### Frontend - Menampilkan Artikel

Halaman artikel akan menampilkan:
- Daftar artikel dalam grid layout
- Thumbnail pada setiap artikel
- Kategori sebagai badge
- Preview teks (160 karakter)
- Tombol "Baca Selengkapnya" yang mengarah ke link asli jika artikel eksternal

---

## Struktur Database

```sql
CREATE TABLE artikel (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(255) NOT NULL,
    isi LONGTEXT NOT NULL,
    kategori VARCHAR(100),
    url_sumber VARCHAR(255) DEFAULT NULL,           /* URL sumber artikel */
    thumbnail VARCHAR(255) DEFAULT NULL,            /* URL gambar thumbnail */
    is_eksternal BOOLEAN DEFAULT FALSE,             /* Flag: artikel eksternal atau lokal */
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## Contoh Class ArtikelScraper

Library yang digunakan: **PHP DOMDocument** (built-in, tidak perlu install)

### Method Utama:

**`ArtikelScraper::scrapeArticle($url)`**
- Input: URL artikel
- Output: Array dengan keys [judul, deskripsi, thumbnail, url_sumber] atau false jika gagal

### Private Methods:

- `extractTitle()` - Ambil judul dengan hierarchi: og:title > title tag > h1
- `extractDescription()` - Ambil deskripsi dengan hierarchi: og:description > meta description > paragraf pertama
- `extractThumbnail()` - Ambil gambar dengan hierarchi: og:image > twitter:image > gambar pertama
- `normalizeUrl()` - Handle relative URLs menjadi absolute URLs

---

## Error Handling

Fitur ini menangani berbagai error:

✅ URL tidak valid
✅ Website tidak accessible/timeout (10 detik)
✅ Meta tags tidak ditemukan (fallback)
✅ Relative URLs pada gambar
✅ Character encoding UTF-8

---

## Limitasi & Note

1. **Timeout 10 detik** - Jika website lambat, scraping gagal
2. **Hanya text & image** - Tidak scrape video atau content dinamis
3. **Relative URL** - Otomatis convert ke absolute URL
4. **Meta data** - Tergantung pada website yang di-scrape
5. **No Cache** - Setiap kali fetch dilakukan request baru ke website

---

## Tips Menggunakan

### Website yang Baik di-Scrape
- ✅ Website dengan meta tags lengkap (og:title, og:description, og:image)
- ✅ Website statis atau server-rendered
- ✅ Website dengan struktur HTML yang rapi

### Website yang Sulit di-Scrape
- ❌ Website dinamis (React, Vue) tanpa server-side rendering
- ❌ Website tanpa meta tags
- ❌ Website dengan CORS protection
- ❌ Website yang memerlukan JavaScript untuk load konten

---

## Troubleshooting

### Scraping gagal (Error: "Gagal mengambil data dari URL")

**Solusi:**
1. Pastikan URL valid dan dapat diakses
2. Coba akses URL langsung di browser
3. Cek apakah website memblokir automated requests
4. Lihat console browser untuk error detil
5. Cek file extensions dan encoding

### Thumbnail tidak muncul

**Solusi:**
1. Pastikan URL gambar valid (buka di browser)
2. Cek CORS jika gambar dari domain berbeda
3. Gunakan HTTPS jika website menggunakan HTTPS
4. Fallback ke gambar placeholder jika diperlukan

### Deskripsi tidak lengkap/salah

**Solusi:**
1. Edit manual di form sebelum simpan
2. Gunakan artikel dengan meta description lengkap
3. Gunakan website yang memiliki struktur konten jelas

---

## Keamanan

⚠️ **Important Notes:**

1. **Validasi URL** - Input di-validate dengan FILTER_VALIDATE_URL
2. **Timeout** - Request timeout 10 detik untuk prevent hanging
3. **XSS Prevention** - Output di-escape dengan htmlspecialchars()
4. **SQL Injection** - Menggunakan prepared statement
5. **Rate Limiting** - Tidak ada, pastikan tambahkan jika diperlukan untuk production

---

## Support & Debug

Untuk debug, uncomment di `ArtikelScraper.php`:

```php
// Uncoment untuk debug
// echo '<pre>';
// var_dump($data);
// echo '</pre>';
```

---

**Dibuat: 2026 | Version: 1.0**
