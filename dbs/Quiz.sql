-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 15, 2026 at 06:50 AM
-- Server version: 8.0.44-0ubuntu0.24.04.2
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Quiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `hasil_test`
--

CREATE TABLE `hasil_test` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `paket_id` int NOT NULL,
  `skor` int NOT NULL DEFAULT '0',
  `benar` int NOT NULL DEFAULT '0',
  `salah` int NOT NULL DEFAULT '0',
  `kosong` int NOT NULL DEFAULT '0',
  `waktu_pengerjaan` int NOT NULL DEFAULT '0',
  `tanggal_test` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hasil_test`
--

INSERT INTO `hasil_test` (`id`, `user_id`, `paket_id`, `skor`, `benar`, `salah`, `kosong`, `waktu_pengerjaan`, `tanggal_test`) VALUES
(25, 12, 32, 5, 1, 1, 0, 17, '2025-12-03 15:32:01'),
(26, 12, 32, 5, 1, 1, 0, 13, '2025-12-03 15:32:58'),
(27, 12, 32, 5, 1, 1, 0, 517, '2025-12-03 15:41:41'),
(28, 12, 32, 5, 1, 0, 1, 600, '2025-12-03 15:57:03'),
(29, 12, 32, 5, 1, 1, 0, 13, '2025-12-03 15:57:50'),
(30, 12, 32, 5, 1, 1, 0, 18, '2025-12-03 15:58:17'),
(31, 12, 32, 0, 0, 0, 2, 600, '2025-12-03 15:58:43'),
(32, 12, 32, 5, 1, 1, 0, 29, '2025-12-03 15:59:39'),
(33, 12, 32, 10, 2, 0, 0, 11, '2025-12-03 16:00:18'),
(34, 12, 32, 10, 2, 0, 0, 10, '2025-12-03 16:01:52'),
(35, 12, 32, 10, 2, 0, 0, 255, '2025-12-06 05:33:25'),
(36, 12, 33, 5, 1, 0, 0, 7, '2025-12-06 05:46:22'),
(37, 12, 32, 15, 3, 0, 0, 29, '2025-12-11 06:34:25'),
(38, 13, 32, 50, 10, 1, 0, 85, '2025-12-12 12:29:08'),
(39, 12, 32, 25, 5, 6, 0, 124, '2025-12-13 03:25:18'),
(40, 12, 33, 5, 1, 0, 0, 14, '2025-12-18 00:37:44'),
(41, 12, 32, 55, 11, 0, 0, 229, '2025-12-22 06:24:15'),
(42, 12, 32, 0, 0, 0, 11, 600, '2025-12-27 01:51:32'),
(43, 12, 32, 0, 0, 0, 11, 600, '2025-12-27 02:05:12'),
(44, 12, 32, 0, 0, 0, 11, 600, '2025-12-27 02:17:59'),
(45, 12, 32, 0, 0, 0, 11, 600, '2025-12-27 02:41:34'),
(46, 12, 32, 0, 0, 1, 10, 58, '2026-01-03 02:49:14'),
(47, 12, 32, 50, 10, 1, 0, 128, '2026-01-03 02:51:31'),
(48, 12, 32, 0, 0, 0, 11, 600, '2026-01-03 02:54:56'),
(49, 12, 32, 0, 0, 0, 11, 217, '2026-01-05 07:45:14'),
(50, 12, 32, 10, 2, 1, 8, 343, '2026-01-08 06:20:41'),
(51, 12, 32, 20, 4, 7, 0, 282, '2026-01-08 06:33:17'),
(52, 12, 32, 5, 1, 0, 10, 29, '2026-01-08 06:36:13'),
(53, 12, 32, 5, 1, 0, 10, 81, '2026-01-08 06:36:18'),
(54, 12, 38, 5, 1, 0, 0, 27, '2026-01-08 06:40:32'),
(55, 12, 32, 0, 0, 0, 11, 120, '2026-01-08 06:42:46'),
(56, 12, 33, 0, 0, 0, 1, 6, '2026-01-08 06:43:21'),
(57, 12, 39, 5, 1, 0, 0, 20, '2026-01-08 06:48:11');

-- --------------------------------------------------------

--
-- Table structure for table `jawaban_detail`
--

CREATE TABLE `jawaban_detail` (
  `id` int NOT NULL,
  `hasil_test_id` int NOT NULL,
  `soal_id` int NOT NULL,
  `jawaban_user` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jawaban_detail`
