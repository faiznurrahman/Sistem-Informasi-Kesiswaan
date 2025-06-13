-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.7.0.6850
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for kesiswaan
CREATE DATABASE IF NOT EXISTS `kesiswaan` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `kesiswaan`;

-- Dumping structure for table kesiswaan.guru
CREATE TABLE IF NOT EXISTS `guru` (
  `id_guru` int NOT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `tempat_tanggal_lahir` varchar(150) DEFAULT NULL,
  `alamat` text,
  `no_hp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `pendidikan_terakhir` varchar(50) DEFAULT NULL,
  `program_studi` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_guru`),
  CONSTRAINT `guru_ibfk_1` FOREIGN KEY (`id_guru`) REFERENCES `pengguna` (`id_pengguna`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.guru: ~4 rows (approximately)
INSERT INTO `guru` (`id_guru`, `jenis_kelamin`, `tempat_tanggal_lahir`, `alamat`, `no_hp`, `email`, `pendidikan_terakhir`, `program_studi`, `foto`) VALUES
	(2, 'P', 'Jakarta, 22 Maret 1975', 'Jl. Tes No. 1', '081212121213', 'ahmad.yani@gmail.com', 'S1', 'Matematika', '1749477940-PAS-Foto-SMA-12.jpg'),
	(3, 'P', 'Bandung, 15 Mei 1980', 'Jl. Ajibarang No. 5', '081212341234', 'siti.aminah@gmail.com', 'S1', 'Bahasa Indonesia', '1749475063-profile.jpg'),
	(4, 'L', 'Jakarta, 22 Maret 2012', 'Jakarta', '081212121212', 'faiznurrahman842@gmail.comsdf', 'S2', 'Matematika', '1749651901-261d25689004496b7629ad88f2134b5a.jpg'),
	(5, 'L', 'Jakarta, 22 Maret 2012', 'Jakarta', '081212121212', 'faiznurrahman842@gmail.com', 'S3', 'Bahasa Indonesia', '1749652269-WhatsApp Image 2025-06-08 at 20.36.25_cfe77622.jpg');

-- Dumping structure for table kesiswaan.jadwal
CREATE TABLE IF NOT EXISTS `jadwal` (
  `id_jadwal` int NOT NULL AUTO_INCREMENT,
  `id_kelas` int NOT NULL,
  `id_mapel` int NOT NULL,
  `id_guru` int NOT NULL,
  `hari` varchar(20) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruang` varchar(10) DEFAULT NULL,
  `id_tahun` int NOT NULL,
  PRIMARY KEY (`id_jadwal`),
  KEY `id_kelas` (`id_kelas`),
  KEY `id_mapel` (`id_mapel`),
  KEY `id_guru` (`id_guru`),
  KEY `id_tahun` (`id_tahun`),
  CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`),
  CONSTRAINT `jadwal_ibfk_2` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`),
  CONSTRAINT `jadwal_ibfk_3` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`),
  CONSTRAINT `jadwal_ibfk_4` FOREIGN KEY (`id_tahun`) REFERENCES `tahun_ajaran` (`id_tahun`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.jadwal: ~0 rows (approximately)
INSERT INTO `jadwal` (`id_jadwal`, `id_kelas`, `id_mapel`, `id_guru`, `hari`, `jam_mulai`, `jam_selesai`, `ruang`, `id_tahun`) VALUES
	(1, 1, 6, 5, 'Jumat', '08:43:00', '09:00:00', '0', 1),
	(2, 1, 5, 4, 'Kamis', '09:00:00', '10:20:00', '0', 2);

-- Dumping structure for table kesiswaan.jadwal_mapel
CREATE TABLE IF NOT EXISTS `jadwal_mapel` (
  `id_jadwal` int NOT NULL AUTO_INCREMENT,
  `id_kelas` int DEFAULT NULL,
  `id_mapel` int DEFAULT NULL,
  `hari` varchar(20) DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `id_tahun` int DEFAULT NULL,
  PRIMARY KEY (`id_jadwal`),
  KEY `id_kelas` (`id_kelas`),
  KEY `id_mapel` (`id_mapel`),
  KEY `id_tahun` (`id_tahun`),
  CONSTRAINT `jadwal_mapel_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`),
  CONSTRAINT `jadwal_mapel_ibfk_2` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`),
  CONSTRAINT `jadwal_mapel_ibfk_3` FOREIGN KEY (`id_tahun`) REFERENCES `tahun_ajaran` (`id_tahun`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.jadwal_mapel: ~1 rows (approximately)
INSERT INTO `jadwal_mapel` (`id_jadwal`, `id_kelas`, `id_mapel`, `hari`, `jam_mulai`, `jam_selesai`, `id_tahun`) VALUES
	(3, 2, 3, 'Rabu', '07:00:00', '08:30:00', 1);

-- Dumping structure for table kesiswaan.kelas
CREATE TABLE IF NOT EXISTS `kelas` (
  `id_kelas` int NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(50) NOT NULL,
  `id_walikelas` int DEFAULT NULL,
  PRIMARY KEY (`id_kelas`),
  KEY `id_walikelas` (`id_walikelas`),
  CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`id_walikelas`) REFERENCES `guru` (`id_guru`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.kelas: ~4 rows (approximately)
INSERT INTO `kelas` (`id_kelas`, `nama_kelas`, `id_walikelas`) VALUES
	(1, 'X IPA 1', 2),
	(2, 'X IPA 2', 3),
	(3, 'X IPS 1', 4),
	(4, 'X IPS 2', 5);

-- Dumping structure for table kesiswaan.mapel
CREATE TABLE IF NOT EXISTS `mapel` (
  `id_mapel` int NOT NULL AUTO_INCREMENT,
  `nama_mapel` varchar(100) NOT NULL,
  PRIMARY KEY (`id_mapel`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.mapel: ~3 rows (approximately)
INSERT INTO `mapel` (`id_mapel`, `nama_mapel`) VALUES
	(3, 'Biologi'),
	(5, 'Matematika'),
	(6, 'Bahasa Indonesia');

-- Dumping structure for table kesiswaan.mapel_guru
CREATE TABLE IF NOT EXISTS `mapel_guru` (
  `id_mapel_guru` int NOT NULL AUTO_INCREMENT,
  `id_mapel` int DEFAULT NULL,
  `id_guru` int DEFAULT NULL,
  PRIMARY KEY (`id_mapel_guru`),
  UNIQUE KEY `unique_guru` (`id_guru`),
  KEY `id_mapel` (`id_mapel`),
  CONSTRAINT `mapel_guru_ibfk_1` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`),
  CONSTRAINT `mapel_guru_ibfk_2` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.mapel_guru: ~2 rows (approximately)
