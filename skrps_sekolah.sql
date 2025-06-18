-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2025 at 02:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skrps_sekolah`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'petugas@gmail.com', '$2y$10$PZlSboPeKlI/fSxdON7pVukbUOGz8MK7AmGDL.1l8qIdqKT8E2Ify');

-- --------------------------------------------------------

--
-- Table structure for table `bahan_ajar`
--

CREATE TABLE `bahan_ajar` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `nama_file_asli` varchar(255) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `tipe_file` varchar(100) NOT NULL,
  `ukuran_file` int(11) NOT NULL,
  `id_guru_uploader` int(11) NOT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bahan_ajar`
--

INSERT INTO `bahan_ajar` (`id`, `judul`, `deskripsi`, `nama_file_asli`, `path_file`, `tipe_file`, `ukuran_file`, `id_guru_uploader`, `tanggal_upload`) VALUES
(1, 'bab 1', 'b1=', 'bab1.pdf', 'uploads/bahan_ajar/file_68517d12e83538.99707783.pdf', 'application/pdf', 329407, 9, '2025-06-17 14:34:58');

-- --------------------------------------------------------

--
-- Table structure for table `bank_soal`
--

CREATE TABLE `bank_soal` (
  `id` int(11) NOT NULL,
  `id_guru_uploader` int(11) NOT NULL,
  `judul_soal` varchar(255) NOT NULL,
  `mata_pelajaran` varchar(100) DEFAULT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `jenis_ujian` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `nama_file_asli` varchar(255) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `tipe_file` varchar(100) NOT NULL,
  `ukuran_file` int(11) NOT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank_soal`
--

INSERT INTO `bank_soal` (`id`, `id_guru_uploader`, `judul_soal`, `mata_pelajaran`, `kelas`, `jenis_ujian`, `deskripsi`, `nama_file_asli`, `path_file`, `tipe_file`, `ukuran_file`, `tanggal_upload`) VALUES
(1, 9, 'Ipa', 'Matematika', 'XII TKJ', 'UAS', '', 'latihan-us-usp-mat-ipa.pdf', 'uploads/bank_soal/9_soal_685290d96f6c55.48260194.pdf', 'application/pdf', 536651, '2025-06-18 10:11:37');

-- --------------------------------------------------------

--
-- Table structure for table `chat_log`
--

CREATE TABLE `chat_log` (
  `id` int(11) NOT NULL,
  `user_input` text NOT NULL,
  `bot_response` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `gelar` varchar(100) DEFAULT NULL,
  `mata_pelajaran` varchar(100) DEFAULT NULL,
  `kelompok_mata_pelajaran` varchar(100) DEFAULT NULL,
  `jurusan` varchar(255) DEFAULT NULL,
  `jenis_kelamin` enum('Laki-Laki','Perempuan','Lainnya') DEFAULT NULL,
  `agama` varchar(50) DEFAULT NULL,
  `jenjang` varchar(50) DEFAULT NULL,
  `pangkat` varchar(50) DEFAULT NULL,
  `golongan` varchar(10) DEFAULT NULL,
  `nip` varchar(18) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `email_pemulihan` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id`, `name`, `gelar`, `mata_pelajaran`, `kelompok_mata_pelajaran`, `jurusan`, `jenis_kelamin`, `agama`, `jenjang`, `pangkat`, `golongan`, `nip`, `email`, `email_pemulihan`, `photo`, `created_at`, `updated_at`, `password`, `alamat`, `no_hp`) VALUES
(9, 'Usman', 'ST', '11', 'Kepala Sekolah', 'Teknik Elektro', 'Laki-Laki', 'Islam', 'S1', 'Pembina Tingkat I', 'IV/b', '196908231993031007', 'usman@gmail.com', '', 'uploads/profile_67ab25575a465.jpg', '2025-02-11 10:19:14', '2025-06-17 14:20:01', '$2y$10$4bPWRmI/DwBcI41KyO.dte31giKG52Tt1WSSMUFB1x49CPH4b0UOC', 'Jl Kapten Muslim gg pribadi', '0895612167255'),
(10, 'Juleta', 'Dra', '12', 'PAI', 'Pendidikan Agama Islam', 'Perempuan', 'Islam', 'S1', 'Pembina Tingkat I', 'IV/b', '196908231993031001', 'juleta@gmail.com', '', 'uploads/profile_67ab270a62dd9.jpg', '2025-02-11 10:28:37', '2025-02-11 10:31:38', '$2y$10$bdux1fzpN4sOvS7Mk3zOZOxbTNRt7j0NS.NZlB41MINsRoL5bsRB2', 'Jl Kapten Muslim gg pribadi', '0895612167251');

-- --------------------------------------------------------

--
-- Table structure for table `guru_mapel`
--