--

INSERT INTO `jawaban_detail` (`id`, `hasil_test_id`, `soal_id`, `jawaban_user`, `is_correct`) VALUES
(96, 25, 117, 'A', 1),
(97, 25, 118, 'C', 0),
(98, 26, 117, 'A', 1),
(99, 26, 118, 'C', 0),
(100, 27, 118, 'D', 0),
(101, 27, 117, 'A', 1),
(102, 28, 118, 'A', 1),
(103, 28, 117, NULL, 0),
(104, 29, 117, 'A', 1),
(105, 29, 118, 'C', 0),
(106, 30, 117, 'A', 1),
(107, 30, 118, 'C', 0),
(108, 31, 117, NULL, 0),
(109, 31, 118, NULL, 0),
(110, 32, 118, 'C', 0),
(111, 32, 117, 'A', 1),
(112, 33, 117, 'A', 1),
(113, 33, 118, 'A', 1),
(114, 34, 117, 'A', 1),
(115, 34, 118, 'C', 1),
(116, 35, 117, 'A', 1),
(117, 35, 118, 'C', 1),
(118, 36, 119, 'A', 1),
(120, 37, 118, 'C', 1),
(121, 37, 117, 'A', 1),
(122, 38, 117, 'A', 1),
(123, 38, 128, 'C', 1),
(124, 38, 129, 'A', 1),
(125, 38, 127, 'B', 1),
(126, 38, 122, 'D', 1),
(127, 38, 123, 'A', 0),
(128, 38, 121, 'A', 1),
(129, 38, 118, 'C', 1),
(130, 38, 124, 'A', 1),
(131, 38, 126, 'A', 1),
(132, 38, 125, 'C', 1),
(133, 39, 122, 'D', 1),
(134, 39, 123, 'B', 1),
(135, 39, 117, 'A', 1),
(136, 39, 121, 'A', 1),
(137, 39, 129, 'D', 0),
(138, 39, 126, 'C', 0),
(139, 39, 125, 'C', 1),
(140, 39, 127, 'E', 0),
(141, 39, 128, 'D', 0),
(142, 39, 118, 'D', 0),
(143, 39, 124, 'D', 0),
(144, 40, 119, 'A', 1),
(145, 41, 118, 'C', 1),
(146, 41, 121, 'A', 1),
(147, 41, 125, 'C', 1),
(148, 41, 126, 'A', 1),
(149, 41, 129, 'A', 1),
(150, 41, 127, 'B', 1),
(151, 41, 124, 'A', 1),
(152, 41, 123, 'B', 1),
(153, 41, 128, 'C', 1),
(154, 41, 122, 'D', 1),
(155, 41, 117, 'A', 1),
(156, 42, 122, NULL, 0),
(157, 42, 125, NULL, 0),
(158, 42, 127, NULL, 0),
(159, 42, 129, NULL, 0),
(160, 42, 117, NULL, 0),
(161, 42, 124, NULL, 0),
(162, 42, 128, NULL, 0),
(163, 42, 118, NULL, 0),
(164, 42, 126, NULL, 0),
(165, 42, 121, NULL, 0),
(166, 42, 123, NULL, 0),
(167, 43, 126, NULL, 0),
(168, 43, 128, NULL, 0),
(169, 43, 123, NULL, 0),
(170, 43, 125, NULL, 0),
(171, 43, 117, NULL, 0),
(172, 43, 122, NULL, 0),
(173, 43, 124, NULL, 0),
(174, 43, 118, NULL, 0),
(175, 43, 129, NULL, 0),
(176, 43, 121, NULL, 0),
(177, 43, 127, NULL, 0),
(178, 44, 125, NULL, 0),
(179, 44, 127, NULL, 0),
(180, 44, 124, NULL, 0),
(181, 44, 128, NULL, 0),
(182, 44, 129, NULL, 0),
(183, 44, 123, NULL, 0),
(184, 44, 122, NULL, 0),
(185, 44, 121, NULL, 0),
(186, 44, 117, NULL, 0),
(187, 44, 126, NULL, 0),
(188, 44, 118, NULL, 0),
(189, 45, 121, NULL, 0),
(190, 45, 124, NULL, 0),
(191, 45, 123, NULL, 0),
(192, 45, 122, NULL, 0),
(193, 45, 117, NULL, 0),
(194, 45, 129, NULL, 0),
(195, 45, 126, NULL, 0),
(196, 45, 127, NULL, 0),
(197, 45, 118, NULL, 0),
(198, 45, 125, NULL, 0),
(199, 45, 128, NULL, 0),
(200, 46, 123, 'A', 0),
(201, 46, 126, NULL, 0),
(202, 46, 124, NULL, 0),
(203, 46, 118, NULL, 0),
(204, 46, 121, NULL, 0),
(205, 46, 129, NULL, 0),
(206, 46, 117, NULL, 0),
(207, 46, 122, NULL, 0),
(208, 46, 125, NULL, 0),
(209, 46, 127, NULL, 0),
(210, 46, 128, NULL, 0),
(211, 47, 117, 'A', 1),
(212, 47, 118, 'C', 1),
(213, 47, 123, 'A', 0),
(214, 47, 125, 'C', 1),
(215, 47, 121, 'A', 1),
(216, 47, 126, 'A', 1),
(217, 47, 127, 'B', 1),
(218, 47, 128, 'C', 1),
(219, 47, 122, 'D', 1),
(220, 47, 124, 'A', 1),
(221, 47, 129, 'A', 1),
(222, 48, 118, NULL, 0),
(223, 48, 121, NULL, 0),
(224, 48, 129, NULL, 0),
(225, 48, 123, NULL, 0),
(226, 48, 127, NULL, 0),
(227, 48, 122, NULL, 0),
(228, 48, 126, NULL, 0),
(229, 48, 128, NULL, 0),
(230, 48, 125, NULL, 0),
(231, 48, 117, NULL, 0),
(232, 48, 124, NULL, 0),
(233, 49, 121, NULL, 0),
(234, 49, 128, NULL, 0),
(235, 49, 127, NULL, 0),
(236, 49, 117, NULL, 0),
(237, 49, 129, NULL, 0),
(238, 49, 124, NULL, 0),
(239, 49, 118, NULL, 0),
(240, 49, 122, NULL, 0),
(241, 49, 123, NULL, 0),
(242, 49, 126, NULL, 0),
(243, 49, 125, NULL, 0),
(244, 50, 117, 'A', 1),
(245, 50, 129, NULL, 0),
(246, 50, 127, 'B', 1),
(247, 50, 125, NULL, 0),
(248, 50, 128, NULL, 0),
(249, 50, 118, NULL, 0),
(250, 50, 121, NULL, 0),
(251, 50, 124, NULL, 0),
(252, 50, 126, 'C', 0),
(253, 50, 123, NULL, 0),
(254, 50, 122, NULL, 0),
(255, 51, 124, 'A', 1),
(256, 51, 117, 'A', 1),
(257, 51, 126, 'B', 0),
(258, 51, 122, 'B', 0),
(259, 51, 127, 'C', 0),
(260, 51, 121, 'C', 0),
(261, 51, 128, 'C', 1),
(262, 51, 129, 'B', 0),
(263, 51, 118, 'C', 1),
(264, 51, 123, 'A', 0),
(265, 51, 125, 'D', 0),
(266, 52, 118, 'C', 1),
(267, 52, 128, NULL, 0),
(268, 52, 124, NULL, 0),
(269, 52, 125, NULL, 0),
(270, 52, 129, NULL, 0),
(271, 52, 123, NULL, 0),
(272, 52, 117, NULL, 0),
(273, 52, 121, NULL, 0),
(274, 52, 122, NULL, 0),
(275, 52, 127, NULL, 0),
(276, 52, 126, NULL, 0),
(277, 53, 129, 'A', 1),
(278, 53, 118, NULL, 0),
(279, 53, 127, NULL, 0),
(280, 53, 123, NULL, 0),
(281, 53, 126, NULL, 0),
(282, 53, 117, NULL, 0),
(283, 53, 125, NULL, 0),
(284, 53, 124, NULL, 0),
(285, 53, 128, NULL, 0),
(286, 53, 122, NULL, 0),
(287, 53, 121, NULL, 0),
(288, 54, 130, 'A', 1),
(289, 55, 122, NULL, 0),
(290, 55, 124, NULL, 0),
(291, 55, 123, NULL, 0),
(292, 55, 128, NULL, 0),
(293, 55, 126, NULL, 0),
(294, 55, 117, NULL, 0),
(295, 55, 121, NULL, 0),
(296, 55, 129, NULL, 0),
(297, 55, 118, NULL, 0),
(298, 55, 125, NULL, 0),
(299, 55, 127, NULL, 0),
(300, 56, 119, NULL, 0),
(301, 57, 132, 'A', 1);

