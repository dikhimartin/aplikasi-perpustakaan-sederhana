/*
 Navicat Premium Data Transfer

 Source Server         : perpus
 Source Server Type    : MySQL
 Source Server Version : 80042 (8.0.42)
 Source Host           : 127.0.0.1:33306
 Source Schema         : perpustakaan_db

 Target Server Type    : MySQL
 Target Server Version : 80042 (8.0.42)
 File Encoding         : 65001

 Date: 04/07/2025 19:04:31
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for HistoryPeminjaman
-- ----------------------------
DROP TABLE IF EXISTS `HistoryPeminjaman`;
CREATE TABLE `HistoryPeminjaman` (
  `id_history` varchar(36) NOT NULL,
  `id_peminjaman` varchar(36) NOT NULL,
  `nim_mahasiswa` varchar(36) NOT NULL,
  `id_buku` varchar(36) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `lama_pinjam_hari` int NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_history`),
  KEY `id_peminjaman` (`id_peminjaman`),
  KEY `nim_mahasiswa` (`nim_mahasiswa`),
  KEY `id_buku` (`id_buku`),
  CONSTRAINT `HistoryPeminjaman_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `TransaksiPeminjaman` (`id_peminjaman`) ON DELETE CASCADE,
  CONSTRAINT `HistoryPeminjaman_ibfk_2` FOREIGN KEY (`nim_mahasiswa`) REFERENCES `MasterMahasiswa` (`nim`) ON DELETE CASCADE,
  CONSTRAINT `HistoryPeminjaman_ibfk_3` FOREIGN KEY (`id_buku`) REFERENCES `MasterBuku` (`id_buku`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of HistoryPeminjaman
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for MasterBuku
-- ----------------------------
DROP TABLE IF EXISTS `MasterBuku`;
CREATE TABLE `MasterBuku` (
  `id_buku` varchar(36) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `pengarang` varchar(255) NOT NULL,
  `penerbit` varchar(255) NOT NULL,
  `tahun_terbit` int NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_buku`),
  UNIQUE KEY `isbn` (`isbn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of MasterBuku
-- ----------------------------
BEGIN;
INSERT INTO `MasterBuku` (`id_buku`, `judul`, `pengarang`, `penerbit`, `tahun_terbit`, `isbn`, `created_at`, `updated_at`) VALUES ('f9339b31-58cd-11f0-b3c5-62981652b335', 'Dasar-Dasar Pemrograman PHP', 'Andi Nugraha', 'Gramedia', 2020, '978-602-03-1234-5', '2025-07-04 11:56:52', '2025-07-04 11:56:52');
INSERT INTO `MasterBuku` (`id_buku`, `judul`, `pengarang`, `penerbit`, `tahun_terbit`, `isbn`, `created_at`, `updated_at`) VALUES ('f9339d93-58cd-11f0-b3c5-62981652b335', 'Algoritma dan Struktur Data', 'Bambang Wijaya', 'Erlangga', 2018, '978-979-09-5678-9', '2025-07-04 11:56:52', '2025-07-04 11:56:52');
INSERT INTO `MasterBuku` (`id_buku`, `judul`, `pengarang`, `penerbit`, `tahun_terbit`, `isbn`, `created_at`, `updated_at`) VALUES ('f9339e86-58cd-11f0-b3c5-62981652b335', 'Basis Data untuk Pemula', 'Citra Dewi', 'Informatika', 2021, '978-623-78-9012-3', '2025-07-04 11:56:52', '2025-07-04 11:56:52');
COMMIT;

-- ----------------------------
-- Table structure for MasterMahasiswa
-- ----------------------------
DROP TABLE IF EXISTS `MasterMahasiswa`;
CREATE TABLE `MasterMahasiswa` (
  `nim` varchar(36) NOT NULL,
  `nama_mahasiswa` varchar(255) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `status` enum('aktif','non_aktif') DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`nim`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of MasterMahasiswa
-- ----------------------------
BEGIN;
INSERT INTO `MasterMahasiswa` (`nim`, `nama_mahasiswa`, `jurusan`, `email`, `no_telepon`, `status`, `created_at`, `updated_at`) VALUES ('0e4f1002-c613-4204-a46a-3f9fa1ac18c1', 'Siti Aminah', 'Sistem Informasi', 'siti.aminah@example.com', '085678901234', 'non_aktif', '2025-07-04 12:02:22', '2025-07-04 12:02:22');
INSERT INTO `MasterMahasiswa` (`nim`, `nama_mahasiswa`, `jurusan`, `email`, `no_telepon`, `status`, `created_at`, `updated_at`) VALUES ('11db3328-85ac-4ff2-be79-51e08c7c3738', 'Joko Susilo', 'Manajemen', 'joko.susilo@example.com', '087890123456', 'aktif', '2025-07-04 12:01:17', '2025-07-04 12:01:17');
INSERT INTO `MasterMahasiswa` (`nim`, `nama_mahasiswa`, `jurusan`, `email`, `no_telepon`, `status`, `created_at`, `updated_at`) VALUES ('1dd908ae-8e24-42be-bba3-10127f9c75cd', 'Dikhi Martin', 'Teknik Informatika', 'dikhi.martin@example.ac.id', '081234567890', 'aktif', '2025-07-04 11:59:19', '2025-07-04 11:59:19');
INSERT INTO `MasterMahasiswa` (`nim`, `nama_mahasiswa`, `jurusan`, `email`, `no_telepon`, `status`, `created_at`, `updated_at`) VALUES ('76cdc7af-c501-45b7-a246-33e36a17c62d', 'Budi Santoso', 'Teknik Informatika', 'budi.santoso@example.com', '081234567890', 'aktif', '2025-07-04 12:00:18', '2025-07-04 12:00:18');
COMMIT;

-- ----------------------------
-- Table structure for StokBuku
-- ----------------------------
DROP TABLE IF EXISTS `StokBuku`;
CREATE TABLE `StokBuku` (
  `id_stok` varchar(36) NOT NULL,
  `id_buku` varchar(36) NOT NULL,
  `jumlah_tersedia` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_stok`),
  KEY `id_buku` (`id_buku`),
  CONSTRAINT `StokBuku_ibfk_1` FOREIGN KEY (`id_buku`) REFERENCES `MasterBuku` (`id_buku`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of StokBuku
-- ----------------------------
BEGIN;
INSERT INTO `StokBuku` (`id_stok`, `id_buku`, `jumlah_tersedia`, `created_at`, `updated_at`) VALUES ('f934b7aa-58cd-11f0-b3c5-62981652b335', 'f9339b31-58cd-11f0-b3c5-62981652b335', 5, '2025-07-04 11:56:52', '2025-07-04 11:56:52');
INSERT INTO `StokBuku` (`id_stok`, `id_buku`, `jumlah_tersedia`, `created_at`, `updated_at`) VALUES ('f934bc45-58cd-11f0-b3c5-62981652b335', 'f9339d93-58cd-11f0-b3c5-62981652b335', 3, '2025-07-04 11:56:52', '2025-07-04 11:56:52');
INSERT INTO `StokBuku` (`id_stok`, `id_buku`, `jumlah_tersedia`, `created_at`, `updated_at`) VALUES ('f934bdc3-58cd-11f0-b3c5-62981652b335', 'f9339e86-58cd-11f0-b3c5-62981652b335', 7, '2025-07-04 11:56:52', '2025-07-04 11:56:52');
COMMIT;

-- ----------------------------
-- Table structure for TransaksiPeminjaman
-- ----------------------------
DROP TABLE IF EXISTS `TransaksiPeminjaman`;
CREATE TABLE `TransaksiPeminjaman` (
  `id_peminjaman` varchar(36) NOT NULL,
  `nim_mahasiswa` varchar(36) NOT NULL,
  `id_buku` varchar(36) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_maksimal` date NOT NULL,
  `tanggal_kembali_aktual` date DEFAULT NULL,
  `status` enum('dipinjam','dikembalikan','terlambat') DEFAULT 'dipinjam',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_peminjaman`),
  KEY `nim_mahasiswa` (`nim_mahasiswa`),
  KEY `id_buku` (`id_buku`),
  CONSTRAINT `TransaksiPeminjaman_ibfk_1` FOREIGN KEY (`nim_mahasiswa`) REFERENCES `MasterMahasiswa` (`nim`) ON DELETE CASCADE,
  CONSTRAINT `TransaksiPeminjaman_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `MasterBuku` (`id_buku`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of TransaksiPeminjaman
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for User
-- ----------------------------
DROP TABLE IF EXISTS `User`;
CREATE TABLE `User` (
  `id_user` varchar(36) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','mahasiswa') DEFAULT 'mahasiswa',
  `nim_mahasiswa` varchar(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  KEY `nim_mahasiswa` (`nim_mahasiswa`),
  CONSTRAINT `User_ibfk_1` FOREIGN KEY (`nim_mahasiswa`) REFERENCES `MasterMahasiswa` (`nim`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of User
-- ----------------------------
BEGIN;
INSERT INTO `User` (`id_user`, `username`, `password`, `role`, `nim_mahasiswa`, `created_at`, `updated_at`) VALUES ('a710421f-d83f-42d3-98b4-bf22411cc766', 'mahasiswa2', '$2y$12$q5BgTnH7y89vjywECpV5Xez0U.xa5mjw5n02kppvo4xTaj6qUghlq', 'mahasiswa', '11db3328-85ac-4ff2-be79-51e08c7c3738', '2025-07-04 12:01:18', '2025-07-04 12:01:18');
INSERT INTO `User` (`id_user`, `username`, `password`, `role`, `nim_mahasiswa`, `created_at`, `updated_at`) VALUES ('b4d2ecf7-756e-4dee-a3dc-89ad5f216dd9', 'dikhimartin', '$2y$12$p8S.RqSN9iPzQ4YgcRVI5.m4yFMu7dk2byUIIMFzlfoGzmLEP7VIy', 'mahasiswa', '1dd908ae-8e24-42be-bba3-10127f9c75cd', '2025-07-04 11:59:19', '2025-07-04 11:59:19');
INSERT INTO `User` (`id_user`, `username`, `password`, `role`, `nim_mahasiswa`, `created_at`, `updated_at`) VALUES ('d63c5fb9-3ff6-42b4-9d41-4110db979337', 'mahasiswa3', '$2y$12$xCre77MxWMaaNepC8zstQu5V7IWDWDAj4XxbzQ7uB6DebpxmfBYpW', 'mahasiswa', '0e4f1002-c613-4204-a46a-3f9fa1ac18c1', '2025-07-04 12:02:22', '2025-07-04 12:02:22');
INSERT INTO `User` (`id_user`, `username`, `password`, `role`, `nim_mahasiswa`, `created_at`, `updated_at`) VALUES ('f3d82337-2252-44ff-a726-cc0922780e96', 'mahasiswa1', '$2y$12$CQbuNUOb5kNgJOM3s1NoW.gCDqKHYDBkcImwmVtrWnoPDSqDt81OC', 'mahasiswa', '76cdc7af-c501-45b7-a246-33e36a17c62d', '2025-07-04 12:00:18', '2025-07-04 12:00:18');
INSERT INTO `User` (`id_user`, `username`, `password`, `role`, `nim_mahasiswa`, `created_at`, `updated_at`) VALUES ('f92e8bf3-58cd-11f0-b3c5-62981652b335', 'admin', '$2y$12$Lxijj51zoiYI6apXu3O84OBPdX3Rq5saRFMFNXk20FxMGza4Qm2KO', 'admin', NULL, '2025-07-04 11:56:52', '2025-07-04 11:56:52');
COMMIT;

-- ----------------------------
-- Function structure for HitungTotalTransaksi
-- ----------------------------
DROP FUNCTION IF EXISTS `HitungTotalTransaksi`;
delimiter ;;
CREATE FUNCTION `HitungTotalTransaksi`(qty INT, unitPrice NUMERIC(19,5))
 RETURNS decimal(19,5)
  DETERMINISTIC
BEGIN
    RETURN qty * unitPrice;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
