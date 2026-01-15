<?php

require_once '../config.php';
requireLogin();
requireAdmin();

// STATISTIK SISTEM
$total_users = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='user'")->fetch_assoc()['total'];
$total_paket = $conn->query("SELECT COUNT(*) as total FROM paket_soal")->fetch_assoc()['total'];
$total_soal = $conn->query("SELECT COUNT(*) as total FROM soal")->fetch_assoc()['total'];
$total_test = $conn->query("SELECT COUNT(*) as total FROM hasil_test")->fetch_assoc()['total'];

// Test hari ini
$today = date('Y-m-d');
$test_hari_ini = $conn->query("SELECT COUNT(*) as total FROM hasil_test WHERE DATE(tanggal_test) = '$today'")->fetch_assoc()['total'];

// Rata-rata skor
$rata_skor = $conn->query("SELECT AVG(skor) as rata FROM hasil_test")->fetch_assoc()['rata'];
$rata_skor = $rata_skor ? round($rata_skor, 1) : 0;

// TEST TERBARU (10 terakhir)
$sql = "SELECT ht.*, u.nama_lengkap, ps.nama_paket 
        FROM hasil_test ht 
        JOIN users u ON ht.user_id = u.id 
        JOIN paket_soal ps ON ht.paket_id = ps.id 
        ORDER BY ht.tanggal_test DESC 
        LIMIT 10";
$test_result = $conn->query($sql);

// USER TERBARU (5 terakhir)
$sql = "SELECT * FROM users WHERE role='user' ORDER BY created_at DESC LIMIT 5";
$user_result = $conn->query($sql);

// PAKET SOAL TERPOPULER
$sql = "SELECT ps.nama_paket, COUNT(ht.id) as total_test 
        FROM paket_soal ps 
        LEFT JOIN hasil_test ht ON ps.id = ht.paket_id 
        GROUP BY ps.id 
        ORDER BY total_test DESC 
        LIMIT 5";
$paket_populer = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard-admin.css">
</head>
<body>
    <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
        <i class="fas fa-bars" id="toggleIcon"></i>
    </button>
    <!-- SIDEBAR -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>
                <i class="fas fa-graduation-cap"></i>
                <span>Quizly</span>
            </h2>
            <span class="admin-badge">ADMIN PANEL</span>
        </div>
        <nav class="sidebar-menu">
            <a href="index.php" class="menu-item active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="paket.php" class="menu-item">
                <i class="fas fa-box"></i>
                <span>Paket Soal</span>
            </a>
            <a href="soal.php" class="menu-item">
                <i class="fas fa-book"></i>
                <span>Kelola Soal</span>
            </a>
            <a href="users.php" class="menu-item">
                <i class="fas fa-users"></i>
                <span>Kelola User</span>
            </a>
            <a href="../dashboard.php" class="menu-item">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke User</span>
            </a>
            <a href="../logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>
    
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- TOP BAR -->
        <div class="top-bar">
            <h1 class="page-title">Dashboard Admin</h1>
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?>
                </div>
                <div>
                    <div style="font-weight: 600;"><?= e($_SESSION['nama_lengkap']) ?></div>
                    <div style="font-size: 12px; color: #666;">Administrator</div>
                </div>
            </div>
        </div>
        
        <!-- STATS GRID -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?= $total_users ?></div>
                <div class="stat-label">Total User</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-number"><?= $total_paket ?></div>
                <div class="stat-label">Paket Soal</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-number"><?= $total_soal ?></div>
                <div class="stat-label">Total Soal</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stat-number"><?= $total_test ?></div>
                <div class="stat-label">Test Dikerjakan</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-number"><?= $test_hari_ini ?></div>
                <div class="stat-label">Test Hari Ini</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-number"><?= $rata_skor ?></div>
                <div class="stat-label">Rata-rata Skor</div>
            </div>
        </div>
        
        <!-- TEST TERBARU -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-clock"></i> Test Terbaru
                </h2>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Nama User</th>
                        <th>Paket Soal</th>
                        <th>Skor</th>
                        <th>Benar/Salah/Kosong</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($test_result->num_rows > 0): ?>
                        <?php while ($test = $test_result->fetch_assoc()): 
                            $total_soal = $test['benar'] + $test['salah'] + $test['kosong'];
                            $persentase = ($total_soal > 0) ? ($test['benar'] / $total_soal) * 100 : 0;
                            $badge_class = $persentase >= 80 ? 'badge-success' : ($persentase >= 60 ? 'badge-warning' : 'badge-danger');
                        ?>
                        <tr>
                            <td><strong><?= e($test['nama_lengkap']) ?></strong></td>
                            <td><?= e($test['nama_paket']) ?></td>
                            <td><span class="badge <?= $badge_class ?>"><?= $test['skor'] ?></span></td>
                            <td>
                                <span style="color: #28a745;">✓ <?= $test['benar'] ?></span> / 
                                <span style="color: #dc3545;">✗ <?= $test['salah'] ?></span> / 
                                <span style="color: #ffc107;">− <?= $test['kosong'] ?></span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($test['tanggal_test'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px;">
                                <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 10px;"></i>
                                <p style="color: #666;">Belum ada test yang dikerjakan</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px;">
            <!-- USER TERBARU -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-user-plus"></i> User Terbaru
                    </h2>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($user_result->num_rows > 0): ?>
                            <?php while ($user = $user_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= e($user['nama_lengkap']) ?></td>
                                <td><span class="badge badge-info">@<?= e($user['username']) ?></span></td>
                                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 40px;">
                                    <p style="color: #666;">Belum ada user</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- PAKET TERPOPULER -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-fire"></i> Paket Terpopuler
                    </h2>
                </div>
                
                <div class="chart-container">
                    <?php 
                    $max_test = 0;
                    $paket_array = [];
                    while ($paket = $paket_populer->fetch_assoc()) {
                        $paket_array[] = $paket;
                        if ($paket['total_test'] > $max_test) {
                            $max_test = $paket['total_test'];
                        }
                    }
                    
                    foreach ($paket_array as $paket): 
                        $width = $max_test > 0 ? ($paket['total_test'] / $max_test) * 100 : 0;
                    ?>
                    <div class="chart-item">
                        <div class="chart-label">
                            <span><?= e($paket['nama_paket']) ?></span>
                            <span><strong><?= $paket['total_test'] ?></strong> test</span>
                        </div>
                        <div class="chart-bar">
                            <div class="chart-fill" style="width: <?= $width ?>%;">
                                <?= $paket['total_test'] ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($paket_array)): ?>
                    <p style="text-align: center; color: #666; padding: 40px;">Belum ada data</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
        document.body.classList.toggle('sidebar-open');
        
        const icon = document.getElementById('toggleIcon');
        icon.className = document.getElementById('sidebar').classList.contains('active') 
            ? 'fas fa-times' 
            : 'fas fa-bars';
    }
    
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('active');
        document.getElementById('sidebarOverlay').classList.remove('active');
        document.body.classList.remove('sidebar-open');
        document.getElementById('toggleIcon').className = 'fas fa-bars';
    }
</script>
</body>
</body>
</html>