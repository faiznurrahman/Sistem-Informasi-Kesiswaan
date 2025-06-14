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

-- Dumping data for table kesiswaan.guru: ~0 rows (approximately)
INSERT INTO `guru` (`id_guru`, `jenis_kelamin`, `tempat_tanggal_lahir`, `alamat`, `no_hp`, `email`, `pendidikan_terakhir`, `program_studi`, `foto`) VALUES
	(3, 'L', 'Jakarta, 1980-05-15', 'Jl. Merdeka No. 10', '081234567801', 'budi.santoso@example.com', 'S1', 'Pendidikan Matematika', 'budi.jpg'),
	(4, 'P', 'Bandung, 1982-03-22', 'Jl. Sudirman No. 15', '081234567802', 'siti.aminah@example.com', 'S1', 'Pendidikan Bahasa Inggris', 'siti.jpg'),
	(5, 'L', 'Surabaya, 1978-11-10', 'Jl. Gatot Subroto No. 20', '081234567803', 'ahmad.yani@example.com', 'S1', 'Pendidikan Fisika', 'ahmad.jpg'),
	(6, 'P', 'Yogyakarta, 1985-07-19', 'Jl. Malioboro No. 25', '081234567804', 'rina.susanti@example.com', 'S1', 'Pendidikan Biologi', 'rina.jpg'),
	(7, 'L', 'Semarang, 1983-09-30', 'Jl. Pahlawan No. 30', '081234567805', 'eko.prasetyo@example.com', 'S1', 'Pendidikan Kimia', 'eko.jpg'),
	(8, 'P', 'Medan, 1981-12-12', 'Jl. Diponegoro No. 35', '081234567806', 'dewi.lestari@example.com', 'S1', 'Pendidikan Sejarah', 'dewi.jpg'),
	(9, 'L', 'Makassar, 1979-04-25', 'Jl. Urip Sumoharjo No. 40', '081234567807', 'fajar.nugroho@example.com', 'S1', 'Pendidikan Geografi', 'fajar.jpg'),
	(10, 'P', 'Denpasar, 1984-06-18', 'Jl. Hayam Wuruk No. 45', '081234567808', 'lina.marlina@example.com', 'S1', 'Pendidikan Seni', 'lina.jpg');

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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.jadwal: ~0 rows (approximately)
INSERT INTO `jadwal` (`id_jadwal`, `id_kelas`, `id_mapel`, `id_guru`, `hari`, `jam_mulai`, `jam_selesai`, `ruang`, `id_tahun`) VALUES
	(1, 1, 1, 3, 'Senin', '07:00:00', '08:30:00', 'A101', 1),
	(2, 1, 2, 4, 'Senin', '08:30:00', '10:00:00', 'A102', 1),
	(3, 2, 3, 5, 'Selasa', '07:00:00', '08:30:00', 'A103', 1),
	(4, 2, 4, 6, 'Selasa', '08:30:00', '10:00:00', 'A104', 1),
	(5, 3, 5, 7, 'Rabu', '07:00:00', '08:30:00', 'A105', 1),
	(6, 3, 6, 8, 'Rabu', '08:30:00', '10:00:00', 'A106', 1),
	(7, 4, 7, 9, 'Kamis', '07:00:00', '08:30:00', 'A107', 1),
	(8, 4, 8, 10, 'Kamis', '08:30:00', '10:00:00', 'A108', 1),
	(9, 5, 1, 3, 'Jumat', '07:00:00', '08:30:00', 'A109', 1),
	(10, 5, 2, 4, 'Jumat', '08:30:00', '10:00:00', 'A110', 1),
	(11, 6, 3, 5, 'Senin', '10:00:00', '11:30:00', 'A111', 1),
	(12, 6, 4, 6, 'Senin', '11:30:00', '13:00:00', 'A112', 1),
	(13, 7, 5, 7, 'Selasa', '10:00:00', '11:30:00', 'A113', 1),
	(14, 7, 6, 8, 'Selasa', '11:30:00', '13:00:00', 'A114', 1),
	(15, 8, 7, 9, 'Rabu', '10:00:00', '11:30:00', 'A115', 1),
	(16, 8, 8, 10, 'Rabu', '11:30:00', '13:00:00', 'A116', 1);

