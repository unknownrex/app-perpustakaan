# app-perpustakaan

Sistem Perpustakaan Digital Kampus — project praktikum Workshop Pemrograman Framework (Laravel 12). Mencakup CRUD lengkap dengan autentikasi berbasis role, REST API, serta halaman Dashboard dan Laporan yang mengonsumsi API aplikasi sendiri lewat Laravel HTTP Client.

## Fitur

- **CRUD Buku** — tambah, lihat, ubah, hapus, plus filter per kategori dan pagination
- **CRUD Kategori** — khusus role `admin`
- **CRUD Anggota** — dengan validasi keunikan NIM dan email, plus pencarian berdasarkan nama
- **Peminjaman** — satu transaksi bisa memuat beberapa buku (`loan_items`), badge status berwarna, dan proses pengembalian
- **Autentikasi & Otorisasi** — login/logout session-based, middleware `auth` untuk seluruh route CRUD, middleware `admin` (`CheckAdminRole`) untuk kategori
- **Profil** — lihat data user dan ganti password
- **REST API** — endpoint JSON untuk `books`, `members`, `loans`, `stats` memakai API Resource (lihat [`API.md`](API.md))
- **Dashboard & Laporan Peminjaman** — data diambil lewat `Http::get()` ke API internal, bukan query Eloquent langsung
- **Seeder & Factory** — data dummy bergaya Indonesia (locale `id_ID`) untuk demo
- **Skenario demo UAS** — lihat [`DEMO.md`](DEMO.md)

## Prasyarat

- PHP >= 8.2 dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`
- Composer
- MySQL atau MariaDB

## Instalasi

```bash
git clone https://github.com/unknownrex/app-perpustakaan.git
cd app-perpustakaan
composer install
```

Salin file environment lalu generate application key:

```bash
# Windows
copy .env.example .env
# Linux / macOS
cp .env.example .env

php artisan key:generate
```

## Konfigurasi `.env`

### Database

Buat database terlebih dahulu, misalnya:

```sql
CREATE DATABASE app_perpustakaan;
```

`.env.example` masih memakai `DB_CONNECTION=sqlite`. Untuk project ini ganti blok database menjadi MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=app_perpustakaan
DB_USERNAME=root
DB_PASSWORD=
```

### Variabel khusus project

```env
# Locale Faker - membuat Seeder/Factory menghasilkan data bergaya Indonesia
APP_FAKER_LOCALE=id_ID

# Base URL server kedua khusus panggilan API internal (wajib beda port dari server utama)
INTERNAL_API_URL=http://127.0.0.1:8011
```

`INTERNAL_API_URL` dibaca lewat `config('services.internal_api.base_url')` di `config/services.php`, dipakai `DashboardController` dan `LoanController@report`.

## Migrasi & Seeder

```bash
php artisan migrate:fresh --seed
```

Perintah ini membuat ulang seluruh tabel lalu mengisi data demo:

| Data | Jumlah |
|---|---|
| User (1 admin, 2 petugas) | 3 |
| Kategori | 5 |
| Buku | 20 |
| Anggota | 15 |
| Peminjaman | 10 |

Urutan seeding diatur di `database/seeders/DatabaseSeeder.php`: `UserSeeder` → `CategorySeeder` → `BookSeeder` → `MemberSeeder` → `LoanSeeder`. Urutan ini penting karena `BookFactory` butuh baris `categories` dan `LoanSeeder` butuh `members` + `users`.

## Menjalankan Aplikasi — WAJIB DUA SERVER

`php artisan serve` (PHP built-in web server) memproses **satu request pada satu waktu**. Dashboard dan Laporan Peminjaman mengirim request HTTP ke API aplikasi sendiri. Kalau request itu diarahkan ke port yang sama, server sibuk menunggu balasan dari dirinya sendiri sehingga terjadi **deadlock**: halaman menggantung sampai timeout.

Karena itu jalankan **dua terminal**:

```bash
# Terminal 1 — melayani trafik browser
php artisan serve --port=8000

# Terminal 2 — khusus menerima panggilan API internal (INTERNAL_API_URL)
php artisan serve --port=8011
```

Buka `http://127.0.0.1:8000`.

> **Peringatan:** kalau hanya satu server yang jalan, halaman **Dashboard** akan menampilkan angka `0` dan halaman **Laporan Peminjaman** tampil kosong. Itu bukan bug aplikasi (ada `try/catch ConnectionException` yang mencegah error 500), melainkan tanda server kedua belum dinyalakan. Di server produksi (Nginx + PHP-FPM atau Laravel Octane) masalah ini tidak muncul karena banyak request diproses bersamaan.

## Akun Demo

| Email | Password | Role |
|---|---|---|
| `admin@pens.ac.id` | `password` | admin |
| `petugas1@pens.ac.id` | `password` | petugas |
| `petugas2@pens.ac.id` | `password` | petugas |

## Halaman Web

| URL | Nama Route | Akses |
|---|---|---|
| `/login` | `login` | publik |
| `/` | `dashboard` | semua user login |
| `/books` | `books.index` | semua user login |
| `/members` | `members.index` | semua user login |
| `/loans` | `loans.index` | semua user login |
| `/loans/report` | `loans.report` | semua user login |
| `/categories` | `categories.index` | admin |
| `/profil` | `profile.show` | semua user login |

## Endpoint API

Base URL: `http://127.0.0.1:8000/api`, header wajib `Accept: application/json`.

| Method | Endpoint |
|---|---|
| GET | `/api/books`, `/api/books/{id}` |
| POST | `/api/books` |
| PUT | `/api/books/{id}` |
| DELETE | `/api/books/{id}` |
| GET | `/api/members`, `/api/members/{id}` |
| GET | `/api/loans` |
| POST | `/api/loans` |
| PUT | `/api/loans/{id}/kembalikan` |
| GET | `/api/stats` |

Contoh body, response, dan status code lengkap ada di [`API.md`](API.md).

## Struktur Singkat

```
app/Http/Controllers/           controller web + Api/
app/Http/Resources/             BookResource, MemberResource, LoanResource
app/Models/                     Book, Category, Member, Loan, LoanItem, User
database/seeders/               UserSeeder, CategorySeeder, BookSeeder, MemberSeeder, LoanSeeder
database/factories/             BookFactory, MemberFactory
resources/views/                layouts/, partials/, per-resource, vendor/pagination/custom
routes/web.php                  route halaman web
routes/api.php                  route REST API
```

## Troubleshooting

| Gejala | Penyebab | Solusi |
|---|---|---|
| Dashboard `0` / Laporan kosong | server `:8011` belum jalan | jalankan terminal kedua |
| Halaman menggantung lama | `INTERNAL_API_URL` mengarah ke port server utama | set `INTERNAL_API_URL=http://127.0.0.1:8011` |
| `SQLSTATE[HY000] [1049] Unknown database` | database belum dibuat | `CREATE DATABASE app_perpustakaan;` |
| `Access denied for user` | kredensial `DB_USERNAME`/`DB_PASSWORD` salah | sesuaikan dengan konfigurasi MySQL lokal |
| Data demo hilang / berantakan | database berisi sisa uji coba | `php artisan migrate:fresh --seed` |
| `npm run dev` error | project tidak memakai Vite/Tailwind untuk tampilan | abaikan; CSS ada di `<style>` `resources/views/layouts/app.blade.php` |

## Dokumen Terkait

- [`DEMO.md`](DEMO.md) — skenario demo UAS langkah demi langkah
- [`API.md`](API.md) — dokumentasi endpoint REST API beserta contoh response
