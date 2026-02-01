<?php
/**
 * Quizly - Ranking Page
 * Halaman untuk menampilkan peringkat peserta quiz
 * dengan fitur Print PDF yang profesional
 */

require_once 'config.php';
requireLogin();
requireUser();

$user_id = $_SESSION['user_id'];

// Get filter parameters
$paket_id = isset($_GET['paket']) ? (int)$_GET['paket'] : 0;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Fetch active packages for filter
$paket_list = $conn->query("SELECT * FROM paket_soal WHERE status = 'aktif' ORDER BY nama_paket");

// Build query with optional filter
$where = $paket_id > 0 ? "WHERE ht.paket_id = $paket_id" : "";

$sql = "SELECT 
            ht.id,
            ht.user_id,
            u.nama_lengkap,
            u.username,
            ps.nama_paket,
            ht.skor,
            ht.benar,
            ht.salah,
            ht.kosong,
            ht.waktu_pengerjaan,
            ht.tanggal_test
        FROM hasil_test ht
        JOIN users u ON ht.user_id = u.id
        JOIN paket_soal ps ON ht.paket_id = ps.id
        $where
        ORDER BY ht.skor DESC, ht.waktu_pengerjaan ASC, ht.tanggal_test ASC
        LIMIT $limit OFFSET $offset";

$ranking_result = $conn->query($sql);

// Count total data for pagination
$sql_count = "SELECT COUNT(*) as total FROM hasil_test ht $where";
$total_data = $conn->query($sql_count)->fetch_assoc()['total'];
$total_pages = max(1, ceil($total_data / $limit));

// Get current user's ranking
$user_ranking = null;
$user_best_skor = null;
$selected_paket_name = 'Semua Paket';

if ($paket_id > 0) {
    // Get package name
    $paket_query = $conn->query("SELECT nama_paket FROM paket_soal WHERE id = $paket_id");
    if ($paket_query->num_rows > 0) {
        $selected_paket_name = $paket_query->fetch_assoc()['nama_paket'];
    }
    
    // Get user's best score for this package
    $sql_user = "SELECT skor FROM hasil_test WHERE user_id = ? AND paket_id = ? ORDER BY skor DESC LIMIT 1";
    $stmt = $conn->prepare($sql_user);
    $stmt->bind_param("ii", $user_id, $paket_id);
    $stmt->execute();
    $user_skor_result = $stmt->get_result();
    
    if ($user_skor_result->num_rows > 0) {
        $user_best_skor = $user_skor_result->fetch_assoc()['skor'];
        
        // Calculate user's ranking
        $sql_rank = "SELECT COUNT(*) + 1 as ranking 
                     FROM hasil_test 
                     WHERE paket_id = ? AND skor > ?";
        $stmt = $conn->prepare($sql_rank);
        $stmt->bind_param("ii", $paket_id, $user_best_skor);
        $stmt->execute();
        $user_ranking = $stmt->get_result()->fetch_assoc()['ranking'];
    }
}

