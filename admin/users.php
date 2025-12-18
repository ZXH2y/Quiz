<?php
/**
 * ADMIN/USERS.PHP - Kelola User (FIXED VERSION)
 */

// Error reporting untuk debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek file config
if (!file_exists('../config.php')) {
    die('ERROR: File config.php tidak ditemukan!');
}

require_once '../config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Cek admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../dashboard.php');
    exit;
}

$success = '';
$error = '';

// HAPUS USER
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $check = $conn->query("SELECT role FROM users WHERE id = $id");
    if ($check && $check->num_rows > 0) {
        $user = $check->fetch_assoc();
        
        if ($user['role'] === 'admin') {
            $error = "Tidak dapat menghapus admin!";
        } else {
            if ($conn->query("DELETE FROM users WHERE id = $id")) {
                $success = "User berhasil dihapus!";
            }
        }
    }
}

// RESET PASSWORD
if (isset($_GET['reset'])) {
    $id = (int)$_GET['reset'];
    $new_pass = password_hash('password123', PASSWORD_DEFAULT);
    
    if ($conn->query("UPDATE users SET password = '$new_pass' WHERE id = $id AND role = 'user'")) {
        $success = "Password berhasil direset ke: password123";
    }
}

// SEARCH
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$where = $search ? "WHERE nama_lengkap LIKE '%$search%' OR username LIKE '%$search%' OR email LIKE '%$search%'" : "";

// AMBIL DATA USER
$users = $conn->query("
    SELECT u.*,
    (SELECT COUNT(*) FROM hasil_test WHERE user_id = u.id) as total_test,
    (SELECT COALESCE(AVG(skor), 0) FROM hasil_test WHERE user_id = u.id) as rata_skor
    FROM users u
    $where
    ORDER BY u.created_at DESC
");

// STATISTIK
$stats = [
    'users' => $conn->query("SELECT COUNT(*) as n FROM users WHERE role='user'")->fetch_assoc()['n'],
    'admins' => $conn->query("SELECT COUNT(*) as n FROM users WHERE role='admin'")->fetch_assoc()['n'],
    'aktif' => $conn->query("SELECT COUNT(DISTINCT user_id) as n FROM hasil_test WHERE tanggal_test >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['n']
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/user.css">
</head>
<body>
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-graduation-cap"></i> <span>Quizly</span></h2>
            <span class="admin-badge">ADMIN PANEL</span>
        </div>
        <nav>
            <a href="index.php" class="menu-item">
                <i class="fas fa-home"></i> <span>Dashboard</span>
            </a>
            <a href="paket.php" class="menu-item">
                <i class="fas fa-box"></i> <span>Paket Soal</span>
            </a>
            <a href="soal.php" class="menu-item">
                <i class="fas fa-book"></i> <span>Kelola Soal</span>
            </a>
            <a href="users.php" class="menu-item active">
                <i class="fas fa-users"></i> <span>Kelola User</span>
            </a>
            <a href="../dashboard.php" class="menu-item">
                <i class="fas fa-arrow-left"></i> <span>Kembali ke User</span>
            </a>
            <a href="../logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </nav>
    </div>
    
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="top-bar">
            <h1 class="page-title">Kelola User</h1>
        </div>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        
        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['users'] ?></h3>
                    <p>Total User</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['admins'] ?></h3>
                    <p>Total Admin</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['aktif'] ?></h3>
                    <p>User Aktif (7 Hari)</p>
                </div>
            </div>
        </div>
        
        <!-- SEARCH -->
        <div class="search-bar">
            <form method="GET" class="search-form">
                <input type="text" name="search" class="search-input" 
                       placeholder="Cari user berdasarkan nama, username, atau email..." 
                       value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
                <?php if ($search): ?>
                <a href="users.php" class="btn btn-warning">
                    <i class="fas fa-times"></i> Reset
                </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- TABLE -->
        <div class="content-card">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Total Test</th>
                        <th>Rata Skor</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users && $users->num_rows > 0): ?>
                        <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="user-avatar">
                                        <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($user['nama_lengkap']) ?></strong><br>
                                        <small style="color: #999;">@<?= htmlspecialchars($user['username']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="badge badge-<?= $user['role'] ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td><?= $user['total_test'] ?></td>
                            <td><?= number_format($user['rata_skor'], 1) ?></td>
                            <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <?php if ($user['role'] === 'user'): ?>
                                <div class="action-buttons">
                                    <a href="?reset=<?= $user['id'] ?>" 
                                       class="btn btn-warning btn-sm" 
                                       onclick="return confirm('Reset password user ini ke password123?')"
                                       title="Reset Password">
                                        <i class="fas fa-key"></i>
                                    </a>
                                    <a href="?delete=<?= $user['id'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Yakin hapus user ini?\nSemua data test akan ikut terhapus!')"
                                       title="Hapus User">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                                <?php else: ?>
                                <span style="color: #999; font-size: 12px;">Protected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 60px;">
                                <i class="fas fa-inbox" style="font-size: 64px; color: #ccc;"></i>
                                <h3 style="color: #666; margin: 20px 0 10px;">Tidak ada user ditemukan</h3>
                                <p style="color: #999;">
                                    <?= $search ? 'Coba kata kunci lain' : 'Belum ada user terdaftar' ?>
                                </p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
       // Auto hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        // =====================================================
// MOBILE SIDEBAR TOGGLE
// =====================================================

document.addEventListener('DOMContentLoaded', function() {
    // Create toggle button
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'sidebar-toggle';
    toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
    toggleBtn.setAttribute('aria-label', 'Toggle Sidebar');
    document.body.appendChild(toggleBtn);
    
    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
    
    const sidebar = document.querySelector('.sidebar');
    
    // Toggle function
    function toggleSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.classList.toggle('sidebar-open');
    }
    
    // Event listeners
    toggleBtn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);
    
    // Close sidebar when clicking menu item on mobile
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                toggleSidebar();
            }
        });
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            }
        }, 250);
    });
});

// =====================================================
// AUTO HIDE ALERTS
// =====================================================

setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
        alert.style.transition = 'opacity 0.5s, transform 0.5s';
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);

// =====================================================
// SMOOTH ANIMATIONS ON LOAD
// =====================================================

window.addEventListener('load', function() {
    // Add entrance animations to table rows
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(10px)';
        setTimeout(() => {
            row.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, 100 * index);
    });
});
    </script>
</body>
</html>