CREATE TABLE `guru_mapel` (
  `id` int(11) NOT NULL,
  `nama_mapel` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guru_mapel`
--

INSERT INTO `guru_mapel` (`id`, `nama_mapel`) VALUES
(11, 'Kepala Sekolah'),
(12, 'Pendidikan Agama dan Budi Pekerti');

-- --------------------------------------------------------

--
-- Table structure for table `kelompok_mapel`
--

CREATE TABLE `kelompok_mapel` (
  `id` int(11) NOT NULL,
  `guru_mapel_id` int(11) NOT NULL,
  `nama_kelompok` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelompok_mapel`
--

INSERT INTO `kelompok_mapel` (`id`, `guru_mapel_id`, `nama_kelompok`) VALUES
(18, 11, 'Kepala Sekolah'),
(19, 12, 'Pendidikan Agama Islam');

-- --------------------------------------------------------

--
-- Table structure for table `petugas`
--

CREATE TABLE `petugas` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL DEFAULT 'petugas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `petugas`
--

INSERT INTO `petugas` (`id`, `username`, `password`, `role`) VALUES
(1, 'wahyuu@gmail.com', '$2y$10$Db6RxAx71.c/irAAUluay.WyYcznthMQkzIzWLkGh.v57e4OzX6me', 'petugas'),
(2, 'fauzan@gmail.com', '$2y$10$beDvHStZQI2ytP6wRzALH.kCHozmHUAaGx4SuiX8KHGivzYVMkMSG', 'petugas'),
(3, 'admin@smkn1.com', '$2y$10$SCjKS1x617OVukS2F1LLI.zNrWdTK5tVLkRd8/l4COcL/d4MdXjqW', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pelatihan`
--

CREATE TABLE `riwayat_pelatihan` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nama_pelatihan` varchar(255) DEFAULT NULL,
  `lama_pelatihan` varchar(50) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_berakhir` date DEFAULT NULL,
  `sertifikat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riwayat_pelatihan`
--

INSERT INTO `riwayat_pelatihan` (`id`, `email`, `nama_pelatihan`, `lama_pelatihan`, `tanggal_mulai`, `tanggal_berakhir`, `sertifikat`) VALUES
(1, 'juleta@gmail.com', 'Pembuatan WEB', '1 Bulan', '2025-01-01', '2025-02-01', 'uploads/sertifikat/1739271348_Bangkit(KM).pdf'),
(2, 'usman@gmail.com', 'Sertifikat SIC6', '1 Bulan', '2025-02-02', '2025-03-02', 'uploads/sertifikat/1739272832_Sertifikat SIC6.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `training_data`
--

CREATE TABLE `training_data` (
  `id` int(11) NOT NULL,
  `pattern` text NOT NULL,
  `response` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bahan_ajar`
--
ALTER TABLE `bahan_ajar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_guru_uploader` (`id_guru_uploader`);

--
-- Indexes for table `bank_soal`
--
ALTER TABLE `bank_soal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_guru_uploader` (`id_guru_uploader`);

--
-- Indexes for table `chat_log`
--
ALTER TABLE `chat_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- Indexes for table `guru_mapel`
--
ALTER TABLE `guru_mapel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kelompok_mapel`
--
ALTER TABLE `kelompok_mapel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_guru_mapel` (`guru_mapel_id`);

--
-- Indexes for table `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `riwayat_pelatihan`
--
ALTER TABLE `riwayat_pelatihan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `training_data`
--
ALTER TABLE `training_data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bahan_ajar`
--
ALTER TABLE `bahan_ajar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bank_soal`
--
ALTER TABLE `bank_soal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chat_log`
--
ALTER TABLE `chat_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `guru_mapel`
--
ALTER TABLE `guru_mapel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `kelompok_mapel`
--
ALTER TABLE `kelompok_mapel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `riwayat_pelatihan`
--
ALTER TABLE `riwayat_pelatihan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `training_data`
--
ALTER TABLE `training_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bahan_ajar`
--
ALTER TABLE `bahan_ajar`
  ADD CONSTRAINT `bahan_ajar_ibfk_1` FOREIGN KEY (`id_guru_uploader`) REFERENCES `guru` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bank_soal`
--
ALTER TABLE `bank_soal`
  ADD CONSTRAINT `bank_soal_ibfk_1` FOREIGN KEY (`id_guru_uploader`) REFERENCES `guru` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kelompok_mapel`
--
ALTER TABLE `kelompok_mapel`
  ADD CONSTRAINT `fk_guru_mapel` FOREIGN KEY (`guru_mapel_id`) REFERENCES `guru_mapel` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kelompok_mapel_ibfk_1` FOREIGN KEY (`guru_mapel_id`) REFERENCES `guru_mapel` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_pelatihan`
--
ALTER TABLE `riwayat_pelatihan`
  ADD CONSTRAINT `riwayat_pelatihan_ibfk_1` FOREIGN KEY (`email`) REFERENCES `guru` (`email`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
