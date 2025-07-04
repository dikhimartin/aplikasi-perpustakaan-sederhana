CREATE DATABASE IF NOT EXISTS perpustakaan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE perpustakaan_db;

-- Tabel MasterBuku
-- id_buku menggunakan VARCHAR(36) untuk menyimpan UUID
CREATE TABLE IF NOT EXISTS MasterBuku (
    id_buku VARCHAR(36) PRIMARY KEY NOT NULL,
    judul VARCHAR(255) NOT NULL,
    pengarang VARCHAR(255) NOT NULL,
    penerbit VARCHAR(255) NOT NULL,
    tahun_terbit INT NOT NULL,
    isbn VARCHAR(20) UNIQUE, -- ISBN harus unik
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel MasterMahasiswa
-- nim menggunakan VARCHAR(36) untuk menyimpan UUID
CREATE TABLE IF NOT EXISTS MasterMahasiswa (
    nim VARCHAR(36) PRIMARY KEY NOT NULL,
    nama_mahasiswa VARCHAR(255) NOT NULL,
    jurusan VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE,
    no_telepon VARCHAR(20),
    status ENUM('aktif', 'non_aktif') DEFAULT 'aktif', -- Status mahasiswa
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel StokBuku
-- id_stok menggunakan VARCHAR(36) untuk menyimpan UUID
-- Relasi ke MasterBuku
CREATE TABLE IF NOT EXISTS StokBuku (
    id_stok VARCHAR(36) PRIMARY KEY NOT NULL,
    id_buku VARCHAR(36) NOT NULL,
    jumlah_tersedia INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_buku) REFERENCES MasterBuku(id_buku) ON DELETE CASCADE
);

-- Tabel TransaksiPeminjaman
-- id_peminjaman menggunakan VARCHAR(36) untuk menyimpan UUID
-- Relasi ke MasterMahasiswa dan MasterBuku
CREATE TABLE IF NOT EXISTS TransaksiPeminjaman (
    id_peminjaman VARCHAR(36) PRIMARY KEY NOT NULL,
    nim_mahasiswa VARCHAR(36) NOT NULL,
    id_buku VARCHAR(36) NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali_maksimal DATE NOT NULL,
    tanggal_kembali_aktual DATE NULL, -- Akan diisi saat buku dikembalikan
    status ENUM('dipinjam', 'dikembalikan', 'terlambat') DEFAULT 'dipinjam',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nim_mahasiswa) REFERENCES MasterMahasiswa(nim) ON DELETE CASCADE,
    FOREIGN KEY (id_buku) REFERENCES MasterBuku(id_buku) ON DELETE CASCADE
);

-- Tabel HistoryPeminjaman
-- id_history menggunakan VARCHAR(36) untuk menyimpan UUID
-- Relasi ke TransaksiPeminjaman, MasterMahasiswa, dan MasterBuku
-- Data akan dimasukkan ke sini saat TransaksiPeminjaman selesai/diupdate statusnya menjadi 'dikembalikan'
CREATE TABLE IF NOT EXISTS HistoryPeminjaman (
    id_history VARCHAR(36) PRIMARY KEY NOT NULL,
    id_peminjaman VARCHAR(36) NOT NULL, -- Merujuk ke transaksi peminjaman yang sudah selesai
    nim_mahasiswa VARCHAR(36) NOT NULL,
    id_buku VARCHAR(36) NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE NOT NULL,
    lama_pinjam_hari INT NOT NULL,
    keterangan VARCHAR(255) NULL, -- Opsional, bisa untuk denda, dll.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_peminjaman) REFERENCES TransaksiPeminjaman(id_peminjaman) ON DELETE CASCADE,
    FOREIGN KEY (nim_mahasiswa) REFERENCES MasterMahasiswa(nim) ON DELETE CASCADE,
    FOREIGN KEY (id_buku) REFERENCES MasterBuku(id_buku) ON DELETE CASCADE
);

-- Tabel User (untuk fitur login dan RBAC)
-- id_user menggunakan VARCHAR(36) untuk menyimpan UUID
-- password akan disimpan dalam bentuk hash
-- role bisa 'admin' atau 'mahasiswa'
-- nim_mahasiswa akan diisi jika role adalah 'mahasiswa'
CREATE TABLE IF NOT EXISTS User (
    id_user VARCHAR(36) PRIMARY KEY NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Simpan hash password (gunakan password_hash() di PHP)
    role ENUM('admin', 'mahasiswa') DEFAULT 'mahasiswa',
    nim_mahasiswa VARCHAR(36) NULL, -- NULL jika user bukan mahasiswa (misal: admin)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nim_mahasiswa) REFERENCES MasterMahasiswa(nim) ON DELETE SET NULL
);