// Format date for print
$print_date = date('d F Y, H:i');
$file_date = date('Ymd_His');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking Peserta - Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/ranking.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar no-print">
        <div class="navbar-content">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i> Quizly
            </div>
            <div class="nav-menu">
                <a href="dashboard.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="ranking.php" class="active">
                    <i class="fas fa-trophy"></i> Ranking
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
            <!-- Mobile Menu Button -->
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="ranking.php" class="active"><i class="fas fa-trophy"></i> Ranking</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>
    
    <!-- CONTAINER -->
    <div class="container">
        <!-- HEADER -->
        <div class="page-header no-print">
            <h1 class="page-title">
                <i class="fas fa-trophy"></i>
                Leaderboard Ranking
            </h1>
            <p class="page-subtitle">Lihat peringkat peserta berdasarkan skor tertinggi</p>
        </div>
        
        <!-- FILTER & ACTIONS -->
        <div class="filter-section no-print">
            <div class="filter-group">
                <div class="filter-label">
                    <i class="fas fa-filter"></i> Filter Paket:
                </div>
                <select class="filter-select" id="paketFilter" onchange="window.location.href='ranking.php?paket=' + this.value">
                    <option value="0">Semua Paket</option>
                    <?php while ($paket = $paket_list->fetch_assoc()): ?>
                    <option value="<?= $paket['id'] ?>" <?= $paket_id == $paket['id'] ? 'selected' : '' ?>>
                        <?= e($paket['nama_paket']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="action-buttons">
                <button class="btn-refresh" onclick="window.location.reload()" title="Refresh Data">
                    <i class="fas fa-sync-alt"></i>
                    <span>Refresh</span>
                </button>
                <button class="btn-print" onclick="printRanking()" title="Cetak ke PDF">
                    <i class="fas fa-file-pdf"></i>
                    <span>Print PDF</span>
                </button>
            </div>
        </div>
        
        <!-- USER RANK CARD -->
        <?php if ($user_ranking !== null): ?>
        <div class="user-rank-card no-print">
            <div class="user-rank-item">
                <div class="user-rank-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="user-rank-info">
                    <div class="user-rank-value">#<?= $user_ranking ?></div>
                    <div class="user-rank-label">Peringkat Anda</div>
                </div>
            </div>
            <div class="rank-divider"></div>
            <div class="user-rank-item">
                <div class="user-rank-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="user-rank-info">
                    <div class="user-rank-value"><?= $user_best_skor ?></div>
                    <div class="user-rank-label">Skor Terbaik</div>
                </div>
            </div>
            <div class="rank-divider"></div>
            <div class="user-rank-item">
                <div class="user-rank-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="user-rank-info">
                    <div class="user-rank-value"><?= $total_data ?></div>
                    <div class="user-rank-label">Total Peserta</div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- RANKING TABLE -->
        <div class="ranking-table">
            <?php if ($ranking_result->num_rows > 0): ?>
            
            <!-- ==================== PRINT AREA START ==================== -->
            <div id="print-area">
                
                <!-- PRINT HEADER -->
                <div class="print-header">
                    <div class="print-header-top">
                        <div class="print-logo">
                            <div class="print-logo-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="print-logo-text">
                                <span class="print-brand">Quizly</span>
                                <span class="print-tagline">Sistem Quiz Online</span>
                            </div>
                        </div>
                        <div class="print-badge">
                            <i class="fas fa-trophy"></i>
                            OFFICIAL RANKING
                        </div>
                    </div>
                    
                    <div class="print-title-section">
                        <h1 class="print-title">Laporan Ranking Peserta</h1>
                        <p class="print-subtitle">Daftar peringkat berdasarkan skor tertinggi</p>
                    </div>
                    
                    <div class="print-meta">
                        <div class="print-meta-card">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <span class="meta-label">Tanggal Cetak</span>
                                <span class="meta-value"><?= $print_date ?> WIB</span>
                            </div>
                        </div>
                        <div class="print-meta-card">
                            <i class="fas fa-folder-open"></i>
                            <div>
                                <span class="meta-label">Paket Soal</span>
                                <span class="meta-value"><?= e($selected_paket_name) ?></span>
                            </div>
                        </div>
                        <div class="print-meta-card">
                            <i class="fas fa-users"></i>
                            <div>
                                <span class="meta-label">Total Data</span>
                                <span class="meta-value"><?= $total_data ?> Peserta</span>
                            </div>
                        </div>
                        <div class="print-meta-card">
                            <i class="fas fa-file-alt"></i>
                            <div>
                                <span class="meta-label">Halaman</span>
                                <span class="meta-value"><?= $page ?> dari <?= $total_pages ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- TABLE -->
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <!-- <th class="th-spasi"></th> -->
                                <th class="th-rank">Rank</th>
                                <th class="th-name">Nama Peserta</th>
                                <th class="th-paket">Paket Soal</th>
                                <th class="th-score">Skor</th>
                                <th class="th-correct">Benar</th>
                                <th class="th-wrong">Salah</th>
                                <th class="th-time">Waktu</th>
                                <th class="th-date">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = $offset + 1;
                            while ($row = $ranking_result->fetch_assoc()): 
                                $is_current_user = ($row['user_id'] == $user_id);
                                $total_soal = $row['benar'] + $row['salah'] + $row['kosong'];
                                $persentase = ($total_soal > 0) ? ($row['benar'] / $total_soal) * 100 : 0;
                                $badge_class = $persentase >= 80 ? 'badge-success' : ($persentase >= 60 ? 'badge-warning' : 'badge-danger');
                                
                                $medal = '';
                                $rank_class = '';
                                if ($rank == 1) {
                                    $medal = '🥇';
                                    $rank_class = 'rank-1';
                                } elseif ($rank == 2) {
                                    $medal = '🥈';
                                    $rank_class = 'rank-2';
                                } elseif ($rank == 3) {
                                    $medal = '🥉';
                                    $rank_class = 'rank-3';
                                }
                            ?>
                            <!-- <tr class="<?= $is_current_user ? 'current-user' : '' ?>" data-rank="<?= $rank ?>"> -->
                                <td class="td-rank">
                                    <div class="rank-cell <?= $rank_class ?>">
                                        <?php if ($medal): ?>
                                            <span class="medal"><?= $medal ?></span>
                                        <?php else: ?>
                                            <span class="rank-num"><?= $rank ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="td-name">
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?= strtoupper(substr($row['nama_lengkap'], 0, 1)) ?>
                                        </div>
                                        <div class="user-details">
                                            <span class="user-name">
                                                <?= e($row['nama_lengkap']) ?>
                                                <?php if ($is_current_user): ?>
                                                    <span class="you-badge">Anda</span>
                                                <?php endif; ?>
                                            </span>
                                            <span class="user-username">@<?= e($row['username']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="td-paket">
                                    <span class="paket-name"><?= e($row['nama_paket']) ?></span>
                                </td>
                                <td class="td-score">
                                    <span class="badge <?= $badge_class ?>"><?= $row['skor'] ?></span>
                                </td>
                                <td class="td-correct">
                                    <span class="stat-correct">
                                        <i class="fas fa-check"></i> <?= $row['benar'] ?>
                                    </span>
                                </td>
                                <td class="td-wrong">
                                    <span class="stat-wrong">
                                        <i class="fas fa-times"></i> <?= $row['salah'] ?>
                                    </span>
                                </td>
                                <td class="td-time">
                                    <span class="time-value">
                                        <i class="fas fa-clock"></i> <?= formatWaktu($row['waktu_pengerjaan']) ?>
                                    </span>
                                </td>
                                <td class="td-date">
                                    <?= date('d/m/Y', strtotime($row['tanggal_test'])) ?>
                                </td>
                            </tr>
                            <?php 
                            $rank++;
                            endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- PRINT FOOTER -->
                <div class="print-footer">
                    <div class="print-footer-decoration">
                        <div class="footer-line"></div>
                        <div class="footer-icon"><i class="fas fa-award"></i></div>
                        <div class="footer-line"></div>
                    </div>
                    <div class="print-footer-content">
                        <div class="footer-left">
                            <span class="footer-copyright">© <?= date('Y') ?> Quizly - Sistem Quiz Online</span>
                            <span class="footer-note">Dokumen ini dicetak secara otomatis oleh sistem</span>
                        </div>
                        <div class="footer-right">
                            <span class="footer-page">Halaman <?= $page ?> dari <?= $total_pages ?></span>
                        </div>
                    </div>
                </div>
                
            </div>
            <!-- ==================== PRINT AREA END ==================== -->
            
            <!-- PAGINATION -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination no-print">
                <?php if ($page > 1): ?>
                <a href="?paket=<?= $paket_id ?>&page=1" class="pagination-btn" title="Halaman Pertama">
                    <i class="fas fa-angle-double-left"></i>
                </a>
                <a href="?paket=<?= $paket_id ?>&page=<?= $page - 1 ?>" class="pagination-btn">
                    <i class="fas fa-chevron-left"></i> Prev
                </a>
                <?php endif; ?>
                
                <div class="pagination-numbers">
                    <?php 
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    if ($start_page > 1): ?>
                        <a href="?paket=<?= $paket_id ?>&page=1" class="pagination-num">1</a>
                        <?php if ($start_page > 2): ?>
                            <span class="pagination-dots">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <a href="?paket=<?= $paket_id ?>&page=<?= $i ?>" 
                           class="pagination-num <?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <span class="pagination-dots">...</span>
                        <?php endif; ?>
                        <a href="?paket=<?= $paket_id ?>&page=<?= $total_pages ?>" class="pagination-num"><?= $total_pages ?></a>
                    <?php endif; ?>
                </div>
                
                <?php if ($page < $total_pages): ?>
                <a href="?paket=<?= $paket_id ?>&page=<?= $page + 1 ?>" class="pagination-btn">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
                <a href="?paket=<?= $paket_id ?>&page=<?= $total_pages ?>" class="pagination-btn" title="Halaman Terakhir">
                    <i class="fas fa-angle-double-right"></i>
                </a>
                <?php endif; ?>
            </div>
            
            <div class="pagination-info no-print">
                Menampilkan <?= $offset + 1 ?> - <?= min($offset + $limit, $total_data) ?> dari <?= $total_data ?> data
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <!-- EMPTY STATE -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3>Belum Ada Data Ranking</h3>
                <p>Belum ada peserta yang mengerjakan test pada paket ini.</p>
                <?php if ($paket_id > 0): ?>
                <a href="ranking.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Lihat Semua Paket
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- PRINT OVERLAY -->
    <div class="print-overlay" id="printOverlay">
        <div class="print-loading">
            <div class="print-spinner"></div>
            <p>Menyiapkan dokumen...</p>
        </div>
    </div>

    <script>
    // Print Function
    function printRanking() {
        const overlay = document.getElementById('printOverlay');
        const originalTitle = document.title;
        
        // Show loading overlay
        overlay.classList.add('active');
        
        // Set document title for PDF filename
        document.title = 'Ranking_Quizly_<?= $file_date ?>';
        
        // Small delay for overlay animation
        setTimeout(() => {
            // Hide overlay before print dialog
            overlay.classList.remove('active');
            
            // Trigger print
            window.print();
            
            // Restore title after print
            setTimeout(() => {
                document.title = originalTitle;
            }, 500);
        }, 800);
    }
    
    // Keyboard shortcut Ctrl+P
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            printRanking();
        }
    });
    
    // Mobile menu toggle
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('active');
    }
    
    // Close mobile menu on outside click
    document.addEventListener('click', function(e) {
        const menu = document.getElementById('mobileMenu');
        const btn = document.querySelector('.mobile-menu-btn');
        if (!menu.contains(e.target) && !btn.contains(e.target)) {
            menu.classList.remove('active');
        }
    });
    
    // Add row animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('tbody tr').forEach(row => {
        observer.observe(row);
    });
    </script>
</body>
</html>