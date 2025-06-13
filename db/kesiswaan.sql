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

-- Dumping data for table kesiswaan.guru: ~2 rows (approximately)
INSERT INTO `guru` (`id_guru`, `jenis_kelamin`, `tempat_tanggal_lahir`, `alamat`, `no_hp`, `email`, `pendidikan_terakhir`, `program_studi`, `foto`) VALUES
	(2, 'P', 'Jakarta, 22 Maret 2012', 'Tes', '081212121213', 'anggreyniagustin@gmail.com', 'S1', 'Matematika', '1749477940-PAS-Foto-SMA-12.jpg'),
	(39, 'P', 'Jakarta, 22 Maret 2012', 'ajibarang', '081212341234', 'faiznurrahman842@gmail.comsdf', 'Diploma 3', 'Matematika', '1749475063-05845e778e60beae1586402d80410c68.jpg');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.jadwal_mapel: ~0 rows (approximately)

-- Dumping structure for table kesiswaan.kelas
CREATE TABLE IF NOT EXISTS `kelas` (
  `id_kelas` int NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(50) NOT NULL,
  `id_walikelas` int DEFAULT NULL,
  PRIMARY KEY (`id_kelas`),
  KEY `id_walikelas` (`id_walikelas`),
  CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`id_walikelas`) REFERENCES `pengguna` (`id_pengguna`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.kelas: ~0 rows (approximately)

-- Dumping structure for table kesiswaan.mapel
CREATE TABLE IF NOT EXISTS `mapel` (
  `id_mapel` int NOT NULL AUTO_INCREMENT,
  `nama_mapel` varchar(100) NOT NULL,
  PRIMARY KEY (`id_mapel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.mapel: ~0 rows (approximately)

-- Dumping structure for table kesiswaan.mapel_guru
CREATE TABLE IF NOT EXISTS `mapel_guru` (
  `id_mapel_guru` int NOT NULL AUTO_INCREMENT,
  `id_mapel` int DEFAULT NULL,
  `id_guru` int DEFAULT NULL,
  PRIMARY KEY (`id_mapel_guru`),
  KEY `id_mapel` (`id_mapel`),
  KEY `id_guru` (`id_guru`),
  CONSTRAINT `mapel_guru_ibfk_1` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`),
  CONSTRAINT `mapel_guru_ibfk_2` FOREIGN KEY (`id_guru`) REFERENCES `pengguna` (`id_pengguna`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.mapel_guru: ~0 rows (approximately)

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
  CONSTRAINT `nilai_siswa_ibfk_3` FOREIGN KEY (`id_guru`) REFERENCES `pengguna` (`id_pengguna`),
  CONSTRAINT `nilai_siswa_ibfk_4` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`),
  CONSTRAINT `nilai_siswa_ibfk_5` FOREIGN KEY (`id_tahun`) REFERENCES `tahun_ajaran` (`id_tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.nilai_siswa: ~0 rows (approximately)

-- Dumping structure for table kesiswaan.pengguna
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id_pengguna` int NOT NULL AUTO_INCREMENT,
  `nip` varchar(30) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','guru') NOT NULL,
  PRIMARY KEY (`id_pengguna`),
  UNIQUE KEY `nip` (`nip`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.pengguna: ~2 rows (approximately)
INSERT INTO `pengguna` (`id_pengguna`, `nip`, `nama`, `password`, `role`) VALUES
	(2, '123456789002', 'Ahmad Yani', '$2y$10$yiHEd4cKz8Ju/jMyIGTYzOUv9SqRiJTJIlWBDsq/0XQz1iFd14SXi', 'guru'),
	(39, '123456789001', 'Admin Sekolah', '$2y$10$RLwuzZFaxY9ZS9UB412xGeZMFZI45UpgtDsUYYmRSyukw80I3it0O', 'admin');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.siswa: ~0 rows (approximately)

-- Dumping structure for table kesiswaan.tahun_ajaran
CREATE TABLE IF NOT EXISTS `tahun_ajaran` (
  `id_tahun` int NOT NULL AUTO_INCREMENT,
  `tahun` varchar(20) DEFAULT NULL,
  `semester` enum('Ganjil','Genap') NOT NULL,
  PRIMARY KEY (`id_tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.tahun_ajaran: ~0 rows (approximately)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