INSERT INTO `mapel_guru` (`id_mapel_guru`, `id_mapel`, `id_guru`) VALUES
	(4, 5, 4),
	(5, 6, 5),
	(7, 3, 3);

-- Dumping structure for table kesiswaan.nilai_siswa
CREATE TABLE IF NOT EXISTS `nilai_siswa` (
  `id_nilai` int NOT NULL AUTO_INCREMENT,
  `id_siswa` int DEFAULT NULL,
  `id_mapel` int DEFAULT NULL,
  `id_guru` int DEFAULT NULL,
  `id_kelas` int DEFAULT NULL,
  `id_tahun` int DEFAULT NULL,
  `nilai_uh` double DEFAULT NULL,
  `nilai_uts` double DEFAULT NULL,
  `nilai_uas` double DEFAULT NULL,
  `nilai_akhir` double DEFAULT NULL,
  PRIMARY KEY (`id_nilai`),
  KEY `id_siswa` (`id_siswa`),
  KEY `id_mapel` (`id_mapel`),
  KEY `id_guru` (`id_guru`),
  KEY `id_kelas` (`id_kelas`),
  KEY `id_tahun` (`id_tahun`),
  CONSTRAINT `nilai_siswa_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`),
  CONSTRAINT `nilai_siswa_ibfk_2` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`),
  CONSTRAINT `nilai_siswa_ibfk_3` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`),
  CONSTRAINT `nilai_siswa_ibfk_4` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`),
  CONSTRAINT `nilai_siswa_ibfk_5` FOREIGN KEY (`id_tahun`) REFERENCES `tahun_ajaran` (`id_tahun`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.nilai_siswa: ~1 rows (approximately)

-- Dumping structure for table kesiswaan.pengguna
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id_pengguna` int NOT NULL AUTO_INCREMENT,
  `nip` varchar(30) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','guru') NOT NULL,
  PRIMARY KEY (`id_pengguna`),
  UNIQUE KEY `nip` (`nip`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.pengguna: ~5 rows (approximately)
INSERT INTO `pengguna` (`id_pengguna`, `nip`, `nama`, `password`, `role`) VALUES
	(1, '123456789001', 'Admin Sekolah', '$2y$10$RLwuzZFaxY9ZS9UB412xGeZMFZI45UpgtDsUYYmRSyukw80I3it0O', 'admin'),
	(2, '123456789002', 'Ahmad Yani', '$2y$10$yiHEd4cKz8Ju/jMyIGTYzOUv9SqRiJTJIlWBDsq/0XQz1iFd14SXi', 'guru'),
	(3, '123456789003', 'Siti Aminah', '$2y$10$examplehashedpassword1234567890', 'guru'),
	(4, '123456789004', 'Joko Widodo', '$2y$10$GlvGXFfqOubq1F2bZc1GFu/GsXtyqFqWoY8AmiR.CwkHRqy3ddese', 'guru'),
	(5, '123456789005', 'Amad', '$2y$10$51zMvErYvU.19aJF/oin.ezRNofPMgFM7lae6/YXXf3q3tfTAzoay', 'guru');

-- Dumping structure for table kesiswaan.siswa
CREATE TABLE IF NOT EXISTS `siswa` (
  `id_siswa` int NOT NULL AUTO_INCREMENT,
  `nama_siswa` varchar(100) NOT NULL,
  `nis` varchar(30) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text,
  `foto` varchar(255) DEFAULT NULL,
  `id_kelas` int DEFAULT NULL,
  PRIMARY KEY (`id_siswa`),
  UNIQUE KEY `nis` (`nis`),
  KEY `id_kelas` (`id_kelas`),
  CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.siswa: ~4 rows (approximately)
INSERT INTO `siswa` (`id_siswa`, `nama_siswa`, `nis`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `foto`, `id_kelas`) VALUES
	(1, 'Ahmad Fajar', 'NIS001', 'L', '2008-05-10', 'Jl. Merdeka No. 1', NULL, 1),
	(2, 'Budi Santoso', 'NIS002', 'L', '2007-11-23', 'Jl. Melati No. 5', NULL, 1),
	(3, 'Citra Ayu', 'NIS003', 'P', '2008-08-15', 'Jl. Mawar No. 3', NULL, 2);

-- Dumping structure for table kesiswaan.tahun_ajaran
CREATE TABLE IF NOT EXISTS `tahun_ajaran` (
  `id_tahun` int NOT NULL AUTO_INCREMENT,
  `tahun` varchar(20) DEFAULT NULL,
  `semester` enum('Ganjil','Genap') NOT NULL,
  PRIMARY KEY (`id_tahun`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.tahun_ajaran: ~3 rows (approximately)
INSERT INTO `tahun_ajaran` (`id_tahun`, `tahun`, `semester`) VALUES
	(1, '2024/2025', 'Ganjil'),
	(2, '2024/2025', 'Genap'),
	(3, '2025/2026', 'Ganjil'),
	(4, '2025/2026', 'Genap');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
