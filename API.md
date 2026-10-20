# Dokumentasi REST API — app-perpustakaan

Base URL: `http://127.0.0.1:8000/api`
Header wajib: `Accept: application/json`
Body request dikirim sebagai JSON (`Content-Type: application/json`).

Seluruh contoh response di bawah adalah hasil pengujian nyata dari Bruno (status code disertakan).

## Books

| Method | URL | Body (jika ada) | Contoh Response | Status |
|---|---|---|---|---|
| GET | `/api/books` | - | Lihat [GET /api/books](#get-apibooks) | `200` |
| GET | `/api/books/{id}` | - | Lihat [GET /api/books/{id}](#get-apibooksid) | `200` / `404` |
| POST | `/api/books` | `judul`, `penulis`, `penerbit`, `tahun_terbit`, `isbn`, `stok`, `category_id` | Lihat [POST /api/books](#post-apibooks) | `201` / `422` |
| PUT | `/api/books/{id}` | Sama dengan `POST /api/books` | Lihat [PUT /api/books/{id}](#put-apibooksid) | `200` / `422` |
| DELETE | `/api/books/{id}` | - | `{"message": "Buku berhasil dihapus."}` | `200` / `404` |

### GET /api/books

Query string opsional: `?judul=` (pencarian sebagian), `?category_id=` (pencocokan persis).

```json
{
  "data": [
    {
      "id": 1,
      "judul": "Laravel 12 Untuk Pemula",
      "penulis": "Budi",
      "penerbit": "PENS Press",
      "tahun_terbit": "2025",
      "isbn": "978-602-1",
      "stok": 5,
      "sampul": null,
      "category": {
        "id": 1,
        "nama_kategori": "Pemrograman"
      },
      "created_at": "2026-10-07T13:47:42.000000Z",
      "updated_at": "2026-10-07T13:47:42.000000Z"
    },
    {
      "id": 2,
      "judul": "Laskar Pelangi",
      "penulis": "Andrea Hirata",
      "penerbit": "Bentang Pustaka",
      "tahun_terbit": "2005",
      "isbn": null,
      "stok": 4,
      "sampul": null,
      "category": {
        "id": 2,
        "nama_kategori": "Novel"
      },
      "created_at": "2026-10-20T16:38:00.000000Z",
      "updated_at": "2026-10-20T16:38:17.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/books?page=1",
    "last": "http://127.0.0.1:8000/api/books?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      { "url": null, "label": "&laquo; Previous", "page": null, "active": false },
      { "url": "http://127.0.0.1:8000/api/books?page=1", "label": "1", "page": 1, "active": true },
      { "url": null, "label": "Next &raquo;", "page": null, "active": false }
    ],
    "path": "http://127.0.0.1:8000/api/books",
    "per_page": 10,
    "to": 2,
    "total": 2
  }
}
```

Filter `?judul=laskar` → hanya buku yang judulnya mengandung "laskar":

```json
{
  "data": [
    {
      "id": 2,
      "judul": "Laskar Pelangi",
      "penulis": "Andrea Hirata",
      "penerbit": "Bentang Pustaka",
      "tahun_terbit": "2005",
      "isbn": null,
      "stok": 4,
      "sampul": null,
      "category": { "id": 2, "nama_kategori": "Novel" },
      "created_at": "2026-10-20T16:38:00.000000Z",
      "updated_at": "2026-10-20T16:38:17.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/books?page=1",
    "last": "http://127.0.0.1:8000/api/books?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://127.0.0.1:8000/api/books",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

Filter `?category_id=2` menghasilkan bentuk response yang sama (hanya buku berkategori "Novel").

### GET /api/books/{id}

`GET /api/books/1` → `200`:

```json
{
  "data": {
    "id": 1,
    "judul": "Laravel 12 Untuk Pemula",
    "penulis": "Budi",
    "penerbit": "PENS Press",
    "tahun_terbit": "2025",
    "isbn": "978-602-1",
    "stok": 5,
    "sampul": null,
    "category": { "id": 1, "nama_kategori": "Pemrograman" },
    "created_at": "2026-10-07T13:47:42.000000Z",
    "updated_at": "2026-10-07T13:47:42.000000Z"
  }
}
```

`GET /api/books/999` (id tidak ada) → `404`:

```json
{
  "message": "No query results for model [App\\Models\\Book] 999",
  "exception": "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException"
}
```

> Body lengkap juga memuat `file`, `line`, dan `trace` karena `APP_DEBUG=true`. Pada `APP_DEBUG=false`, hanya `message` yang dikirim.

### POST /api/books

Body:

```json
{
  "judul": "Buku Uji API",
  "penulis": "Penulis X",
  "penerbit": "Penerbit X",
  "tahun_terbit": 2020,
  "isbn": "978-999-999",
  "stok": 3,
  "category_id": 1
}
```

Sukses → `201`:

```json
{
  "data": {
    "id": 4,
    "judul": "Buku Uji API",
    "penulis": "Penulis X",
    "penerbit": "Penerbit X",
    "tahun_terbit": 2020,
    "isbn": "978-999-999",
    "stok": 3,
    "sampul": null,
    "category": { "id": 1, "nama_kategori": "Pemrograman" },
    "created_at": "2026-10-20T16:55:53.000000Z",
    "updated_at": "2026-10-20T16:55:53.000000Z"
  }
}
```

Body kosong (`{}`) → `422`:

```json
{
  "message": "Judul buku wajib diisi. (and 5 more errors)",
  "errors": {
    "judul": ["Judul buku wajib diisi."],
    "penulis": ["Nama penulis wajib diisi."],
    "penerbit": ["Nama penerbit wajib diisi."],
    "tahun_terbit": ["Tahun terbit wajib diisi."],
    "stok": ["Stok wajib diisi."],
    "category_id": ["Kategori wajib dipilih."]
  }
}
```

### PUT /api/books/{id}

Body: sama dengan `POST /api/books`. `PUT /api/books/1` → `200`:

```json
{
  "data": {
    "id": 1,
    "judul": "Buku Uji API Edit",
    "penulis": "Penulis Y",
    "penerbit": "Penerbit Y",
    "tahun_terbit": 2021,
    "isbn": null,
    "stok": 1,
    "sampul": null,
    "category": { "id": 2, "nama_kategori": "Novel" },
    "created_at": "2026-10-07T13:47:42.000000Z",
    "updated_at": "2026-10-20T16:58:15.000000Z"
  }
}
```

### DELETE /api/books/{id}

`DELETE /api/books/5` → `200`:

```json
{
  "message": "Buku berhasil dihapus."
}
```

## Members

| Method | URL | Body (jika ada) | Contoh Response | Status |
|---|---|---|---|---|
| GET | `/api/members` | - | Lihat [GET /api/members](#get-apimembers) | `200` |
| GET | `/api/members/{id}` | - | Lihat [GET /api/members/{id}](#get-apimembersid) | `200` / `404` |

### GET /api/members

```json
{
  "data": [
    {
      "id": 1,
      "nama": "Andi Mahasiswa",
      "nim": "3123230001",
      "email": "andi@mahasiswa.pens.ac.id",
      "nomor_telepon": "081234567890",
      "alamat": "Surabaya",
      "status": "aktif",
      "created_at": "2026-10-07T13:47:43.000000Z",
      "updated_at": "2026-10-07T13:47:43.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/members?page=1",
    "last": "http://127.0.0.1:8000/api/members?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://127.0.0.1:8000/api/members",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

### GET /api/members/{id}

`GET /api/members/1` → `200`:

```json
{
  "data": {
    "id": 1,
    "nama": "Andi Mahasiswa",
    "nim": "3123230001",
    "email": "andi@mahasiswa.pens.ac.id",
    "nomor_telepon": "081234567890",
    "alamat": "Surabaya",
    "status": "aktif",
    "created_at": "2026-10-07T13:47:43.000000Z",
    "updated_at": "2026-10-07T13:47:43.000000Z"
  }
}
```

## Loans

| Method | URL | Body (jika ada) | Contoh Response | Status |
|---|---|---|---|---|
| GET | `/api/loans` | - | Lihat [GET /api/loans](#get-apiloans) | `200` |
| POST | `/api/loans` | `member_id`, `user_id`, `tanggal_pinjam`, `tanggal_kembali`, `book_ids[]` | Lihat [POST /api/loans](#post-apiloans) | `201` / `422` |
| PUT | `/api/loans/{id}/kembalikan` | - | Lihat [PUT /api/loans/{id}/kembalikan](#put-apiloansidkembalikan) | `200` / `404` |

### GET /api/loans

```json
{
  "data": [
    {
      "id": 1,
      "member": { "id": 1, "nama": "Andi Mahasiswa" },
      "petugas": { "id": 2, "name": "Petugas Satu" },
      "tanggal_pinjam": "2026-10-07",
      "tanggal_kembali": "2026-10-14",
      "tanggal_dikembalikan": null,
      "status": "dipinjam",
      "books": [
        { "id": 1, "judul": "Buku Uji API Edit" }
      ]
    },
    {
      "id": 2,
      "member": { "id": 1, "nama": "Andi Mahasiswa" },
      "petugas": { "id": 2, "name": "Petugas Satu" },
      "tanggal_pinjam": "2026-10-20",
      "tanggal_kembali": "2026-10-27",
      "tanggal_dikembalikan": "2026-10-20",
      "status": "dikembalikan",
      "books": [
        { "id": 1, "judul": "Buku Uji API Edit" },
        { "id": 2, "judul": "Laskar Pelangi" }
      ]
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/loans?page=1",
    "last": "http://127.0.0.1:8000/api/loans?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://127.0.0.1:8000/api/loans",
    "per_page": 10,
    "to": 2,
    "total": 2
  }
}
```

### POST /api/loans

`user_id` wajib dikirim eksplisit karena API bersifat publik (tidak memakai session login).

Body:

```json
{
  "member_id": 1,
  "user_id": 2,
  "tanggal_pinjam": "2026-10-20",
  "tanggal_kembali": "2026-10-27",
  "book_ids": [1, 2]
}
```

Sukses → `201`, `status` terbaca `"dipinjam"` (bukan `null`):

```json
{
  "data": {
    "id": 3,
    "member": { "id": 1, "nama": "Andi Mahasiswa" },
    "petugas": { "id": 2, "name": "Petugas Satu" },
    "tanggal_pinjam": "2026-10-20",
    "tanggal_kembali": "2026-10-27",
    "tanggal_dikembalikan": null,
    "status": "dipinjam",
    "books": [
      { "id": 1, "judul": "Buku Uji API Edit" },
      { "id": 2, "judul": "Laskar Pelangi" }
    ]
  }
}
```

Body tidak valid (mis. `book_ids` kosong) → `422` dengan pesan per field.

### PUT /api/loans/{id}/kembalikan

`PUT /api/loans/1/kembalikan` → `200`:

```json
{
  "data": {
    "id": 1,
    "member": { "id": 1, "nama": "Andi Mahasiswa" },
    "petugas": { "id": 2, "name": "Petugas Satu" },
    "tanggal_pinjam": "2026-10-07",
    "tanggal_kembali": "2026-10-14",
    "tanggal_dikembalikan": "2026-10-20",
    "status": "dikembalikan",
    "books": [
      { "id": 1, "judul": "Buku Uji API Edit" }
    ]
  }
}
```

## Stats

| Method | URL | Body (jika ada) | Contoh Response | Status |
|---|---|---|---|---|
| GET | `/api/stats` | - | `{"total_buku": 2, "total_anggota": 1, "peminjaman_aktif": 1}` | `200` |

```json
{
  "total_buku": 2,
  "total_anggota": 1,
  "peminjaman_aktif": 1
}
```
