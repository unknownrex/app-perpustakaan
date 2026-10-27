# Skenario Demo — app-perpustakaan

Dokumen ini adalah urutan demo. Ikuti dari atas ke bawah; tiap langkah sudah menyebutkan apa yang diklik, apa yang harus muncul, dan poin apa yang perlu dijelaskan.

---

## Requirement Sebelum Demo

- [ ] **Dua server jalan bersamaan** (lihat [Menjalankan dua server](#menjalankan-dua-server)). Kalau hanya satu, Dashboard dan Laporan akan tampil 0/kosong.
- [ ] Database baru di-seed: `php artisan migrate:fresh --seed`
- [ ] Browser sudah login? **Belum** — kita mulai dari halaman login di depan penguji.
- [ ] Postman terbuka dengan 2 request tersimpan: `GET /api/books` dan `GET /api/stats`
- [ ] Kredensial dihafal (tidak perlu buka `.env` atau database saat demo)

### Kredensial

| Email | Password | Role |
|---|---|---|
| `admin@pens.ac.id` | `password` | admin |
| `petugas1@pens.ac.id` | `password` | petugas |
| `petugas2@pens.ac.id` | `password` | petugas |

### Menjalankan dua server

`php artisan serve` memproses satu request pada satu waktu. Dashboard dan Laporan memanggil API aplikasi sendiri lewat HTTP, jadi butuh server kedua di port berbeda supaya tidak saling mengunci (*deadlock*). Di produksi (Nginx + PHP-FPM) masalah ini tidak muncul.

```bash
# Terminal 1 - trafik browser
php artisan serve --port=8000

# Terminal 2 - khusus dipanggil aplikasi sendiri lewat INTERNAL_API_URL
php artisan serve --port=8011
```

Buka browser di `http://127.0.0.1:8000`.

---

## Skenario Demo

### 1. Login sebagai admin

- **Aksi:** buka `http://127.0.0.1:8000/login`, masuk dengan `admin@pens.ac.id` / `password`.
- **Harus muncul:** redirect ke Dashboard (`/`), flash message "Login berhasil, selamat datang Bambang Sutrisno.", dan navbar kanan atas menampilkan `Bambang Sutrisno (Admin)`.
- **Poin penjelasan:** autentikasi berbasis session (`Auth::attempt()`) + `session()->regenerate()` untuk mencegah session fixation. Route CRUD dilindungi middleware `auth`.

### 2. Tampilkan dashboard statistik

- **Aksi:** halaman `/` (sudah otomatis setelah login).
- **Harus muncul:** tiga kartu — Total Buku `20`, Total Anggota `15`, Peminjaman Aktif (jumlah loan berstatus `dipinjam`, berubah-ubah sesuai hasil seeding).
- **Poin penjelasan:** angka ini **bukan** query Eloquent langsung. `DashboardController@index` memanggil `Http::get(config('services.internal_api.base_url').'/api/stats')`, lalu `$response->json()` dioper ke Blade. Ada `try/catch ConnectionException` sehingga kalau server `:8011` mati, halaman tetap tampil dengan angka 0 (bukan error 500).

### 3. Tambah kategori baru

- **Aksi:** buka `/categories` → isi form tambah (contoh: `Teknologi Informasi`) → Simpan.
- **Harus muncul:** baris baru di tabel kategori + alert hijau "Kategori berhasil ditambahkan."
- **Poin penjelasan:** route ini ada di grup middleware `admin` (`CheckAdminRole`). Login sebagai petugas akan ditolak di sini — bisa ditunjukkan di langkah opsional.

### 4. Tambah buku dengan kategori

- **Aksi:** buka `/books` → "+ Tambah Buku" → isi judul, penulis, penerbit, tahun, ISBN, stok, pilih kategori **Teknologi Informasi** yang baru dibuat di langkah 3 → Simpan.
- **Harus muncul:** buku baru di index dengan kolom kategori berisi nama kategori (bukan angka `category_id`).
- **Poin penjelasan:** dropdown kategori diisi dari `Category::all()`, dan nama kategori tampil di index karena relasi `belongsTo` di-eager-load (`with('category')`) — bukan N+1.

### 5. Tambah anggota baru

- **Aksi:** buka `/members` → "+ Tambah Anggota" → isi nama, NIM, email kampus, telepon, alamat → Simpan. Lalu ulangi submit dengan **NIM/email yang sama**.
- **Harus muncul:** data pertama tersimpan; percobaan duplikat ditolak dengan pesan validasi `unique` di bawah field, data tidak masuk.
- **Poin penjelasan:** validasi `unique` di server tetap wajib meskipun form HTML punya `required`/`type=email`, karena request bisa dikirim tanpa lewat form.
- **Bonus:** tunjukkan kolom pencarian di atas tabel (`?search=`) memfilter berdasarkan nama anggota.

### 6. Buat transaksi peminjaman

- **Aksi:** buka `/loans` → "+ Tambah Peminjaman" → pilih anggota (termasuk yang baru dibuat), pilih satu/lebih buku (termasuk buku baru), isi tanggal pinjam & tanggal kembali → Simpan.
- **Harus muncul:** redirect ke `/loans` dengan alert sukses; baris baru berstatus `Dipinjam`.
- **Poin penjelasan:** form **tidak punya input petugas**. `user_id` diisi otomatis dari `auth()->id()` — petugas yang sedang login. Satu transaksi bisa memuat banyak buku lewat tabel pivot `loan_items`.

### 7. Lihat daftar peminjaman aktif

- **Aksi:** tetap di `/loans`.
- **Harus muncul:** transaksi baru di paling atas, kolom Anggota/Petugas/Buku berisi **nama**, bukan ID mentah; status berwarna (badge kuning `Dipinjam`, hijau `Dikembalikan`, merah `Terlambat`).
- **Poin penjelasan:** `Loan::with(['member', 'user', 'loanItems.book'])->paginate(10)` — eager loading mencegah N+1 Query Problem. Pagination memakai view custom (`vendor/pagination/custom.blade.php`) karena project tidak memakai Tailwind.

### 8. Proses pengembalian buku

- **Aksi:** pada baris yang baru dibuat (status `Dipinjam`), klik **Kembalikan** → konfirmasi.
- **Harus muncul:** alert sukses, status berubah jadi `Dikembalikan` hijau, tombol Kembalikan hilang dari baris itu.
- **Poin penjelasan:** route `PUT /loans/{id}/kembalikan` (`LoanController@kembalikan`) mengisi `status = 'dikembalikan'` dan `tanggal_dikembalikan = now()`. Alternatif lewat API: `PUT /api/loans/{id}/kembalikan` di Postman.

### 9. Tampilkan halaman laporan

- **Aksi:** klik menu **Laporan** (atau buka `/loans/report`).
- **Harus muncul:** tabel 10 transaksi lengkap dengan anggota, petugas, daftar buku, tanggal, dan status — termasuk transaksi yang baru dibuat/dikembalikan di langkah 6–8.
- **Poin penjelasan:** data halaman ini berasal dari `GET /api/loans` (dipanggil `Http::get()`), bukan query Eloquent langsung. Bedanya dengan `/loans`: hasilnya array JSON, jadi diakses `$loan['member']['nama']`, dan paginasi dibangun manual dari `meta.current_page`/`meta.last_page`.

### 10. Demo API di Postman

- **Aksi:** jalankan `GET http://127.0.0.1:8000/api/books` (header `Accept: application/json`).
- **Harus muncul:** JSON `{"data": [...], "links": {...}, "meta": {...}}`, tiap item berformat `BookResource` (field `category` sebagai objek bersarang), status `200`.
- **Aksi:** jalankan `GET http://127.0.0.1:8000/api/stats`.
- **Harus muncul:** `{"total_buku": ..., "total_anggota": ..., "peminjaman_aktif": ...}` sesuai data terkini.
- **Poin penjelasan:** API Resource memisahkan bentuk respons dari struktur tabel, sehingga perubahan skema tidak langsung merusak kontrak ke klien. API ini sengaja tanpa token (di luar cakupan materi). Detail endpoint lain ada di `API.md`.

### 11. Tanya jawab konseptual

Dosen memilih 3–5 pertanyaan dari daftar di modul Pertemuan 11. Tidak perlu dibaca ulang di sini — cukup pastikan bisa menjelaskan **kenapa**, bukan cuma definisi.