-- --------------------------------------------------------

--
-- Table structure for table `paket_soal`
--

CREATE TABLE `paket_soal` (
  `id` int NOT NULL,
  `nama_paket` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `durasi_menit` int NOT NULL DEFAULT '90',
  `jumlah_soal` int NOT NULL DEFAULT '35',
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `paket_soal`
--

INSERT INTO `paket_soal` (`id`, `nama_paket`, `deskripsi`, `gambar`, `durasi_menit`, `jumlah_soal`, `status`, `created_at`) VALUES
(32, 'LINUX BASIC', 'Belajar linux', 'paket_1764775487_7706.png', 10, 10, 'aktif', '2025-12-03 15:24:47'),
(33, 'Network Enumeration', 'Belajar enumerasi service,port dan version yang bisa di exploitasi', 'paket_1764999659_1949.png', 10, 10, 'aktif', '2025-12-06 05:40:59'),
(34, 'Information Gathering - Web Edition', 'belajar bagaimana caranya mencari informasi terhadap target.', 'paket_1767407690_1111.png', 15, 20, 'aktif', '2026-01-03 02:34:50'),
(35, 'Password Attack', 'Belajar bagaimana memecahkan password target, crach hash & enkripsi \\r\\nseperti MD5,sha256 & sha512', 'paket_1767407835_9895.png', 20, 10, 'aktif', '2026-01-03 02:37:15'),
(36, 'Using the Metasploit Framework', 'penggunaan metasploit as a tools otomation attack', 'paket_1767408052_2218.png', 15, 10, 'aktif', '2026-01-03 02:40:52'),
(37, 'Introduction to Active Directory', 'penting untuk penetration tester untuk belajar bagaimana cara scanning active directory untuk mencari celah', 'paket_1767408177_8680.png', 15, 12, 'aktif', '2026-01-03 02:42:57'),
(38, 'php', 'aamaja', '', 10, 35, 'aktif', '2026-01-08 06:38:03'),
(39, 'bejsr php', 'sfsfsf', 'paket_1767854642_1498.png', 90, 35, 'aktif', '2026-01-08 06:44:02'),
(40, 'php 2', 'addaa', '', 100, 30, 'aktif', '2026-01-08 06:44:43');

-- --------------------------------------------------------

--
-- Table structure for table `soal`
--

CREATE TABLE `soal` (
  `id` int NOT NULL,
  `paket_id` int NOT NULL,
  `pertanyaan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pilihan_a` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pilihan_b` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pilihan_c` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pilihan_d` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pilihan_e` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `jawaban_benar` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pembahasan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `soal`
--

INSERT INTO `soal` (`id`, `paket_id`, `pertanyaan`, `pilihan_a`, `pilihan_b`, `pilihan_c`, `pilihan_d`, `pilihan_e`, `jawaban_benar`, `pembahasan`, `created_at`) VALUES
(117, 32, 'Apa itu linux ?', 'Sistem operasi', 'aplikasi', 'website', 'server', 'Aplikasi pengolah kata', 'A', 'Linux adalah sebuah\\r\\nsistem operasi (OS) yang bersifat open-source, yang artinya kode sumbernya bebas diakses, dimodifikasi, dan didistribusikan oleh siapa saja. Dibuat oleh Linus Torvalds pada tahun 1991, Linux berbasis Unix dan menjadi dasar bagi banyak distribusi atau varian sistem operasi, seperti Ubuntu atau Fedora.', '2025-12-03 15:28:48'),
(118, 32, 'Kernel dalam Linux berfungsi sebagai…', 'Antarmuka grafis untuk pengguna', 'Pengelola file dan folder', 'Penghubung antara perangkat keras dan perangkat lunak', 'Aplikasi manajemen jaringan', 'mengatur memori', 'C', 'Kernel adalah inti sistem operasi yang mengatur komunikasi antara software dan hardware.', '2025-12-03 15:30:40'),
(119, 33, 'Bagaimana cara scanning port jika kita berada di jaringan target menggunakan NMAP', 'nmap -p- ipTarger -sV -sC', 'nmap -O', 'nmap --min-rate -T5', 'sudo apt remove', 'nmap -sS -O', 'A', 'Ketika kita sudah berhasil masuk traget jaringan kiita,\\r\\nlangkah pertama yang perlu kita lakukan adalah mengumpulkan  informasi mengenai target kita(reconaisance)  salah satunya adalah scanning port', '2025-12-06 05:45:49'),
(121, 32, 'perintah ls biasanya di gunakan untuk?', 'listing file', 'ganti path', 'berpindah direktori', 'lihat privilege', 'hapus file', 'A', '-ls adalah salah satu perintah dasar yang perlu  kalian ketahui ketika ingin menggunakan sistem operasi linux, fungsinya untuk melakukan listing file yang ada di dalam current folder atau work directory kalian', '2025-12-11 06:40:41'),
(122, 32, 'ketika kalian pengen mengetahui sedang menggunakna user apa, perintah yang perlu kalian gunakan adalah', 'pwd', 'dir -a', 'ping', 'whoami', 'uname -a', 'D', 'whoami adalah perintah yang perlu kalian gunakan untuk mengetahui nama user yang sedah kalian gunakan.', '2025-12-11 06:44:08'),
(123, 32, 'Perintah untuk berpindah direktori adalah…', 'mv', 'cd', 'cp', 'mkdir', 'touch', 'B', 'cd (change directory) digunakan untuk menavigasi struktur direktori di Linux. Misalnya cd Documents untuk masuk ke folder Documents, atau cd .. untuk kembali ke direktori sebelumnya. Perintah ini sangat penting karena Linux berbasis terminal sangat mengandalkan navigasi direktori secara manual.', '2025-12-12 12:21:03'),
(124, 32, 'Perintah untuk membuat direktori baru adalah…', 'mkdir', 'rmdir', 'mkfile', 'newdir', 'createdir', 'A', 'mkdir (make directory) digunakan untuk membuat satu atau lebih folder baru. Misalnya mkdir project akan membuat folder bernama project. Linux tidak memiliki perintah seperti “newdir”, jadi mkdir adalah satu-satunya cara standar.', '2025-12-12 12:22:11'),
(125, 32, 'Perintah yang digunakan untuk menampilkan isi file teks adalah…', 'mv', 'nano', 'cat', 'chmod', 'sudo', 'C', 'cat (concatenate) digunakan untuk membaca dan menampilkan isi suatu file langsung di terminal. Selain melihat isi file, cat juga bisa digunakan untuk menggabungkan file atau membuat file baru. Sangat berguna untuk melihat konfigurasi atau skrip tanpa membuka editor.', '2025-12-12 12:23:07'),
(126, 32, 'Perintah yang digunakan untuk menghapus file adalah…', 'rm', 'del', 'erase', 'rmdir', 'clear', 'A', 'rm (remove) menghapus file atau direktori (dengan opsi tertentu seperti rm -r). Perintah ini tidak memindahkan file ke \\\"trash\\\", jadi penghapusan bersifat permanen. rmdir hanya menghapus direktori kosong, sehingga untuk file biasa perintah yang benar adalah rm.', '2025-12-12 12:24:08'),
(127, 32, 'Perintah yang digunakan untuk menyalin file adalah…', 'mv', 'cp', 'copy', 'dup', 'push', 'B', 'cp (copy) digunakan untuk menyalin file atau direktori. Misalnya cp file1.txt file2.txt. Untuk menyalin folder beserta isinya digunakan cp -r. Linux tidak menggunakan copy seperti di Windows, sehingga cp adalah perintah standar.', '2025-12-12 12:25:23'),
(128, 32, 'Fungsi utama dari perintah chmod adalah…', 'Mengubah kepemilikan file', 'Menampilkan proses berjalan', 'Mengubah izin akses file', 'Menjalankan script', 'Mengompres file', 'C', 'chmod (change mode) digunakan untuk mengatur permission file/direktori seperti hak baca (read), tulis (write), dan eksekusi (execute) bagi user, group, dan others. Contohnya chmod 755 script.sh mengatur agar file dapat dieksekusi. Sistem Linux sangat mengandalkan permission untuk keamanan.', '2025-12-12 12:26:22'),
(129, 32, 'Perintah yang digunakan untuk melihat proses yang sedang berjalan adalah…', 'top', 'run', 'exec', 'showproc', 'psrun', 'A', 'top menampilkan daftar proses aktif secara real-time, termasuk penggunaan CPU, RAM, dan PID (process ID). Ini berguna untuk troubleshooting, memonitor performa sistem, atau menemukan aplikasi yang membebani CPU.', '2025-12-12 12:27:08'),
(130, 38, 'ajsjabsabsags', 'a', 'aa', 'aa', 'aa', 'ad', 'A', 'ajcbasjcbhcvasc', '2026-01-08 06:39:00'),
(131, 38, 'sapa nama saya', 'ade', 'saikal', 'akbar', 'aaaaaaaa', 'aaa', 'A', 'benar saya adalah ade', '2026-01-08 06:45:48'),
(132, 39, 'asda', 'sadxasdada', 'dada', 'adadad', 'adad', 'adadad', 'A', 'daada', '2026-01-08 06:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `created_at`) VALUES
(9, 'andri', '$2y$10$apH/lZooOQXmWOiuPCVaeOWYmpL6QI2X1CeXhw.MErBGXwvYolzr6', 'andri', 'andri@ganteng.com', 'admin', '2025-11-21 11:12:34'),
(12, 'alice', '$2y$10$7n20gl0ykMYLbiEJV8dP1OsC9IGC9eKHW/76h5kn/f8qT.XmtcRxq', 'alice', 'alice@gmail.com', 'user', '2025-12-03 15:31:32'),
(13, 'alif', '$2y$10$Yu4j7xTHY3lRlCpkbLiL0OweNKTBw8Qo.Z21r.Lbx1n2c25S/w/KO', 'alif', 'alif@gmail.com', 'user', '2025-12-06 06:54:16'),
(14, 'ade ganteng', '$2y$10$0YtXPETUR9m5pHb9f26zAOrJQRO97XImq0nh0vOpH156W9kNrUAZi', 'ade ganteng', 'ade@gmail.com', 'user', '2025-12-29 08:41:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hasil_test`
--
ALTER TABLE `hasil_test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_paket` (`paket_id`);

--
-- Indexes for table `jawaban_detail`
--
ALTER TABLE `jawaban_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hasil_test_id` (`hasil_test_id`),
  ADD KEY `soal_id` (`soal_id`);

--
-- Indexes for table `paket_soal`
--
ALTER TABLE `paket_soal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `soal`
--
ALTER TABLE `soal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_paket` (`paket_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hasil_test`
--
ALTER TABLE `hasil_test`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `jawaban_detail`
--
ALTER TABLE `jawaban_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=302;

--
-- AUTO_INCREMENT for table `paket_soal`
--
ALTER TABLE `paket_soal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `soal`
--
ALTER TABLE `soal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hasil_test`
--
ALTER TABLE `hasil_test`
  ADD CONSTRAINT `hasil_test_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hasil_test_ibfk_2` FOREIGN KEY (`paket_id`) REFERENCES `paket_soal` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jawaban_detail`
--
ALTER TABLE `jawaban_detail`
  ADD CONSTRAINT `jawaban_detail_ibfk_1` FOREIGN KEY (`hasil_test_id`) REFERENCES `hasil_test` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jawaban_detail_ibfk_2` FOREIGN KEY (`soal_id`) REFERENCES `soal` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `soal`
--
ALTER TABLE `soal`
  ADD CONSTRAINT `soal_ibfk_1` FOREIGN KEY (`paket_id`) REFERENCES `paket_soal` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
