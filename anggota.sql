-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Okt 2025 pada 19.15
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `anggota`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_pendaftaran`
--

CREATE TABLE `log_pendaftaran` (
  `id_log` int(11) NOT NULL,
  `id_mhs` int(11) NOT NULL,
  `aksi` varchar(50) DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `log_pendaftaran`
--

INSERT INTO `log_pendaftaran` (`id_log`, `id_mhs`, `aksi`, `waktu`) VALUES
(39, 41, 'Pendaftaran baru', '2025-10-26 18:07:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id_mhs` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jk` enum('laki','perempuan','lainnya') NOT NULL,
  `id_prodi` int(11) NOT NULL,
  `catatan` text DEFAULT NULL,
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mahasiswa`
--

INSERT INTO `mahasiswa` (`id_mhs`, `nama`, `jk`, `id_prodi`, `catatan`, `tanggal_daftar`) VALUES
(41, 'Reinnent Rasika Zahrain', 'laki', 1, '', '2025-10-26 18:07:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa_minat`
--

CREATE TABLE `mahasiswa_minat` (
  `id_mhs` int(11) NOT NULL,
  `id_minat` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mahasiswa_minat`
--

INSERT INTO `mahasiswa_minat` (`id_mhs`, `id_minat`) VALUES
(41, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `minat`
--

CREATE TABLE `minat` (
  `id_minat` int(11) NOT NULL,
  `kode_minat` char(5) NOT NULL,
  `nama_minat` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `minat`
--

INSERT INTO `minat` (`id_minat`, `kode_minat`, `nama_minat`) VALUES
(1, 'ai', 'Kecerdasan Buatan'),
(2, 'web', 'Pengembangan Web'),
(3, 'iot', 'Internet of Things');

-- --------------------------------------------------------

--
-- Struktur dari tabel `prodi`
--

CREATE TABLE `prodi` (
  `id_prodi` int(11) NOT NULL,
  `kode_prodi` char(2) NOT NULL,
  `nama_prodi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `prodi`
--

INSERT INTO `prodi` (`id_prodi`, `kode_prodi`, `nama_prodi`) VALUES
(1, 'ti', 'Teknik Informatika'),
(2, 'si', 'Sistem Informasi'),
(3, 'te', 'Teknik Elektro');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `log_pendaftaran`
--
ALTER TABLE `log_pendaftaran`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_mhs` (`id_mhs`);

--
-- Indeks untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id_mhs`),
  ADD KEY `fk_mahasiswa_prodi` (`id_prodi`);

--
-- Indeks untuk tabel `mahasiswa_minat`
--
ALTER TABLE `mahasiswa_minat`
  ADD PRIMARY KEY (`id_mhs`,`id_minat`),
  ADD KEY `id_minat` (`id_minat`);

--
-- Indeks untuk tabel `minat`
--
ALTER TABLE `minat`
  ADD PRIMARY KEY (`id_minat`),
  ADD UNIQUE KEY `kode_minat` (`kode_minat`);

--
-- Indeks untuk tabel `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`id_prodi`),
  ADD UNIQUE KEY `kode_prodi` (`kode_prodi`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `log_pendaftaran`
--
ALTER TABLE `log_pendaftaran`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id_mhs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `minat`
--
ALTER TABLE `minat`
  MODIFY `id_minat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `prodi`
--
ALTER TABLE `prodi`
  MODIFY `id_prodi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `log_pendaftaran`
--
ALTER TABLE `log_pendaftaran`
  ADD CONSTRAINT `log_pendaftaran_ibfk_1` FOREIGN KEY (`id_mhs`) REFERENCES `mahasiswa` (`id_mhs`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `fk_mahasiswa_prodi` FOREIGN KEY (`id_prodi`) REFERENCES `prodi` (`id_prodi`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mahasiswa_minat`
--
ALTER TABLE `mahasiswa_minat`
  ADD CONSTRAINT `mahasiswa_minat_ibfk_1` FOREIGN KEY (`id_mhs`) REFERENCES `mahasiswa` (`id_mhs`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mahasiswa_minat_ibfk_2` FOREIGN KEY (`id_minat`) REFERENCES `minat` (`id_minat`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