-- Dumping structure for table kesiswaan.kelas
CREATE TABLE IF NOT EXISTS `kelas` (
  `id_kelas` int NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(50) NOT NULL,
  `id_walikelas` int DEFAULT NULL,
  PRIMARY KEY (`id_kelas`),
  KEY `id_walikelas` (`id_walikelas`),
  CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`id_walikelas`) REFERENCES `guru` (`id_guru`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.kelas: ~0 rows (approximately)
INSERT INTO `kelas` (`id_kelas`, `nama_kelas`, `id_walikelas`) VALUES
	(1, 'X IPA 1', 3),
	(2, 'X IPA 2', 4),
	(3, 'X IPS 1', 5),
	(4, 'XI IPA 1', 6),
	(5, 'XI IPA 2', 7),
	(6, 'XI IPS 1', 8),
	(7, 'XII IPA 1', 9),
	(8, 'XII IPS 1', 10);

-- Dumping structure for table kesiswaan.mapel
CREATE TABLE IF NOT EXISTS `mapel` (
  `id_mapel` int NOT NULL AUTO_INCREMENT,
  `nama_mapel` varchar(100) NOT NULL,
  PRIMARY KEY (`id_mapel`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.mapel: ~0 rows (approximately)
INSERT INTO `mapel` (`id_mapel`, `nama_mapel`) VALUES
	(1, 'Matematika'),
	(2, 'Bahasa Inggris'),
	(3, 'Fisika'),
	(4, 'Biologi'),
	(5, 'Kimia'),
	(6, 'Sejarah'),
	(7, 'Geografi'),
	(8, 'Seni Budaya');

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.mapel_guru: ~0 rows (approximately)
INSERT INTO `mapel_guru` (`id_mapel_guru`, `id_mapel`, `id_guru`) VALUES
	(1, 1, 3),
	(2, 2, 4),
	(3, 3, 5),
	(4, 4, 6),
	(5, 5, 7),
	(6, 6, 8),
	(7, 7, 9),
	(8, 8, 10);

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.nilai_siswa: ~0 rows (approximately)
INSERT INTO `nilai_siswa` (`id_nilai`, `id_siswa`, `id_mapel`, `id_guru`, `id_kelas`, `id_tahun`, `nilai_uh`, `nilai_uts`, `nilai_uas`, `nilai_akhir`) VALUES
	(1, 1, 1, 3, 1, 1, 80, 85, 90, 85),
	(2, 2, 2, 4, 1, 1, 75, 80, 85, 80),
	(3, 5, 3, 5, 2, 1, 78, 82, 80, 80),
	(4, 6, 4, 6, 2, 1, 85, 88, 90, 87.67),
	(5, 9, 5, 7, 3, 1, 75, 80, 85, 80),
	(6, 10, 6, 8, 3, 1, 88, 90, 92, 90),
	(7, 13, 7, 9, 4, 1, 82, 85, 88, 85),
	(8, 14, 8, 10, 4, 1, 80, 78, 82, 80),
	(9, 17, 1, 3, 5, 1, 85, 90, 87, 87.33),
	(10, 21, 3, 5, 6, 1, 78, 82, 80, 80);

-- Dumping structure for table kesiswaan.pengguna
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id_pengguna` int NOT NULL AUTO_INCREMENT,
  `nip` varchar(30) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','guru') NOT NULL,
  PRIMARY KEY (`id_pengguna`),
  UNIQUE KEY `nip` (`nip`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.pengguna: ~10 rows (approximately)
INSERT INTO `pengguna` (`id_pengguna`, `nip`, `nama`, `password`, `role`) VALUES
	(1, 'ADM001', 'Admin Utama', '$2y$10$RLwuzZFaxY9ZS9UB412xGeZMFZI45UpgtDsUYYmRSyukw80I3it0O', 'admin'),
	(2, 'ADM002', 'Admin Cadangan', '$2y$10$RLwuzZFaxY9ZS9UB412xGeZMFZI45UpgtDsUYYmRSyukw80I3it0O', 'admin'),
	(3, 'GRU001', 'Budi Santoso', '$2y$10$yiHEd4cKz8Ju/jMyIGTYzOUv9SqRiJTJIlWBDsq/0XQz1iFd14SXi', 'guru'),
	(4, 'GRU002', 'Siti Aminah', '$2y$10$yiHEd4cKz8Ju/jMyIGTYzOUv9SqRiJTJIlWBDsq/0XQz1iFd14SXi', 'guru'),
	(5, 'GRU003', 'Ahmad Yani', '$2y$10$yiHEd4cKz8Ju/jMyIGTYzOUv9SqRiJTJIlWBDsq/0XQz1iFd14SXi', 'guru'),
	(6, 'GRU004', 'Rina Susanti', '$2y$10$yiHEd4cKz8Ju/jMyIGTYzOUv9SqRiJTJIlWBDsq/0XQz1iFd14SXi', 'guru'),
	(7, 'GRU005', 'Eko Prasetyo', '$2y$10$yiHEd4cKz8Ju/jMyIGTYzOUv9SqRiJTJIlWBDsq/0XQz1iFd14SXi', 'guru'),
	(8, 'GRU006', 'Dewi Lestari', '$2y$10$yiHEd4cKz8Ju/jMyIGTYzOUv9SqRiJTJIlWBDsq/0XQz1iFd14SXi', 'guru'),
	(9, 'GRU007', 'Fajar Nugroho', '$2y$10$yiHEd4cKz8Ju/jMyIGTYzOUv9SqRiJTJIlWBDsq/0XQz1iFd14SXi', 'guru'),
	(10, 'GRU008', 'Lina Marlina', '$2y$10$yiHEd4cKz8Ju/jMyIGTYzOUv9SqRiJTJIlWBDsq/0XQz1iFd14SXi', 'guru');

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
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.siswa: ~0 rows (approximately)
INSERT INTO `siswa` (`id_siswa`, `nama_siswa`, `nis`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `foto`, `id_kelas`) VALUES
	(1, 'Andi Pratama', 'SIS001', 'L', '2008-01-10', 'Jl. Kebon Jeruk No. 1', 'andi.jpg', 1),
	(2, 'Bunga Melati', 'SIS002', 'P', '2008-02-15', 'Jl. Melati No. 2', 'bunga.jpg', 1),
	(3, 'Candra Wijaya', 'SIS003', 'L', '2008-03-20', 'Jl. Anggrek No. 3', 'candra.jpg', 1),
	(4, 'Dewi Sartika', 'SIS004', 'P', '2008-04-25', 'Jl. Mawar No. 4', 'dewi.jpg', 1),
	(5, 'Eka Putra', 'SIS005', 'L', '2008-05-30', 'Jl. Kamboja No. 5', 'eka.jpg', 2),
	(6, 'Fitri Rahayu', 'SIS006', 'P', '2008-06-05', 'Jl. Kenanga No. 6', 'fitri.jpg', 2),
	(7, 'Gilang Ramadhan', 'SIS007', 'L', '2008-07-10', 'Jl. Flamboyan No. 7', 'gilang.jpg', 2),
	(8, 'Hani Suryani', 'SIS008', 'P', '2008-08-15', 'Jl. Teratai No. 8', 'hani.jpg', 2),
	(9, 'Indra Kusuma', 'SIS009', 'L', '2007-09-20', 'Jl. Cempaka No. 9', 'indra.jpg', 3),
	(10, 'Julianti Sari', 'SIS010', 'P', '2007-10-25', 'Jl. Dahlia No. 10', 'julianti.jpg', 3),
	(11, 'Kurniawan Adi', 'SIS011', 'L', '2007-11-30', 'Jl. Kembang No. 11', 'kurniawan.jpg', 3),
	(12, 'Lia Amalia', 'SIS012', 'P', '2007-12-05', 'Jl. Bougenville No. 12', 'lia.jpg', 3),
	(13, 'Miko Saputra', 'SIS013', 'L', '2007-01-10', 'Jl. Sedap Malam No. 13', 'miko.jpg', 4),
	(14, 'Nia Ramadhani', 'SIS014', 'P', '2007-02-15', 'Jl. Sakura No. 14', 'nia.jpg', 4),
	(15, 'Oka Wijaya', 'SIS015', 'L', '2007-03-20', 'Jl. Tulip No. 15', 'oka.jpg', 4),
	(16, 'Putri Aulia', 'SIS016', 'P', '2007-04-25', 'Jl. Anggrek No. 16', 'putri.jpg', 4),
	(17, 'Rudi Hartono', 'SIS017', 'L', '2006-05-30', 'Jl. Melati No. 17', 'rudi.jpg', 5),
	(18, 'Sari Indah', 'SIS018', 'P', '2006-06-05', 'Jl. Mawar No. 18', 'sari.jpg', 5),
	(19, 'Tono Susilo', 'SIS019', 'L', '2006-07-10', 'Jl. Kamboja No. 19', 'tono.jpg', 5),
	(20, 'Umi Kalsum', 'SIS020', 'P', '2006-08-15', 'Jl. Kenanga No. 20', 'umi.jpg', 5),
	(21, 'Vino Bastian', 'SIS021', 'L', '2006-09-20', 'Jl. Flamboyan No. 21', 'vino.jpg', 6),
	(22, 'Wulan Sari', 'SIS022', 'P', '2006-10-25', 'Jl. Teratai No. 22', 'wulan.jpg', 6),
	(23, 'Xena Putri', 'SIS023', 'P', '2006-11-30', 'Jl. Cempaka No. 23', 'xena.jpg', 6),
	(24, 'Yudi Pratama', 'SIS024', 'L', '2006-12-05', 'Jl. Dahlia No. 24', 'yudi.jpg', 6),
	(25, 'Zaki Rahman', 'SIS025', 'L', '2005-01-10', 'Jl. Kembang No. 25', 'zaki.jpg', 7),
	(26, 'Aisyah Nur', 'SIS026', 'P', '2005-02-15', 'Jl. Bougenville No. 26', 'aisyah.jpg', 7),
	(27, 'Bima Sakti', 'SIS027', 'L', '2005-03-20', 'Jl. Sedap Malam No. 27', 'bima.jpg', 7),
	(28, 'Cici Lestari', 'SIS028', 'P', '2005-04-25', 'Jl. Sakura No. 28', 'cici.jpg', 7),
	(29, 'Dedi Kurnia', 'SIS029', 'L', '2005-05-30', 'Jl. Tulip No. 29', 'dedi.jpg', 8),
	(30, 'Evi Susanti', 'SIS030', 'P', '2005-06-05', 'Jl. Anggrek No. 30', 'evi.jpg', 8);

-- Dumping structure for table kesiswaan.tahun_ajaran
CREATE TABLE IF NOT EXISTS `tahun_ajaran` (
  `id_tahun` int NOT NULL AUTO_INCREMENT,
  `tahun` varchar(20) DEFAULT NULL,
  `semester` enum('Ganjil','Genap') NOT NULL,
  PRIMARY KEY (`id_tahun`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kesiswaan.tahun_ajaran: ~0 rows (approximately)
INSERT INTO `tahun_ajaran` (`id_tahun`, `tahun`, `semester`) VALUES
	(1, '2024/2025', 'Ganjil'),
	(2, '2024/2025', 'Genap'),
	(3, '2025/2026', 'Ganjil');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
