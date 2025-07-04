# Aplikasi Perpustakaan Sederhana

Aplikasi manajemen perpustakaan sederhana berbasis PHP Native dengan arsitektur MVC (Model-View-Controller) dan database MySQL. Aplikasi ini memungkinkan pengelolaan data buku, mahasiswa, serta transaksi peminjaman dan pengembalian buku, dilengkapi dengan fitur otentikasi pengguna dan Role-Based Access Control (RBAC).

---

## Daftar Isi

- [Persyaratan Minimum](#persyaratan-minimum)
- [Proses Cloning Proyek](#proses-cloning-proyek)
- [Struktur Proyek](#struktur-proyek)
- [Setup Database](#setup-database)
  - [Konfigurasi Database (app/Config/database.php)](#konfigurasi-database-appconfigdatabasephp)
  - [Impor Skema dan Data Awal](#impor-skema-dan-data-awal)
- [Cara Menjalankan Aplikasi](#cara-menjalankan-aplikasi)
  - [Menggunakan PHP Built-in Server (Disarankan untuk Pengembangan Lokal)](#menggunakan-php-built-in-server-disarankan-untuk-pengembangan-lokal)
  - [Menggunakan Docker Compose](#menggunakan-docker-compose)
- [Kredensial Login Default](#kredensial-login-default)
- [Arsitektur Aplikasi](#arsitektur-aplikasi)
- [Modul Aplikasi](#modul-aplikasi)

---

## Persyaratan Minimum

Pastikan sistem kita memenuhi persyaratan berikut:

- **PHP:** Versi `8.2` atau lebih tinggi.
- **MySQL:** Versi `8.0` atau lebih tinggi.
- **Git:** Untuk melakukan cloning repository.
- **Docker & Docker Compose:** (Jika ingin menjalankan database dan/atau aplikasi menggunakan Docker)

---

## Proses Cloning Proyek

Untuk mendapatkan salinan proyek ini di sistem lokal kamu, ikuti langkah-langkah berikut:

1. **Buka Terminal atau Command Prompt:** Arahkan ke direktori tempat kamu ingin menyimpan proyek.

2. **Lakukan Cloning Repository:** Gunakan perintah `git clone` berikut:

   ```bash
   git clone https://github.com/dikhimartin/aplikasi-perpustakaan-sederhana.git
   ```

3. **Masuk ke Direktori Proyek:** Setelah proses cloning selesai, navigasikan ke direktori proyek:

   ```bash
   cd aplikasi-perpustakaan-sederhana
   ```

Sekarang kamu memiliki salinan lokal dari proyek ini dan siap untuk melanjutkan ke proses setup.

---

## Struktur Proyek

Berikut adalah struktur direktori utama proyek ini:

````
.
├── app/            \# Logika inti aplikasi (Model, Controller, Core)
│   ├── Config/     \# Konfigurasi aplikasi (misal: database.php)
│   ├── Controllers/\# Menangani logika permintaan dan interaksi model/view
│   ├── Core/       \# Class inti (Database, Router, Auth, Utilities)
│   └── Models/     \# Berinteraksi langsung dengan database
├── docker-compose.yaml \# Konfigurasi Docker Compose untuk services database
├── dump\_sql/       \# Direktori untuk script SQL database
│   └── data.sql    \# Script SQL untuk membuat tabel dan data awal
├── public/         \# Direktori root web yang dapat diakses publik
│   ├── css/        \# File CSS
│   ├── index.php   \# Front controller utama aplikasi
│   └── js/         \# File JavaScript
├── README.md       \# File dokumentasi ini
└── views/          \# File tampilan (HTML/PHP)
├── auth/       \# Tampilan terkait otentikasi (login)
├── buku/       \# Tampilan untuk manajemen buku
├── layouts/    \# Layout template utama aplikasi
├── mahasiswa/  \# Tampilan untuk manajemen mahasiswa
├── partials/   \# Bagian-bagian tampilan yang dapat digunakan kembali (header, footer)
└── peminjaman/ \# Tampilan untuk manajemen peminjaman

````

---

## Setup Database

Aplikasi ini menggunakan database MySQL. Kita dapat menyiapkannya secara manual atau menggunakan Docker Compose.

### Konfigurasi Database (app/Config/database.php)

Pastikan file `app/Config/database.php` kita dikonfigurasi dengan benar sesuai lingkungan kita.

```php
<?php
// app/Config/database.php

// Jika menggunakan PHP Built-in Server dan MySQL dari Docker Compose:
define('DB_HOST', 'localhost:33306'); // Sesuaikan dengan port yang kita forward di docker-compose.yaml
// Jika menggunakan Docker Compose untuk PHP dan MySQL (dalam jaringan Docker):
// define('DB_HOST', 'db'); // 'db' adalah nama services MySQL di docker-compose.yaml
define('DB_USER', 'your_db_user');     // Ganti dengan username database kita
define('DB_PASS', 'your_db_password'); // Ganti dengan password database kita
define('DB_NAME', 'perpustakaan_db'); // Nama database
````

### Impor Skema dan Data Awal

Kita dapat mengimpor skema database dan data awal menggunakan phpMyAdmin (jika menggunakan Docker Compose) atau klien MySQL/MariaDB lainnya.

1.  **Akses phpMyAdmin:** Jika menggunakan Docker Compose, akses `http://localhost:8080`.
2.  **Buat Database:** Buat database baru dengan nama `perpustakaan_db` (atau nama lain yang kita tentukan di `database.php` dan `docker-compose.yaml`). Pastikan menggunakan `utf8mb4_unicode_ci` untuk collation.
3.  **Impor SQL:** Impor file `dump_sql/data.sql` ke database yang baru kita buat. File ini berisi semua definisi tabel dan data awal (termasuk user admin).

-----

## Cara Menjalankan Aplikasi

Ada dua cara utama untuk menjalankan aplikasi ini secara lokal:

### Menggunakan PHP Built-in Server (Disarankan untuk Pengembangan Lokal)

Ini adalah cara paling cepat dan mudah untuk menjalankan aplikasi PHP tanpa perlu konfigurasi web server eksternal seperti Apache atau Nginx.

1. **Buka Terminal:** Navigasikan ke direktori root proyek kita (misal: `/var/www/html/perpustakaan`).

   ```bash
   cd /path/to/your/project/aplikasi-perpustakaan-sederhana
   ```

2. **Jalankan Server:**

   ```bash
   php -S localhost:8000 -t public
   ```

     - `localhost:8000`: Aplikasi akan berjalan di `http://localhost:8000`. Kita bisa mengganti `8000` dengan port lain jika diperlukan.
     - `-t public`: Menentukan folder `public` sebagai document root server. Ini penting agar routing aplikasi kita berfungsi dengan benar.

3. **Akses Aplikasi:** Buka browser kita dan kunjungi `http://localhost:8000/`.

### Menggunakan Docker Compose

Menggunakan Docker Compose akan menjalankan database (MySQL) dan phpMyAdmin dalam kontainer terisolasi, yang sangat direkomendasikan untuk konsistensi lingkungan pengembangan.

1. **Pastikan Docker Terinstal:** Pastikan Docker dan Docker Compose sudah terinstal di sistem kita.

2. **Jalankan Kontainer:** Buka terminal di direktori root proyek kita (tempat file `docker-compose.yaml` berada), lalu jalankan:

   ```bash
   docker-compose up -d
   ```

     - Ini akan membuat dan menjalankan kontainer `db` (MySQL) dan `phpmyadmin` di latar belakang.
     - Port MySQL `3306` dari kontainer akan di-forward ke port `33306` di host kita.
     - Port phpMyAdmin `80` dari kontainer akan di-forward ke port `8080` di host kita.

3. **Akses phpMyAdmin:** Buka browser dan kunjungi `http://localhost:8080`. Kita bisa login dengan user `root` dan `your_root_password` yang kita tentukan di `docker-compose.yaml`.

4. **Impor Database:** Gunakan phpMyAdmin untuk mengimpor `dump_sql/data.sql` ke database `perpustakaan_db`.

5. **Jalankan Aplikasi PHP:** Setelah database siap, jalankan aplikasi PHP kita menggunakan PHP Built-in Server seperti yang dijelaskan di bagian sebelumnya:

   ```bash
   php -S localhost:8000 -t public
   ```

6. **Akses Aplikasi:** Buka browser kita dan kunjungi `http://localhost:8000/`.

**Untuk Menghentikan Kontainer Docker:**

```bash
docker-compose down
```

Untuk menghentikan dan menghapus semua data database (jangan lakukan ini jika kita ingin menyimpan data):

```bash
docker-compose down -v
```

-----

## Kredensial Login Default

Setelah mengimpor `dump_sql/data.sql`, kita akan memiliki user `admin` yang dapat digunakan untuk login:

  - **Username:** `admin`
  - **Password:** `password123`

-----

## Arsitektur Aplikasi

Aplikasi ini dibangun dengan pendekatan **MVC (Model-View-Controller) sederhana** dan beberapa komponen inti (`Core`) untuk menangani fungsionalitas dasar.

  - **Model (`app/Models/`):** Bertanggung jawab untuk berinteraksi dengan database. Setiap file model merepresentasikan sebuah entitas atau tabel database (misal: `Buku.php`, `Mahasiswa.php`, `User.php`, `Peminjaman.php`, `StokBuku.php`). Mereka berisi logika untuk operasi CRUD (Create, Read, Update, Delete) dan validasi terkait data.
  - **View (`views/`):** Bertanggung jawab untuk menampilkan antarmuka pengguna. Ini adalah file HTML/PHP yang berisi markup dan sedikit logika presentasi. Views tidak boleh berisi logika bisnis atau akses database langsung.
    - `layouts/app.php`: Template layout utama yang digunakan oleh semua halaman.
    - `partials/`: Bagian-bagian kecil dari tampilan yang dapat digunakan kembali (misal: header, footer).
  - **Controller (`app/Controllers/`):** Bertindak sebagai perantara antara Model dan View. Controller menerima permintaan pengguna, memanggil Model untuk memproses data, dan kemudian meneruskan data ke View untuk ditampilkan.
    - `AuthController.php`: Menangani logika login, logout, dan otentikasi.
    - `BukuController.php`: Mengelola operasi terkait buku (tambah, edit, hapus, daftar).
    - `MahasiswaController.php`: Mengelola operasi terkait mahasiswa.
    - `PeminjamanController.php`: Mengelola operasi peminjaman dan pengembalian buku.
  - **Core (`app/Core/`):** Berisi Class-Class inti yang mendukung fungsionalitas aplikasi secara keseluruhan.
    - `Database.php`: Menangani koneksi ke database.
    - `Router.php`: Menerima permintaan HTTP dan mengarahkannya ke Controller/metode yang sesuai.
    - `Auth.php`: Menangani logika otentikasi dan otorisasi (Role-Based Access Control - RBAC).
    - `Utils.php`: Berisi fungsi-fungsi utilitas umum (misal: `generateUuid`).
  - **Public (`public/`):** Direktori yang dapat diakses langsung oleh web server. Berisi `index.php` (front controller) dan aset statis (CSS, JS).

-----

## Modul Aplikasi

Aplikasi ini mencakup modul-modul utama berikut:

  - **User & Otentikasi (Login/Logout):**
    - Pengelolaan user (`User` Model).
    - Fungsionalitas login dan logout (`AuthController`).
    - **Role-Based Access Control (RBAC):** Membedakan akses berdasarkan peran pengguna (`admin` atau `mahasiswa`) melalui Class `Auth`. Hanya admin yang dapat melakukan operasi CRUD pada buku dan mahasiswa.
  - **Master Data Buku:**
    - Melihat daftar buku (`BukuController`, `views/buku/index.php`).
    - Menambah buku baru (`BukuController`, `views/buku/create.php`).
    - Mengedit detail buku (`BukuController`, `views/buku/edit.php`).
    - Menghapus buku (`BukuController`).
    - Pengelolaan stok buku terpisah (`StokBuku` Model).
  - **Master Data Mahasiswa:**
    - Melihat daftar mahasiswa (`MahasiswaController`, `views/mahasiswa/index.php`).
    - Menambah mahasiswa baru (`MahasiswaController`, `views/mahasiswa/create.php`).
    - Mengedit detail mahasiswa (`MahasiswaController`, `views/mahasiswa/edit.php`).
    - Menghapus mahasiswa (`MahasiswaController`).
  - **Transaksi Peminjaman:**
    - Mencatat peminjaman buku baru (`PeminjamanController`, `views/peminjaman/create.php`).
    - Mengelola status peminjaman (dipinjam, dikembalikan, terlambat).
    - Pengurangan stok buku otomatis saat peminjaman.
  - **Riwayat Peminjaman:**
    - Melihat daftar peminjaman aktif (`PeminjamanController`, `views/peminjaman/history.php`).
    - Melihat riwayat lengkap peminjaman (termasuk yang sudah dikembalikan) dengan fitur filter dan sorting.
    - Fungsionalitas pengembalian buku (`PeminjamanController`).
    - Penambahan stok buku otomatis saat pengembalian.
    - Pencatatan riwayat peminjaman terpisah (`HistoryPeminjaman` Tabel).