-- Contoh menambahkan user admin
-- Ganti 'password_admin_terenkripsi' dengan hasil password_hash('password123', PASSWORD_BCRYPT) dari PHP
-- Kita bisa menghasilkan hash-nya di PHP dengan: echo password_hash('password123', PASSWORD_BCRYPT);
INSERT INTO User (id_user, username, password, role) VALUES (
    UUID(), -- Menggunakan fungsi UUID() bawaan MySQL 8+
    'admin',
    '$2y$12$Lxijj51zoiYI6apXu3O84OBPdX3Rq5saRFMFNXk20FxMGza4Qm2KO', -- Ini adalah hash contoh untuk 'password123'
    'admin'
) ON DUPLICATE KEY UPDATE username=username; 

-- Contoh menambahkan data mahasiswa
INSERT INTO MasterMahasiswa (nim, nama_mahasiswa, jurusan, email, no_telepon, status) VALUES
(UUID(), 'Budi Santoso', 'Teknik Informatika', 'budi.santoso@example.com', '081234567890', 'aktif'),
(UUID(), 'Siti Aminah', 'Sistem Informasi', 'siti.aminah@example.com', '085678901234', 'aktif'),
(UUID(), 'Joko Susilo', 'Manajemen', 'joko.susilo@example.com', '087890123456', 'non_aktif')
ON DUPLICATE KEY UPDATE nama_mahasiswa=nama_mahasiswa;

-- Contoh menambahkan user untuk mahasiswa (misal: 'mahasiswa1' untuk Budi Santoso)
-- Kita perlu mengambil NIM dari mahasiswa yang sudah ada
-- Jalankan ini setelah data MasterMahasiswa di atas dimasukkan
INSERT INTO User (id_user, username, password, role, nim_mahasiswa) VALUES (
    UUID(),
    'mahasiswa1',
    '$2y$12$Lxijj51zoiYI6apXu3O84OBPdX3Rq5saRFMFNXk20FxMGza4Qm2KO', -- Ini adalah hash contoh untuk 'password123'
    'mahasiswa',
    (SELECT nim FROM MasterMahasiswa WHERE nama_mahasiswa = 'Budi Santoso' LIMIT 1)
) ON DUPLICATE KEY UPDATE username=username;

INSERT INTO User (id_user, username, password, role, nim_mahasiswa) VALUES (
    UUID(),
    'mahasiswa2',
    '$2y$12$Lxijj51zoiYI6apXu3O84OBPdX3Rq5saRFMFNXk20FxMGza4Qm2KO', -- Ini adalah hash contoh untuk 'password123'
    'mahasiswa',
    (SELECT nim FROM MasterMahasiswa WHERE nama_mahasiswa = 'Siti Aminah' LIMIT 1)
) ON DUPLICATE KEY UPDATE username=username;

-- Contoh menambahkan data buku
-- Perhatikan bahwa id_buku juga menggunakan UUID
INSERT INTO MasterBuku (id_buku, judul, pengarang, penerbit, tahun_terbit, isbn) VALUES
(UUID(), 'Dasar-Dasar Pemrograman PHP', 'Andi Nugraha', 'Gramedia', 2020, '978-602-03-1234-5'),
(UUID(), 'Algoritma dan Struktur Data', 'Bambang Wijaya', 'Erlangga', 2018, '978-979-09-5678-9'),
(UUID(), 'Basis Data untuk Pemula', 'Citra Dewi', 'Informatika', 2021, '978-623-78-9012-3')
ON DUPLICATE KEY UPDATE judul=judul;

-- Contoh mengisi stok buku (setelah buku ditambahkan)
INSERT INTO StokBuku (id_stok, id_buku, jumlah_tersedia) VALUES
(UUID(), (SELECT id_buku FROM MasterBuku WHERE judul = 'Dasar-Dasar Pemrograman PHP' LIMIT 1), 5),
(UUID(), (SELECT id_buku FROM MasterBuku WHERE judul = 'Algoritma dan Struktur Data' LIMIT 1), 3),
(UUID(), (SELECT id_buku FROM MasterBuku WHERE judul = 'Basis Data untuk Pemula' LIMIT 1), 7)
ON DUPLICATE KEY UPDATE jumlah_tersedia=jumlah_tersedia;
