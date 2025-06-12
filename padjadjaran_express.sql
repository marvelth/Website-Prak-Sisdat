-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 12, 2025 at 03:10 AM
-- Server version: 8.0.39
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `padjadjaran_express`
--

-- --------------------------------------------------------

--
-- Table structure for table `kantor_cabang`
--

CREATE TABLE `kantor_cabang` (
  `id_cabang` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_cabang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kantor_cabang`
--

INSERT INTO `kantor_cabang` (`id_cabang`, `nama_cabang`, `alamat`, `password`) VALUES
('KC001', 'Kantor Pusat Jakarta', 'Jl. Sudirman No. 123, Jakarta', 'password_cabang_1'),
('KC002', 'Cabang Bandung', 'Jl. Asia Afrika No. 45, Bandung', 'password_cabang_2'),
('KC003', 'Cabang Surabaya', 'Jl. Darmo No. 67, Surabaya', 'password_cabang_3');

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id_kendaraan` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `id_cabang` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_kendaraan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jenis_kendaraan` varchar(40) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kapasitas` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kendaraan`
--

INSERT INTO `kendaraan` (`id_kendaraan`, `id_cabang`, `nama_kendaraan`, `jenis_kendaraan`, `kapasitas`) VALUES
('KD001', 'KC001', 'Motor Beat', 'Motor', 100),
('KD002', 'KC001', 'Mobil Box Kecil', 'Mobil Box', 500),
('KD003', 'KC002', 'Motor Vario', 'Motor', 120),
('KD004', 'KC002', 'Mobil Pickup', 'Mobil Pickup', 800),
('KD005', 'KC003', 'Motor NMAX', 'Motor', 150);

-- --------------------------------------------------------

--
-- Table structure for table `kurir`
--

CREATE TABLE `kurir` (
  `id_kurir` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `id_cabang` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_kendaraan` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_kurir` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_telepon` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_keaktifan` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kurir`
--

INSERT INTO `kurir` (`id_kurir`, `id_cabang`, `id_kendaraan`, `nama_kurir`, `no_telepon`, `status_keaktifan`) VALUES
('KR001', 'KC001', 'KD001', 'Andi Wijaya', '081122334455', 1),
('KR002', 'KC001', 'KD002', 'Rina Susanti', '082233445566', 1),
('KR003', 'KC002', 'KD003', 'Joko Prasetyo', '083344556677', 1),
('KR004', 'KC003', 'KD005', 'Maria Ulfa', '084455667788', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `id_cabang` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_pelanggan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telepon` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `id_cabang`, `nama_pelanggan`, `telepon`, `email`, `alamat`) VALUES
('PL001', 'KC001', 'Budi Santoso', '081234567890', 'budi.s@example.com', 'Jl. Kebon Jeruk No. 10, Jakarta'),
('PL002', 'KC001', 'Siti Aminah', '087654321098', 'siti.a@example.com', 'Jl. Thamrin No. 20, Jakarta'),
('PL003', 'KC002', 'Agus Setiawan', '085012345678', 'agus.s@example.com', 'Jl. Merdeka No. 5, Bandung'),
('PL010', 'KC002', 'Fathin', '08123456789', 'fathin@email.com', 'Jl. Dipatiukur');

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman`
--

CREATE TABLE `pengiriman` (
  `id_pengiriman` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `id_kurir` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_pesanan` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `tanggal_sampai` date DEFAULT NULL,
  `status_pengiriman` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengiriman`
--

INSERT INTO `pengiriman` (`id_pengiriman`, `id_kurir`, `id_pesanan`, `tanggal_kirim`, `tanggal_sampai`, `status_pengiriman`) VALUES
('PGR01', 'KR001', 'PSN01', '2024-05-21', NULL, 'Dalam Pengiriman'),
('PGR02', 'KR003', 'PSN03', '2024-05-23', NULL, 'Dalam Pengiriman'),
('PGR03', 'KR001', 'PSN04', '2024-05-19', '2024-05-20', 'Terkirim');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `id_pelanggan` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_barang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_barang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_pemesanan` date DEFAULT NULL,
  `berat` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_pelanggan`, `nama_barang`, `status_barang`, `tanggal_pemesanan`, `berat`) VALUES
('PSN01', 'PL001', 'Laptop Gaming', 'Dikirim', '2024-05-20', 3.50),
('PSN02', 'PL002', 'Smartphone Terbaru', 'Diproses', '2024-05-21', 0.80),
('PSN03', 'PL003', 'Kamera DSLR', 'Dikirim', '2024-05-22', 2.10),
('PSN04', 'PL001', 'Headset Bluetooth', 'Selesai', '2024-05-18', 0.30);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kantor_cabang`
--
ALTER TABLE `kantor_cabang`
  ADD PRIMARY KEY (`id_cabang`);

--
-- Indexes for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`),
  ADD KEY `id_cabang` (`id_cabang`);

--
-- Indexes for table `kurir`
--
ALTER TABLE `kurir`
  ADD PRIMARY KEY (`id_kurir`),
  ADD KEY `id_cabang` (`id_cabang`),
  ADD KEY `id_kendaraan` (`id_kendaraan`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD KEY `id_cabang` (`id_cabang`);

--
-- Indexes for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`id_pengiriman`),
  ADD KEY `id_kurir` (`id_kurir`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD CONSTRAINT `kendaraan_ibfk_1` FOREIGN KEY (`id_cabang`) REFERENCES `kantor_cabang` (`id_cabang`);

--
-- Constraints for table `kurir`
--
ALTER TABLE `kurir`
  ADD CONSTRAINT `kurir_ibfk_1` FOREIGN KEY (`id_cabang`) REFERENCES `kantor_cabang` (`id_cabang`),
  ADD CONSTRAINT `kurir_ibfk_2` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id_kendaraan`);

--
-- Constraints for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_ibfk_1` FOREIGN KEY (`id_cabang`) REFERENCES `kantor_cabang` (`id_cabang`);

--
-- Constraints for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD CONSTRAINT `pengiriman_ibfk_1` FOREIGN KEY (`id_kurir`) REFERENCES `kurir` (`id_kurir`),
  ADD CONSTRAINT `pengiriman_ibfk_2` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
