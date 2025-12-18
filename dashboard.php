<?php

require_once 'config.php';
requireLogin();

// Jika admin, redirect ke admin panel
// if (isAdmin()) {
//     redirect('admin/index.php');
// }

$user_id = $_SESSION['user_id'];


$sql = "SELECT * FROM paket_soal WHERE status = 'aktif' ORDER BY id";
$paket_result = $conn->query($sql);

$sql = "SELECT ht.*, ps.nama_paket 
        FROM hasil_test ht 
        JOIN paket_soal ps ON ht.paket_id = ps.id 
        WHERE ht.user_id = ? 
        ORDER BY ht.tanggal_test DESC 
        LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$riwayat_result = $stmt->get_result();

// hitung statistik
$sql = "SELECT 
        COUNT(*) as total_test,
        COALESCE(AVG(skor), 0) as rata_skor,
        COALESCE(MAX(skor), 0) as skor_tertinggi,
        COALESCE(SUM(benar), 0) as total_benar,
        COALESCE(SUM(salah), 0) as total_salah
        FROM hasil_test 
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Hitung persentase kelulusan (misal: lulus jika skor >= 65)
$sql_lulus = "SELECT COUNT(*) as jumlah_lulus 
              FROM hasil_test 
              WHERE user_id = ? AND skor >= 65";
$stmt = $conn->prepare($sql_lulus);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$lulus_count = $stmt->get_result()->fetch_assoc()['jumlah_lulus'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quizly!</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-content">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i> Quizly
            </div>
            <button class="mobile-menu-btn" onclick="toggleMenu()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="nav-menu" id="navMenu">
                <a href="dashboard.php" class="active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="ranking.php">
                    <i class="fas fa-trophy"></i> Ranking
                </a>
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?>
                    </div>
                    <span><?= e($_SESSION['nama_lengkap']) ?></span>
                </div>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>
    
    <!-- CONTAINER -->
    <div class="container">
        <!-- WELCOME SECTION -->
        <div class="welcome-section">
            <h1>Selamat Datang, <?= e($_SESSION['nama_lengkap']) ?>! 👋</h1>
            <p>Siap mengasah kemampuan untuk menghadapi tes Quizly? Pilih paket soal dan mulai berlatih sekarang!</p>
        </div>
        
        <!-- STATS GRID -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['total_test'] ?></h3>
                    <p>Total Test Dikerjakan</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($stats['rata_skor'], 1) ?></h3>
                    <p>Rata-rata Skor</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['skor_tertinggi'] ?></h3>
                    <p>Skor Tertinggi</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $lulus_count ?></h3>
                    <p>Test Lulus (≥65)</p>
                </div>
            </div>
        </div>
        
        <!-- PAKET SOAL -->
        <h2 class="section-title">
            <i class="fas fa-book"></i> Paket Soal Tersedia
        </h2>
        
        <div class="paket-grid">
            <?php if ($paket_result->num_rows > 0): ?>
                <?php while ($paket = $paket_result->fetch_assoc()): ?>
                <div class="paket-card">
                    <div class="paket-header">
                        <?php if (!empty($paket['gambar']) && file_exists('admin/uploads/' . $paket['gambar'])): ?>
                            <img src="admin/uploads/<?= e($paket['gambar']) ?>" 
                                 alt="<?= e($paket['nama_paket']) ?>" 
                                 style="width: 100%; height: 250px; object-fit: cover; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                        <?php endif; ?>
                        <h3><?= e($paket['nama_paket']) ?></h3>
                        <p><?= e($paket['deskripsi']) ?></p>
                    </div>
                    <div class="paket-body">
                        <div class="paket-info">
                            <div class="info-item">
                                <i class="fas fa-question-circle"></i>
                                <span class="value"><?= $paket['jumlah_soal'] ?></span>
                                <span class="label">Soal</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <span class="value"><?= $paket['durasi_menit'] ?></span>
                                <span class="label">Menit</span>
                            </div>
                        </div>
                        <a href="test.php?paket=<?= $paket['id'] ?>" class="btn">
                            <i class="fas fa-play"></i>
                            <span>Mulai Test</span>
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Belum Ada Paket Soal</h3>
                    <p>Paket soal akan segera tersedia</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- RIWAYAT TEST -->
        <?php if ($riwayat_result->num_rows > 0): ?>
        <h2 class="section-title">
            <i class="fas fa-history"></i> Riwayat Test Terakhir
        </h2>
        
        <div class="riwayat-section">
            <div class="riwayat-header">
                <h3 style="margin: 0; color: #333;">5 Test Terakhir</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Paket Soal</th>
                        <th>Tanggal</th>
                        <th>Skor</th>
                        <th>Benar</th>
                        <th>Salah</th>
                        <th>Kosong</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($riwayat = $riwayat_result->fetch_assoc()): 
                        $total_soal = $riwayat['benar'] + $riwayat['salah'] + $riwayat['kosong'];
                        $persentase = ($total_soal > 0) ? ($riwayat['benar'] / $total_soal) * 100 : 0;
                        $badge_class = $persentase >= 80 ? 'badge-success' : ($persentase >= 60 ? 'badge-warning' : 'badge-danger');
                    ?>
                    <tr>
                        <td><strong><?= e($riwayat['nama_paket']) ?></strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($riwayat['tanggal_test'])) ?></td>
                        <td><span class="badge <?= $badge_class ?>"><?= $riwayat['skor'] ?></span></td>
                        <td><span style="color: #28a745;">✓ <?= $riwayat['benar'] ?></span></td>
                        <td><span style="color: #dc3545;">✗ <?= $riwayat['salah'] ?></span></td>
                        <td><span style="color: #ffc107;">− <?= $riwayat['kosong'] ?></span></td>
                        <td><?= formatWaktu($riwayat['waktu_pengerjaan']) ?></td>
                        <td>
                            <a href="pembahasan.php?hasil=<?= $riwayat['id'] ?>" class="btn-detail">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
<script>
    function toggleMenu() {
        const menu = document.getElementById('navMenu');
        const body = document.body;
        
        menu.classList.toggle('active');
        body.classList.toggle('menu-open');
        
        // Toggle icon
        const btn = document.querySelector('.mobile-menu-btn i');
        if (menu.classList.contains('active')) {
            btn.className = 'fas fa-times';
        } else {
            btn.className = 'fas fa-bars';
        }
    }
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('navMenu');
        const btn = document.querySelector('.mobile-menu-btn');
        
        if (menu.classList.contains('active') && 
            !menu.contains(event.target) && 
            !btn.contains(event.target)) {
            menu.classList.remove('active');
            document.body.classList.remove('menu-open');
            btn.querySelector('i').className = 'fas fa-bars';
        }
    });
    
    // Close menu when clicking a link
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', () => {
            const menu = document.getElementById('navMenu');
            const btn = document.querySelector('.mobile-menu-btn');
            menu.classList.remove('active');
            document.body.classList.remove('menu-open');
            btn.querySelector('i').className = 'fas fa-bars';
        });
    });
</script>
</body>
</html>