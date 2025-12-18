<?php


require_once 'config.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/index.php');
    } else {
        redirect('dashboard.php');
    }
}

$error = '';
$success = '';
$showRegister = isset($_GET['register']);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                if ($user['role'] === 'admin') {
                    redirect('admin/index.php');
                } else {
                    redirect('dashboard.php');
                }
            } else {
                $error = 'Password salah! Silakan coba lagi.';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    $email = sanitize($_POST['email']);
    
    if (empty($username) || empty($password) || empty($nama_lengkap) || empty($email)) {
        $error = 'Semua field harus diisi!';
    } elseif (strlen($username) < 4) {
        $error = 'Username minimal 4 karakter!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Username atau email sudah terdaftar!';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, password, nama_lengkap, email, role) VALUES (?, ?, ?, ?, 'user')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $hashed_password, $nama_lengkap, $email);
            
            if ($stmt->execute()) {
                $success = 'Registrasi berhasil! Silakan login.';
                $showRegister = false;
            } else {
                $error = 'Registrasi gagal! Silakan coba lagi.';
            }
        }
    }
}

if (isset($_GET['logout'])) {
    $success = 'Anda berhasil logout!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizly - Platform Belajar Terbaik</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container">
        <!-- Panel Kiri -->
        <div class="left-panel">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i> Quizy
            </div>
            <div class="tagline">Platform belajar online no 1!</div>
            <ul class="features">
                <li><i class="fas fa-check-circle"></i> Soal Latihan Berkualitas</li>
                <li><i class="fas fa-random"></i> Soal Teracak Setiap User</li>
                <li><i class="fas fa-clock"></i> Simulasi Timer Seperti Ujian Asli</li>
                <li><i class="fas fa-book-open"></i> Pembahasan Lengkap & Detail</li>
                <li><i class="fas fa-trophy"></i> Ranking & Leaderboard</li>
                <li><i class="fas fa-chart-line"></i> Statistik Perkembangan</li>
            </ul>
        </div>
        
        <!-- Panel Kanan -->
        <div class="right-panel">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= e($success) ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (!$showRegister): ?>
                <!-- FORM LOGIN -->
                <h2 class="form-title">Selamat Datang! 👋</h2>
                <p class="form-subtitle">Silakan login untuk melanjutkan</p>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Username</label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" placeholder="Masukkan username" required autofocus>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" placeholder="Masukkan password" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="login" class="btn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login Sekarang</span>
                    </button>
                </form>
                
                <div class="toggle-form">
                    Belum punya akun? <a href="?register=1">Daftar Sekarang</a>
                </div>
                
                
            <?php else: ?>
                <!-- FORM REGISTER -->
                <h2 class="form-title">Daftar Akun Baru 📝</h2>
                <p class="form-subtitle">Buat akun untuk memulai belajar</p>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <div class="input-group">
                            <i class="fas fa-id-card"></i>
                            <input type="text" name="nama_lengkap" placeholder="Nama lengkap Anda" required autofocus>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="email@example.com" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Username</label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" placeholder="Pilih username (min 4 karakter)" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" placeholder="Buat password (min 6 karakter)" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="confirm_password" placeholder="Ketik ulang password" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="register" class="btn">
                        <i class="fas fa-user-plus"></i>
                        <span>Daftar Sekarang</span>
                    </button>
                </form>
                
                <div class="toggle-form">
                    Sudah punya akun? <a href="index.php">Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Auto hide alert setelah 5 detik
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Validasi form register
        const form = document.querySelector('form');
        if (form && form.querySelector('input[name="confirm_password"]')) {
            form.addEventListener('submit', function(e) {
                const password = document.querySelector('input[name="password"]').value;
                const confirm = document.querySelector('input[name="confirm_password"]').value;
                
                if (password !== confirm) {
                    e.preventDefault();
                    alert('❌ Password dan konfirmasi password tidak cocok!');
                    return false;
                }
                
                if (password.length < 6) {
                    e.preventDefault();
                    alert('❌ Password minimal 6 karakter!');
                    return false;
                }
            });
        }
    </script>
</body>
</html>