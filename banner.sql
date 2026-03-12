-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Mar 2026 pada 02.21
-- Versi server: 10.4.27-MariaDB
-- Versi PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bannerposko`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tipe` enum('gambar','url','video') NOT NULL DEFAULT 'gambar',
  `url` varchar(500) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `durasi` int(11) NOT NULL DEFAULT 5,
  `jadwal_mulai` datetime DEFAULT NULL,
  `jadwal_selesai` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `banner`
--

INSERT INTO `banner` (`id`, `gambar`, `tipe`, `url`, `status`, `durasi`, `jadwal_mulai`, `jadwal_selesai`, `updated_at`) VALUES
(26, 'banner_1773123973.png', 'gambar', NULL, 'aktif', 5, '2026-03-10 13:26:00', '2026-03-13 13:26:00', '2026-03-10 06:26:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `kunci` varchar(100) NOT NULL,
  `nilai` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `setting`
--

INSERT INTO `setting` (`id`, `kunci`, `nilai`) VALUES
(1, 'running_text', 'Selamat datang di Bandara Soekarno Hatta'),
(2, 'rt_font', 'sans-serif'),
(3, 'rt_size', '20'),
(4, 'rt_speed', '15'),
(5, 'rt_bg_type', 'transparent'),
(6, 'rt_bg_color', '#ffffff'),
(7, 'rt_bg_blur', '0'),
(8, 'rt_color', '#ffffff'),
(9, 'dt_font', 'sans-serif'),
(10, 'dt_size', '20'),
(11, 'dt_jam_type', 'HH:MM'),
(12, 'dt_bg_type', 'transparent'),
(13, 'dt_bg_color', '#000000'),
(14, 'dt_bg_blur', '0'),
(15, 'dt_color', '#ffffff'),
(16, 'bar_bg_type', 'transparent'),
(17, 'bar_bg_color', '#000000'),
(18, 'bar_bg_blur', '8'),
(19, 'slider_interval', '5');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `nama_tampilan` varchar(100) DEFAULT NULL,
  `foto_profil` varchar(200) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `nama_tampilan`, `foto_profil`, `password`, `created_at`) VALUES
(2, 'admin', 'POSKO', 'profil_2_1773030999.jpg', 'e00cf25ad42683b3df678c61f42c6bda', '2026-03-05 02:15:56');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
