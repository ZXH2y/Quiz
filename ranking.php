<?php


require_once 'config.php';
requireLogin();
requireUser();

$user_id = $_SESSION['user_id'];

$paket_id = isset($_GET['paket']) ? (int)$_GET['paket'] : 0;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$paket_list = $conn->query("SELECT * FROM paket_soal WHERE status = 'aktif' ORDER BY nama_paket");

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

// Hitung total data
$sql_count = "SELECT COUNT(*) as total FROM hasil_test ht $where";
$total_data = $conn->query($sql_count)->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Cari posisi user yang login
if ($paket_id > 0) {
    $sql_user = "SELECT skor FROM hasil_test WHERE user_id = ? AND paket_id = ? ORDER BY skor DESC LIMIT 1";
    $stmt = $conn->prepare($sql_user);
    $stmt->bind_param("ii", $user_id, $paket_id);
    $stmt->execute();
    $user_skor_result = $stmt->get_result();
    
    if ($user_skor_result->num_rows > 0) {
        $user_best_skor = $user_skor_result->fetch_assoc()['skor'];
        
        $sql_rank = "SELECT COUNT(*) + 1 as ranking 
                     FROM hasil_test 
                     WHERE paket_id = ? AND skor > ?";
        $stmt = $conn->prepare($sql_rank);
        $stmt->bind_param("ii", $paket_id, $user_best_skor);
        $stmt->execute();
        $user_ranking = $stmt->get_result()->fetch_assoc()['ranking'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking Peserta - Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/ranking.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
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
        </div>
    </nav>
    
    <!-- CONTAINER -->
    <div class="container">
        <!-- HEADER -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-trophy"></i>
                Leaderboard Ranking
            </h1>
            <p class="page-subtitle">Lihat peringkat peserta berdasarkan skor tertinggi</p>
        </div>
        
        <!-- FILTER -->
        <div class="filter-section">
            <div class="filter-label">
                <i class="fas fa-filter"></i> Filter Paket:
            </div>
            <select class="filter-select" onchange="window.location.href='ranking.php?paket=' + this.value">
                <option value="0">Semua Paket</option>
                <?php while ($paket = $paket_list->fetch_assoc()): ?>
                <option value="<?= $paket['id'] ?>" <?= $paket_id == $paket['id'] ? 'selected' : '' ?>>
                    <?= e($paket['nama_paket']) ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <!-- USER RANK CARD -->
        <?php if (isset($user_ranking)): ?>
        <div class="user-rank-card">
            <div class="user-rank-item">
                <div class="user-rank-value"><i class="fas fa-medal"></i> #<?= $user_ranking ?></div>
                <div class="user-rank-label">Peringkat Anda</div>
            </div>
            <div class="user-rank-item">
                <div class="user-rank-value"><?= $user_best_skor ?></div>
                <div class="user-rank-label">Skor Terbaik</div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- RANKING TABLE -->
        <div class="ranking-table">
            <?php if ($ranking_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">Rank</th>
                        <th>Nama Peserta</th>
                        <th>Paket Soal</th>
                        <th style="text-align: center;">Skor</th>
                        <th style="text-align: center;">Benar</th>
                        <th style="text-align: center;">Salah</th>
                        <th style="text-align: center;">Waktu</th>
                        <th style="text-align: center;">Tanggal</th>
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
                    <tr class="<?= $is_current_user ? 'current-user' : '' ?>">
                        <td class="rank-number <?= $rank_class ?>">
                            <?= $medal ?: $rank ?>
                        </td>
                        <td>
                            <div class="user-name">
                                <?= e($row['nama_lengkap']) ?>
                                <?= $is_current_user ? '<span style="color: #2a5298;">(Anda)</span>' : '' ?>
                            </div>
                            <div style="font-size: 12px; color: #999;">@<?= e($row['username']) ?></div>
                        </td>
                        <td><?= e($row['nama_paket']) ?></td>
                        <td style="text-align: center;">
                            <span class="badge <?= $badge_class ?>"><?= $row['skor'] ?></span>
                        </td>
                        <td style="text-align: center; color: #28a745; font-weight: 600;">
                            ✓ <?= $row['benar'] ?>
                        </td>
                        <td style="text-align: center; color: #dc3545; font-weight: 600;">
                            ✗ <?= $row['salah'] ?>
                        </td>
                        <td style="text-align: center;">
                            <?= formatWaktu($row['waktu_pengerjaan']) ?>
                        </td>
                        <td style="text-align: center;">
                            <?= date('d/m/Y', strtotime($row['tanggal_test'])) ?>
                        </td>
                    </tr>
                    <?php 
                    $rank++;
                    endwhile; ?>
                </tbody>
            </table>
            
            <!-- PAGINATION -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="?paket=<?= $paket_id ?>&page=<?= $page - 1 ?>">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $page || $i == 1 || $i == $total_pages || abs($i - $page) <= 2): ?>
                    <a href="?paket=<?= $paket_id ?>&page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                    <?php elseif (abs($i - $page) == 3): ?>
                    <span style="padding: 10px;">...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <a href="?paket=<?= $paket_id ?>&page=<?= $page + 1 ?>">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-trophy"></i>
                <h3>Belum Ada Data Ranking</h3>
                <p>Belum ada peserta yang mengerjakan test pada paket ini</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>