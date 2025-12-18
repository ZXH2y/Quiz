

--
-- Struktur dari tabel `hasil_test`
--

CREATE TABLE `hasil_test` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `paket_id` int(11) NOT NULL,
  `skor` int(11) NOT NULL DEFAULT 0,
  `benar` int(11) NOT NULL DEFAULT 0,
  `salah` int(11) NOT NULL DEFAULT 0,
  `kosong` int(11) NOT NULL DEFAULT 0,
  `waktu_pengerjaan` int(11) NOT NULL DEFAULT 0,
  `tanggal_test` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `hasil_test`
--

INSERT INTO `hasil_test` (`id`, `user_id`, `paket_id`, `skor`, `benar`, `salah`, `kosong`, `waktu_pengerjaan`, `tanggal_test`) VALUES
(1, 3, 1, 5, 1, 0, 34, 43, '2025-11-15 14:38:46'),
(2, 3, 5, 5, 1, 0, 0, 17, '2025-11-16 04:10:55'),
(3, 4, 5, 5, 1, 0, 0, 7, '2025-11-16 04:35:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jawaban_detail`
--

CREATE TABLE `jawaban_detail` (
  `id` int(11) NOT NULL,
  `hasil_test_id` int(11) NOT NULL,
  `soal_id` int(11) NOT NULL,
  `jawaban_user` char(1) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jawaban_detail`
--

INSERT INTO `jawaban_detail` (`id`, `hasil_test_id`, `soal_id`, `jawaban_user`, `is_correct`) VALUES
(3, 1, 23, NULL, 0),
(4, 1, 10, NULL, 0),
(6, 1, 27, NULL, 0),
(9, 1, 5, NULL, 0),
(10, 1, 24, NULL, 0),
(11, 1, 3, NULL, 0),
(12, 1, 20, NULL, 0),
(13, 1, 11, NULL, 0),
(14, 1, 13, NULL, 0),
(15, 1, 29, NULL, 0),
(16, 1, 25, NULL, 0),
(17, 1, 30, NULL, 0),
(18, 1, 22, NULL, 0),
(19, 1, 12, NULL, 0),
(20, 1, 19, NULL, 0),
(21, 1, 7, NULL, 0),
(22, 1, 2, NULL, 0),
(23, 1, 28, NULL, 0),
(24, 1, 21, NULL, 0),
(25, 1, 8, NULL, 0),
(26, 1, 4, NULL, 0),
(27, 1, 26, NULL, 0),
(28, 1, 17, NULL, 0),
(29, 1, 9, NULL, 0),
(30, 1, 14, NULL, 0),
(31, 1, 15, NULL, 0),
(32, 1, 1, NULL, 0),
(33, 1, 18, NULL, 0),
(34, 1, 6, NULL, 0),
(35, 1, 16, NULL, 0),
(36, 2, 111, 'A', 1),
(37, 3, 111, 'A', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `paket_soal`
--

CREATE TABLE `paket_soal` (
  `id` int(11) NOT NULL,
  `nama_paket` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `durasi_menit` int(11) NOT NULL DEFAULT 90,
  `jumlah_soal` int(11) NOT NULL DEFAULT 35,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `paket_soal`
--

INSERT INTO `paket_soal` (`id`, `nama_paket`, `deskripsi`, `durasi_menit`, `jumlah_soal`, `status`, `created_at`) VALUES
(1, 'TWK - Tes Wawasan Kebangsaan', 'Materi Pancasila, UUD 1945, NKRI, dan Bhinneka Tunggal Ika', 90, 35, 'aktif', '2025-11-15 06:44:31'),
(2, 'TIU - Tes Intelegensi Umum', 'Materi verbal, numerik, dan logika', 90, 35, 'aktif', '2025-11-15 06:44:31'),
(3, 'TKP - Tes Karakteristik Pribadi', 'Materi pelayanan publik dan integritas', 90, 40, 'aktif', '2025-11-15 06:44:31'),
(4, 'paket 1', 'paket ini 110 soal', 110, 110, 'aktif', '2025-11-16 03:51:36'),
(5, 'paket 2', '110 soal', 110, 110, 'aktif', '2025-11-16 04:07:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `soal`
--

CREATE TABLE `soal` (
  `id` int(11) NOT NULL,
  `paket_id` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `pilihan_a` text NOT NULL,
  `pilihan_b` text NOT NULL,
  `pilihan_c` text NOT NULL,
  `pilihan_d` text NOT NULL,
  `pilihan_e` text NOT NULL,
  `jawaban_benar` char(1) NOT NULL,
  `pembahasan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `soal`
--

INSERT INTO `soal` (`id`, `paket_id`, `pertanyaan`, `pilihan_a`, `pilihan_b`, `pilihan_c`, `pilihan_d`, `pilihan_e`, `jawaban_benar`, `pembahasan`, `created_at`) VALUES
(1, 1, 'Pancasila sebagai dasar negara Indonesia tercantum dalam...', 'UUD 1945 Pasal 1 ayat 1', 'Pembukaan UUD 1945 alinea 4', 'Batang Tubuh UUD 1945', 'Tap MPR No. III/MPR/2000', 'Ketetapan PPKI', 'B', 'Pancasila tercantum dalam Pembukaan UUD 1945 alinea keempat', '2025-11-15 06:44:31'),
(2, 1, 'Semboyan Bhinneka Tunggal Ika berasal dari kitab...', 'Negarakertagama', 'Sutasoma', 'Arjunawiwaha', 'Bharatayudha', 'Pararaton', 'B', 'Berasal dari Kitab Sutasoma karya Mpu Tantular', '2025-11-15 06:44:31'),
(3, 1, 'Bentuk negara Indonesia adalah...', 'Serikat', 'Federal', 'Kesatuan', 'Konfederasi', 'Monarki', 'C', 'Pasal 1 ayat 1 UUD 1945: Negara Kesatuan berbentuk Republik', '2025-11-15 06:44:31'),
(4, 1, 'Lembaga yang berwenang mengubah UUD adalah...', 'DPR', 'MPR', 'Presiden', 'MA', 'MK', 'B', 'MPR berwenang mengubah dan menetapkan UUD', '2025-11-15 06:44:31'),
(5, 1, 'Sila ketiga Pancasila adalah...', 'Ketuhanan Yang Maha Esa', 'Kemanusiaan yang adil dan beradab', 'Persatuan Indonesia', 'Kerakyatan yang dipimpin hikmat', 'Keadilan sosial', 'C', 'Sila ke-3: Persatuan Indonesia', '2025-11-15 06:44:31'),
(6, 1, 'Asas pemilu di Indonesia adalah...', 'Langsung, Umum, Bebas, Rahasia', 'LUBER JURDIL', 'Bebas, Adil, Transparan', 'Demokratis', 'Musyawarah', 'B', 'Asas Pemilu: Langsung, Umum, Bebas, Rahasia, Jujur, Adil', '2025-11-15 06:44:31'),
(7, 1, 'Presiden dan Wakil Presiden dipilih secara...', 'Langsung oleh rakyat', 'Melalui MPR', 'Melalui DPR', 'Ditunjuk partai', 'Bertingkat', 'A', 'Pasal 6A: dipilih langsung oleh rakyat', '2025-11-15 06:44:31'),
(8, 1, 'Lambang negara Indonesia adalah...', 'Burung Elang', 'Garuda Pancasila', 'Rajawali', 'Cenderawasih', 'Merak', 'B', 'Garuda Pancasila adalah lambang negara', '2025-11-15 06:44:31'),
(9, 1, 'Lagu kebangsaan Indonesia adalah...', 'Indonesia Pusaka', 'Garuda Pancasila', 'Indonesia Raya', 'Satu Nusa Satu Bangsa', 'Rayuan Pulau Kelapa', 'C', 'Indonesia Raya ciptaan W.R. Supratman', '2025-11-15 06:44:31'),
(10, 1, 'Hari Kesaktian Pancasila diperingati tanggal...', '1 Juni', '17 Agustus', '1 Oktober', '10 November', '28 Oktober', 'C', 'Diperingati setiap 1 Oktober', '2025-11-15 06:44:31'),
(11, 1, 'Wawasan Nusantara adalah cara pandang tentang...', 'Wilayah geografis', 'Diri dan lingkungan', 'Politik luar negeri', 'Ekonomi nasional', 'Pertahanan', 'B', 'Cara pandang bangsa tentang diri dan lingkungan', '2025-11-15 06:44:31'),
(12, 1, 'Otonomi daerah diatur dalam...', 'UU No. 23 Tahun 2014', 'UU No. 32 Tahun 2004', 'UU No. 22 Tahun 1999', 'UUD 1945 Pasal 18', 'Perpres', 'A', 'UU No. 23 Tahun 2014 tentang Pemerintahan Daerah', '2025-11-15 06:44:31'),
(13, 1, 'Sistem pemerintahan Indonesia adalah...', 'Parlementer', 'Presidensial', 'Semi Presidensial', 'Monarki', 'Liberal', 'B', 'Sistem presidensial', '2025-11-15 06:44:31'),
(14, 1, 'Kedaulatan berada di tangan rakyat dan dilaksanakan menurut...', 'Undang-Undang Dasar', 'Keinginan rakyat', 'Keputusan MPR', 'Musyawarah', 'Voting', 'A', 'Pasal 1 ayat 2 UUD 1945', '2025-11-15 06:44:31'),
(15, 1, 'Proklamasi kemerdekaan dibacakan tanggal...', '17 Agustus 1945', '18 Agustus 1945', '1 Juni 1945', '16 Agustus 1945', '19 Agustus 1945', 'A', 'Proklamasi 17 Agustus 1945', '2025-11-15 06:44:31'),
(16, 1, 'Pasal 28 UUD 1945 mengatur tentang...', 'Kewajiban warga negara', 'Hak asasi manusia', 'Sistem pemerintahan', 'Pemilu', 'Lembaga negara', 'B', 'Pasal 28 dan Bab XA: HAM', '2025-11-15 06:44:31'),
(17, 1, 'Makna sila pertama Pancasila adalah...', 'Percaya Tuhan', 'Menghormati agama lain', 'Beribadah sesuai agama', 'Semua benar', 'A dan B', 'D', 'Ketuhanan Yang Maha Esa mencakup semua aspek', '2025-11-15 06:44:31'),
(18, 1, 'Jumlah provinsi di Indonesia saat ini...', '34 provinsi', '35 provinsi', '36 provinsi', '37 provinsi', '38 provinsi', 'E', 'Saat ini 38 provinsi', '2025-11-15 06:44:31'),
(19, 1, 'Ibukota Negara Indonesia adalah...', 'Jakarta', 'Nusantara', 'Jakarta dan Nusantara', 'Yogyakarta', 'Bandung', 'C', 'Transisi Jakarta ke Nusantara', '2025-11-15 06:44:31'),
(20, 1, 'Mata uang Indonesia adalah...', 'Rupiah', 'Dollar', 'Ringgit', 'Bath', 'Peso', 'A', 'Rupiah adalah mata uang resmi', '2025-11-15 06:44:31'),
(21, 1, 'Lembaga pemeriksa keuangan negara adalah...', 'KPK', 'BPK', 'Kejaksaan', 'Kepolisian', 'BPKP', 'B', 'BPK: Badan Pemeriksa Keuangan', '2025-11-15 06:44:31'),
(22, 1, 'Hak dan kewajiban warga negara diatur pasal...', 'Pasal 27-28', 'Pasal 27-34', 'Pasal 25-30', 'Pasal 28-35', 'Pasal 30-36', 'B', 'UUD 1945 Pasal 27-34', '2025-11-15 06:44:31'),
(23, 1, 'Fungsi DPR adalah...', 'Legislasi', 'Anggaran', 'Pengawasan', 'Semua benar', 'A dan B', 'D', 'DPR: Legislasi, Anggaran, Pengawasan', '2025-11-15 06:44:31'),
(24, 1, 'Mahkamah Konstitusi berwenang...', 'Menguji UU terhadap UUD', 'Memutus sengketa pemilu', 'Membubarkan parpol', 'Semua benar', 'A dan B', 'D', 'MK memiliki semua kewenangan tersebut', '2025-11-15 06:44:31'),
(25, 1, 'Sistem hukum Indonesia berdasar...', 'Hukum adat', 'Hukum Islam', 'Hukum barat', 'Pancasila dan UUD 1945', 'Hukum internasional', 'D', 'Berdasar Pancasila dan UUD 1945', '2025-11-15 06:44:31'),
(26, 1, 'Pancasila pertama kali dirumuskan oleh...', 'Soekarno', 'Hatta', 'Soepomo', 'Muhammad Yamin', 'Ahmad Soebardjo', 'A', 'Soekarno pada 1 Juni 1945', '2025-11-15 06:44:31'),
(27, 1, 'NKRI berdiri atas dasar...', 'Bhinneka Tunggal Ika', 'Pancasila', 'UUD 1945', 'Gotong royong', 'Musyawarah', 'B', 'Pancasila sebagai dasar negara', '2025-11-15 06:44:31'),
(28, 1, 'Pemilu pertama Indonesia tahun...', '1950', '1955', '1965', '1971', '1977', 'B', 'Pemilu pertama tahun 1955', '2025-11-15 06:44:31'),
(29, 1, 'Wilayah Sabang sampai Merauke mencerminkan...', 'Geografis', 'Persatuan dan kesatuan', 'Kekayaan alam', 'Keberagaman', 'Luas wilayah', 'B', 'Mencerminkan persatuan NKRI', '2025-11-15 06:44:31'),
(30, 1, 'TNI berperan di bidang...', 'Pertahanan', 'Keamanan', 'Hukum', 'Politik', 'Ekonomi', 'A', 'TNI: pertahanan, Polri: keamanan', '2025-11-15 06:44:31'),
(36, 2, '2, 4, 8, 16, ... Bilangan selanjutnya', '24', '28', '32', '36', '40', 'C', 'Pola x2: 16 x 2 = 32', '2025-11-15 06:44:31'),
(37, 2, '3, 6, 12, 24, ... Bilangan selanjutnya', '36', '48', '52', '56', '60', 'B', 'Pola x2: 24 x 2 = 48', '2025-11-15 06:44:31'),
(38, 2, 'Jika A=1, B=2, C=3, maka BACA = ...', '2131', '2132', '2141', '2231', '3231', 'A', 'B=2, A=1, C=3, A=1 = 2131', '2025-11-15 06:44:31'),
(39, 2, '5, 10, 20, 40, ... Bilangan selanjutnya', '60', '70', '80', '90', '100', 'C', 'Pola x2: 40 x 2 = 80', '2025-11-15 06:44:31'),
(40, 2, 'RUMAH : TEMBOK = POHON : ...', 'Daun', 'Akar', 'Batang', 'Buah', 'Dahan', 'C', 'Struktur utama rumah adalah tembok, pohon adalah batang', '2025-11-15 06:44:31'),
(41, 2, 'Semua mahasiswa adalah pelajar. Budi mahasiswa. Maka...', 'Budi pelajar', 'Budi bukan pelajar', 'Budi mungkin pelajar', 'Tidak disimpulkan', 'Salah', 'A', 'Silogisme: Budi pasti pelajar', '2025-11-15 06:44:31'),
(42, 2, '1/2 + 1/4 = ...', '1/6', '2/6', '3/6', '3/4', '1/8', 'D', '2/4 + 1/4 = 3/4', '2025-11-15 06:44:31'),
(43, 2, 'Jika hari ini Senin, 100 hari lagi...', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'B', '100 hari = 14 minggu + 2 hari = Rabu', '2025-11-15 06:44:31'),
(44, 2, '25% dari 200 adalah...', '25', '50', '75', '100', '125', 'B', '25% x 200 = 50', '2025-11-15 06:44:31'),
(45, 2, 'Antonim KERAS adalah...', 'Kuat', 'Lemah', 'Lembut', 'Tegas', 'Kokoh', 'C', 'Lawan kata keras adalah lembut', '2025-11-15 06:44:31'),
(46, 2, 'MOBIL : RODA = KOMPUTER : ...', 'Mouse', 'Keyboard', 'Monitor', 'Processor', 'Speaker', 'D', 'Komponen inti mobil roda, komputer processor', '2025-11-15 06:44:31'),
(47, 2, 'Jika x + 5 = 12, maka x = ...', '5', '6', '7', '8', '9', 'C', 'x = 12 - 5 = 7', '2025-11-15 06:44:31'),
(48, 2, '2³ × 2² = ...', '16', '32', '64', '128', '256', 'B', '2⁵ = 32 atau 8 x 4 = 32', '2025-11-15 06:44:31'),
(49, 2, 'Sinonim CERDAS adalah...', 'Bodoh', 'Pintar', 'Rajin', 'Tekun', 'Malas', 'B', 'Persamaan kata cerdas = pintar', '2025-11-15 06:44:31'),
(50, 2, 'Jika 3x = 15, maka x = ...', '3', '4', '5', '6', '7', 'C', 'x = 15 ÷ 3 = 5', '2025-11-15 06:44:31'),
(51, 2, 'DOKTER : RS = GURU : ...', 'Buku', 'Sekolah', 'Murid', 'Kelas', 'Papan tulis', 'B', 'Tempat kerja dokter RS, guru sekolah', '2025-11-15 06:44:31'),
(52, 2, 'Rata-rata 4, 6, 8, 10 adalah...', '6', '7', '8', '9', '10', 'B', '(4+6+8+10) ÷ 4 = 7', '2025-11-15 06:44:31'),
(53, 2, '√144 = ...', '10', '11', '12', '13', '14', 'C', '12 x 12 = 144', '2025-11-15 06:44:31'),
(54, 2, 'Jika P>Q dan Q>R, maka...', 'P<R', 'P=R', 'P>R', 'P≤R', 'Tidak tentu', 'C', 'Transitif: P > R', '2025-11-15 06:44:31'),
(55, 2, '0, 1, 1, 2, 3, 5, 8, ... Selanjutnya', '11', '12', '13', '14', '15', 'C', 'Fibonacci: 5 + 8 = 13', '2025-11-15 06:44:31'),
(56, 2, 'Antonim OPTIMIS adalah...', 'Pesimis', 'Realis', 'Idealis', 'Positif', 'Negatif', 'A', 'Lawan optimis = pesimis', '2025-11-15 06:44:31'),
(57, 2, '60% dari 150 adalah...', '70', '80', '90', '100', '110', 'C', '60% x 150 = 90', '2025-11-15 06:44:31'),
(58, 2, 'AIR : HAUS = MAKANAN : ...', 'Kenyang', 'Lapar', 'Makan', 'Tidur', 'Minum', 'B', 'Air hilangkan haus, makanan hilangkan lapar', '2025-11-15 06:44:31'),
(59, 2, 'Jika 2x + 3 = 11, maka x = ...', '2', '3', '4', '5', '6', 'C', '2x = 8, x = 4', '2025-11-15 06:44:31'),
(60, 2, '1, 4, 9, 16, 25, ... Selanjutnya', '30', '32', '34', '36', '38', 'D', 'Kuadrat: 6² = 36', '2025-11-15 06:44:31'),
(61, 2, 'BUKU : BACA = PIANO : ...', 'Nyanyi', 'Dengar', 'Main', 'Tiup', 'Pukul', 'C', 'Buku dibaca, piano dimainkan', '2025-11-15 06:44:31'),
(62, 2, '10, 20, 30, 40, ... Selanjutnya', '45', '50', '55', '60', '65', 'B', 'Pola +10: 40 + 10 = 50', '2025-11-15 06:44:31'),
(63, 2, 'Sinonim RAJIN adalah...', 'Malas', 'Tekun', 'Cerdas', 'Bodoh', 'Lemah', 'B', 'Persamaan rajin = tekun', '2025-11-15 06:44:31'),
(64, 2, '1/3 + 1/6 = ...', '1/2', '2/6', '1/9', '2/9', '3/9', 'A', '2/6 + 1/6 = 3/6 = 1/2', '2025-11-15 06:44:31'),
(65, 2, 'AYAH : IBU = LAKI-LAKI : ...', 'Anak', 'Perempuan', 'Kakak', 'Adik', 'Saudara', 'B', 'Pasangan: ayah-ibu, laki-laki-perempuan', '2025-11-15 06:44:31'),
(66, 2, 'Jika x - 3 = 7, maka x = ...', '8', '9', '10', '11', '12', 'C', 'x = 7 + 3 = 10', '2025-11-15 06:44:31'),
(67, 2, '100 ÷ 4 = ...', '20', '25', '30', '35', '40', 'B', '100 ÷ 4 = 25', '2025-11-15 06:44:31'),
(68, 2, 'PANAS : DINGIN = TINGGI : ...', 'Rendah', 'Pendek', 'Panjang', 'Besar', 'Kecil', 'A', 'Antonim: panas-dingin, tinggi-rendah', '2025-11-15 06:44:31'),
(69, 2, '5 + 5 × 5 = ...', '50', '40', '35', '30', '25', 'D', 'Operasi perkalian dulu: 5 + 25 = 30', '2025-11-15 06:44:31'),
(70, 2, 'Sinonim INDAH adalah...', 'Buruk', 'Jelek', 'Cantik', 'Kotor', 'Rusak', 'C', 'Persamaan indah = cantik', '2025-11-15 06:44:31'),
(71, 3, 'Ketika mendapat tugas yang sulit, saya...', 'Menghindari', 'Menunda', 'Minta bantuan segera', 'Coba sendiri dulu', 'Serahkan ke orang lain', 'D', 'Menunjukkan inisiatif dan kemandirian', '2025-11-15 06:44:31'),
(72, 3, 'Rekan kerja melakukan kesalahan, saya...', 'Diam saja', 'Laporkan atasan', 'Tegur dengan bijak', 'Kritik di depan umum', 'Abaikan', 'C', 'Menegur dengan bijak menunjukkan kepedulian', '2025-11-15 06:44:31'),
(73, 3, 'Saya bekerja paling baik ketika...', 'Sendirian', 'Dalam tim', 'Dibawah tekanan', 'Tanpa aturan', 'Diawasi ketat', 'B', 'Kerja tim efektif untuk ASN', '2025-11-15 06:44:31'),
(74, 3, 'Ketika ada konflik dengan rekan, saya...', 'Hindari', 'Selesaikan dengan diskusi', 'Laporkan atasan', 'Balas dendam', 'Diam saja', 'B', 'Menyelesaikan dengan komunikasi baik', '2025-11-15 06:44:31'),
(75, 3, 'Target pekerjaan tidak tercapai karena...', 'Tugas terlalu berat', 'Waktu kurang', 'Evaluasi dan perbaiki', 'Salah orang lain', 'Sistem buruk', 'C', 'Introspeksi dan perbaikan diri', '2025-11-15 06:44:31'),
(76, 3, 'Atasan memberi tugas di luar job desk, saya...', 'Tolak', 'Terima dengan ikhlas', 'Komplain', 'Kerjakan asal-asalan', 'Lempar ke orang lain', 'B', 'Fleksibilitas dan dedikasi', '2025-11-15 06:44:31'),
(77, 3, 'Masyarakat komplain pelayanan, saya...', 'Abaikan', 'Dengarkan dan perbaiki', 'Salahkan sistem', 'Marah-marah', 'Suruh lapor atasan', 'B', 'Pelayanan prima dan perbaikan berkelanjutan', '2025-11-15 06:44:31'),
(78, 3, 'Ketika ada ide inovatif, saya...', 'Simpan sendiri', 'Usulkan ke atasan', 'Takut salah', 'Tunggu diminta', 'Ragu-ragu', 'B', 'Proaktif dan inovatif', '2025-11-15 06:44:31'),
(79, 3, 'Rekan meminta bantuan saat sibuk, saya...', 'Tolak', 'Bantu setelah selesai', 'Abaikan', 'Marah-marah', 'Suruh cari orang lain', 'B', 'Kerjasama dengan time management', '2025-11-15 06:44:31'),
(80, 3, 'Mendapat kritik dari atasan, saya...', 'Tersinggung', 'Terima dan perbaiki', 'Membela diri', 'Marah', 'Diam kesal', 'B', 'Menerima kritik untuk perbaikan', '2025-11-15 06:44:31'),
(81, 3, 'Pekerjaan menumpuk, prioritas saya...', 'Kerjakan semua sekaligus', 'Pilih yang penting dulu', 'Panik', 'Minta perpanjang deadline', 'Lempar ke orang lain', 'B', 'Manajemen prioritas yang baik', '2025-11-15 06:44:31'),
(82, 3, 'Ada kesempatan pelatihan, saya...', 'Tidak tertarik', 'Ikut dengan antusias', 'Malas', 'Lihat-lihat dulu', 'Suruh orang lain', 'B', 'Semangat belajar dan pengembangan diri', '2025-11-15 06:44:31'),
(83, 3, 'Ketika lelah bekerja, saya...', 'Berhenti total', 'Istirahat sebentar lalu lanjut', 'Komplain terus', 'Bolos', 'Pura-pura sakit', 'B', 'Manajemen stamina yang baik', '2025-11-15 06:44:31'),
(84, 3, 'Rekan promosi lebih dulu, saya...', 'Iri', 'Ucapkan selamat', 'Dengki', 'Cari kesalahan dia', 'Komplain ke atasan', 'B', 'Sportivitas dan kebesaran hati', '2025-11-15 06:44:31'),
(85, 3, 'Info rahasia kantor, saya...', 'Sebarkan', 'Jaga kerahasiaan', 'Cerita ke teman dekat', 'Jual ke pihak lain', 'Posting media sosial', 'B', 'Menjaga kerahasiaan dan integritas', '2025-11-15 06:44:31'),
(86, 3, 'Ketika salah, saya...', 'Menyalahkan orang lain', 'Mengakui dan minta maaf', 'Tutup-tutupi', 'Cari pembenaran', 'Lari dari tanggung jawab', 'B', 'Integritas dan tanggung jawab', '2025-11-15 06:44:31'),
(87, 3, 'Jadwal kerja berubah mendadak, saya...', 'Menolak', 'Menyesuaikan diri', 'Marah-marah', 'Komplain', 'Bolos', 'B', 'Fleksibilitas dan adaptasi', '2025-11-15 06:44:31'),
(88, 3, 'Melihat rekan berbuat curang, saya...', 'Ikut-ikutan', 'Tegur dan laporkan', 'Diam saja', 'Manfaatkan untuk diri', 'Pura-pura tidak tahu', 'B', 'Integritas dan keberanian moral', '2025-11-15 06:44:31'),
(89, 3, 'Target pribadi vs target tim bertentangan, saya...', 'Utamakan pribadi', 'Utamakan tim', 'Abaikan keduanya', 'Konflik dengan tim', 'Keluar dari tim', 'B', 'Kepentingan bersama di atas pribadi', '2025-11-15 06:44:31'),
(90, 3, 'Banyak pekerjaan di akhir jam kerja, saya...', 'Tinggalkan besok', 'Selesaikan dulu', 'Pulang tepat waktu', 'Lempar ke shift berikut', 'Pura-pura tidak tahu', 'B', 'Dedikasi dan tanggung jawab', '2025-11-15 06:44:31'),
(91, 3, 'Ketika diminta lembur tanpa uang tambahan, saya...', 'Menolak keras', 'Bersedia membantu', 'Marah-marah', 'Kerjakan asal-asalan', 'Cari alasan menolak', 'B', 'Pengabdian dan loyalitas', '2025-11-15 06:44:31'),
(92, 3, 'Rekan selalu terlambat, saya...', 'Laporkan langsung', 'Ingatkan dengan baik', 'Ikut terlambat', 'Abaikan', 'Gosipkan', 'B', 'Kepedulian dan cara komunikasi yang baik', '2025-11-15 06:44:31'),
(93, 3, 'Mendapat tawaran suap, saya...', 'Terima', 'Tolak tegas', 'Pertimbangkan dulu', 'Minta lebih banyak', 'Terima tapi tidak janji', 'B', 'Integritas dan anti korupsi', '2025-11-15 06:44:31'),
(94, 3, 'Pekerjaan tidak sesuai harapan, saya...', 'Resign', 'Tetap profesional', 'Malas-malasan', 'Komplain terus', 'Cari pekerjaan lain', 'B', 'Profesionalisme dan komitmen', '2025-11-15 06:44:31'),
(95, 3, 'Ketika stress kerja, saya...', 'Melampiaskan ke orang lain', 'Kelola dengan baik', 'Bolos kerja', 'Marah-marah', 'Minum alkohol', 'B', 'Manajemen stress yang sehat', '2025-11-15 06:44:31'),
(96, 3, 'Ada peraturan baru yang memberatkan, saya...', 'Langgar saja', 'Patuhi sambil beri masukan', 'Protes keras', 'Abaikan', 'Adu domba', 'B', 'Ketaatan dengan konstruktif', '2025-11-15 06:44:31'),
(97, 3, 'Menerima hadiah dari pihak berkepentingan, saya...', 'Terima saja', 'Tolak dengan sopan', 'Terima lalu balas', 'Minta lebih banyak', 'Terima diam-diam', 'B', 'Integritas dan menghindari gratifikasi', '2025-11-15 06:44:31'),
(98, 3, 'Informasi salah tersebar, saya...', 'Sebarkan juga', 'Luruskan dengan data benar', 'Diam saja', 'Ikut menambahkan', 'Manfaatkan untuk kepentingan', 'B', 'Kejujuran dan tanggung jawab informasi', '2025-11-15 06:44:31'),
(99, 3, 'Rekan sakit perlu bantuan, saya...', 'Tidak peduli', 'Bantu dengan tulus', 'Pura-pura sibuk', 'Bantu tapi pamrih', 'Suruh orang lain', 'B', 'Empati dan kepedulian sosial', '2025-11-15 06:44:31'),
(100, 3, 'Kesempatan curang tanpa ketahuan, saya...', 'Manfaatkan', 'Tetap jujur', 'Pertimbangkan dulu', 'Lihat situasi', 'Ambil tapi hati-hati', 'B', 'Integritas meski tidak diawasi', '2025-11-15 06:44:31'),
(101, 3, 'Proyek gagal karena kesalahan tim, saya...', 'Salahkan orang tertentu', 'Evaluasi bersama', 'Lepas tanggung jawab', 'Cari kambing hitam', 'Mengundurkan diri', 'B', 'Tanggung jawab kolektif', '2025-11-15 06:44:31'),
(102, 3, 'Ide saya ditolak atasan, saya...', 'Tersinggung', 'Terima dan cari solusi lain', 'Marah-marah', 'Diam membangkang', 'Merasa tidak dihargai', 'B', 'Menerima keputusan dengan dewasa', '2025-11-15 06:44:31'),
(103, 3, 'Waktu istirahat tapi pekerjaan mendesak, saya...', 'Tetap istirahat', 'Selesaikan dulu', 'Suruh orang lain', 'Tunda sampai besok', 'Pura-pura tidak tahu', 'B', 'Dedikasi dan prioritas pekerjaan', '2025-11-15 06:44:31'),
(104, 3, 'Rekan minta tolong hal pribadi di jam kerja, saya...', 'Bantu langsung', 'Bantu setelah jam kerja', 'Tolak mentah-mentah', 'Abaikan', 'Marah-marah', 'B', 'Keseimbangan profesional dan sosial', '2025-11-15 06:44:31'),
(105, 3, 'Teknologi baru sulit dipelajari, saya...', 'Menolak pakai', 'Belajar dengan giat', 'Minta ganti sistem lama', 'Komplain terus', 'Suruh orang lain', 'B', 'Adaptasi dan semangat belajar', '2025-11-15 06:44:31'),
(106, 3, 'Prestasi kerja saya tidak diakui, saya...', 'Marah dan protes', 'Tetap bekerja baik', 'Balas dengan malas', 'Cari pembenaran', 'Iri pada yang diakui', 'B', 'Keikhlasan dan profesionalisme', '2025-11-15 06:44:31'),
(107, 3, 'Diminta pindah tugas ke daerah terpencil, saya...', 'Menolak keras', 'Siap melaksanakan', 'Cari alasan kesehatan', 'Mengundurkan diri', 'Minta kompensasi besar', 'B', 'Pengabdian dan mobilitas', '2025-11-15 06:44:31'),
(108, 3, 'Masyarakat miskin butuh bantuan tapi tidak sesuai prosedur, saya...', 'Tolak karena prosedur', 'Bantu sambil urus prosedur', 'Abaikan', 'Suruh datang lagi', 'Minta uang dulu', 'B', 'Humanis dengan tetap profesional', '2025-11-15 06:44:31'),
(109, 3, 'Atasan membuat keputusan yang kurang tepat, saya...', 'Membangkang', 'Beri masukan dengan sopan', 'Gosipkan ke rekan', 'Laporkan ke atasan atas', 'Diam tapi tidak setuju', 'B', 'Komunikasi konstruktif ke atas', '2025-11-15 06:44:31'),
(110, 3, 'Jam kerja sudah habis tapi antrian masih panjang, saya...', 'Tutup pelayanan', 'Layani sampai selesai', 'Suruh datang besok', 'Layani asal-asalan', 'Pura-pura tidak lihat', 'B', 'Pelayanan prima dan dedikasi', '2025-11-15 06:44:31'),
(111, 5, 'siapa nama saya', 'A ade', 'B akbar', 'C fatul', 'D pikram', 'E zull', 'A', 'ade', '2025-11-16 04:10:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@Quizly.com', 'admin', '2025-11-15 06:44:30')
;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `hasil_test`
--
ALTER TABLE `hasil_test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_paket` (`paket_id`);

--
-- Indeks untuk tabel `jawaban_detail`
--
ALTER TABLE `jawaban_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hasil_test_id` (`hasil_test_id`),
  ADD KEY `soal_id` (`soal_id`);

--
-- Indeks untuk tabel `paket_soal`
--
ALTER TABLE `paket_soal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indeks untuk tabel `soal`
--
ALTER TABLE `soal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_paket` (`paket_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `hasil_test`
--
ALTER TABLE `hasil_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `jawaban_detail`
--
ALTER TABLE `jawaban_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `paket_soal`
--
ALTER TABLE `paket_soal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `soal`
--
ALTER TABLE `soal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `hasil_test`
--
ALTER TABLE `hasil_test`
  ADD CONSTRAINT `hasil_test_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hasil_test_ibfk_2` FOREIGN KEY (`paket_id`) REFERENCES `paket_soal` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jawaban_detail`
--
ALTER TABLE `jawaban_detail`
  ADD CONSTRAINT `jawaban_detail_ibfk_1` FOREIGN KEY (`hasil_test_id`) REFERENCES `hasil_test` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jawaban_detail_ibfk_2` FOREIGN KEY (`soal_id`) REFERENCES `soal` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `soal`
--
ALTER TABLE `soal`
  ADD CONSTRAINT `soal_ibfk_1` FOREIGN KEY (`paket_id`) REFERENCES `paket_soal` (`id`) ON DELETE CASCADE;
COMMIT;


