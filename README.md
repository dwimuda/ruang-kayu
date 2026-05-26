# 🪵 Ruang Kayu — Furniture Alami & Modern

Ruang Kayu adalah sebuah platform katalog interaktif untuk furniture kerajinan tangan (*handcrafted*) berbahan dasar kayu alam pilihan. Dilengkapi dengan website portofolio interaktif bagi pelanggan di bagian depan (*frontend*) serta sistem manajemen konten (*backend*) yang memungkinkan administrator untuk mengelola katalog produk, melacak leads WhatsApp, mengunggah gambar, dan memantau analitik secara dinamis.

---

## ✨ Fitur Utama

### 🖥️ Frontend (Katalog Pelanggan)
*   **Desain Modern & Premium:** Menggunakan visual bernuansa estetika alam, tipografi modern (Fraunces & Jost), efek transisi halus, paralaks, serta kustomisasi kursor interaktif.
*   **Katalog Interaktif:** Filter produk berdasarkan kategori (Meja, Kursi, Lemari, Rak, dll.) secara *real-time* tanpa memuat ulang halaman.
*   **Detail Produk Slider:** Tampilan detail produk dalam bentuk modal interaktif yang dilengkapi dengan kompartemen spesifikasi teknis dan *image slider/carousel* ramah seluler (mendukung sentuhan/swipe).
*   **Integrasi WhatsApp:** Pelanggan dapat langsung menanyakan produk tertentu lewat tombol otomatis yang memformat pesan detail produk beserta harganya.

### ⚙️ Backend & Admin Panel (Manajemen Konten)
*   **Dashboard Statistik:** Memantau metrik penting secara sekilas, termasuk jumlah produk, total prospek (*leads*), prospek hari ini, dan daftar prospek terbaru.
*   **Produk CRUD:** Kelola katalog produk (tambah, edit, dan hapus) secara dinamis dari portal admin.
*   **Unggah Foto Produk:** Modul upload gambar terintegrasi untuk menambahkan foto produk langsung ke server.
*   **Daftar Leads:** Panel khusus untuk mencatat dan melacak setiap kali pengunjung mengeklik tombol WhatsApp untuk berkonsultasi tentang produk tertentu.
*   **Keamanan Terotentikasi:** Menggunakan token otentikasi Bearer berbasis enkripsi Base64 untuk melindungi API backend dan halaman administrasi.

---

## 🛠️ Teknologi yang Digunakan

| Komponen | Teknologi | Keterangan |
|---|---|---|
| **Frontend** | HTML5, Vanilla JS, CSS3 | Tanpa framework berat demi performa pemuatan yang cepat. |
| **Backend API** | PHP 7.4+ (Native) | RESTful API terstruktur dan aman. |
| **Database** | SQLite 3 | Basis data file tunggal berkinerja tinggi, tanpa konfigurasi DBMS yang rumit. |
| **Integrasi** | WhatsApp API | Penghubung langsung ke kontak sales/custom order. |

---

## 📁 Struktur Direktori

```text
Ruang-Kayu/
├── admin/                 # Panel Administrator (PHP)
│   ├── assets/            # CSS & Aset penunjang panel admin
│   ├── dashboard.php      # Dashboard statistik & kelola data
│   ├── index.php          # Redirect & validasi sesi login
│   ├── leads.php          # Halaman pelacakan leads pelanggan
│   ├── login.php          # Form autentikasi admin
│   ├── logout.php         # Sesi keluar
│   └── produk.php         # Manajemen tambah/edit/hapus produk
├── api/                   # RESTful API Backend
│   └── index.php          # Endpoint API & inisialisasi Database SQLite
├── data/                  # Tempat Penyimpanan Database
│   └── ruangkayu.db       # Database SQLite (dibuat otomatis)
├── img/                   # Folder Aset Gambar Produk
│   └── uploads/           # Gambar produk hasil upload admin (dibuat otomatis)
├── index.html             # Landing Page utama frontend
├── script.js              # Logika interaktif frontend
├── style.css              # Styling utama frontend
├── router.php             # Router untuk web server built-in PHP
└── README.md              # Dokumentasi proyek
```

---

## 🚀 Cara Menjalankan Proyek Secara Lokal

Pastikan Anda telah menginstal **PHP** (versi 7.4 ke atas) pada komputer Anda.

### 1. Clone Repositori
```bash
git clone https://github.com/DwiMuda/Ruang-Kayu.git
cd Ruang-Kayu
```

### 2. Jalankan PHP Built-in Server
Untuk menjalankan frontend sekaligus merutekan REST API dengan benar, jalankan perintah di bawah ini di terminal root direktori proyek Anda:
```bash
php -S localhost:8000 router.php
```

### 3. Akses Website
*   **Halaman Utama (Katalog Frontend):**
    Buka browser Anda dan akses **[http://localhost:8000](http://localhost:8000)**
*   **Panel Admin (Backend & Dashboard):**
    Akses **[http://localhost:8000/admin/](http://localhost:8000/admin/)**

---

## 🔑 Kredensial Login Admin (Bawaan)

Saat API backend diakses untuk pertama kali, sistem akan mendeteksi database SQLite dan menginisialisasi tabel beserta satu akun administrator awal secara otomatis:

*   **Username:** `admin`
*   **Password:** `ruangkayu123`

> [!WARNING]
> Demi keamanan, Anda sangat disarankan untuk mengubah kata sandi default ini secara langsung melalui database jika sistem dideploy ke server produksi.

---

## 📡 Endpoint API Backend (`/api/`)

Backend menggunakan model RESTful API terpadu yang diproses melalui `api/index.php`:

| HTTP Method | Endpoint | Akses | Deskripsi |
|---|---|---|---|
| **POST** | `/api/auth/login` | Publik | Otentikasi admin, mengembalikan Token Bearer. |
| **GET** | `/api/products` | Publik | Mendapatkan semua produk (atau produk tunggal dengan parameter `?id=X`). |
| **POST** | `/api/products` | Admin | Menambah produk baru (Memerlukan header `Authorization`). |
| **PUT** | `/api/products` | Admin | Memperbarui data produk (Memerlukan header `Authorization`). |
| **DELETE** | `/api/products` | Admin | Menghapus produk dengan parameter `?id=X` (Memerlukan header `Authorization`). |
| **GET** | `/api/leads` | Admin | Mendapatkan daftar prospek/leads dari klik WhatsApp. |
| **POST** | `/api/leads` | Publik | Mencatat prospek baru saat WhatsApp diklik oleh pelanggan. |
| **GET** | `/api/stats` | Admin | Mendapatkan statistik data produk dan leads untuk dashboard. |
| **POST** | `/api/upload` | Admin | Mengunggah gambar produk baru (Mendukung JPG, PNG, GIF, WEBP). |

---

## 📝 Catatan Penting
*   **Folder Aset Gambar:** Jika terdapat gambar produk bawaan yang tidak muncul pada load pertama, silakan periksa folder `/img/` dan sesuaikan nama file gambar Anda dengan file di database SQLite.
*   **SQLite Extension:** Pastikan ekstensi SQLite diaktifkan di file konfigurasi `php.ini` Anda (`extension=pdo_sqlite` dan `extension=sqlite3`).
