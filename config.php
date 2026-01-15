<?php


// Mulai Session
session_start();


define('DB_HOST', 'localhost');
define('DB_USER', 'andri');           
define('DB_PASS', '223280019');               
define('DB_NAME', 'Quiz');     


try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Cek koneksi
    if ($conn->connect_error) {
        die("❌ Koneksi database gagal: " . $conn->connect_error);
    }
    
    // Set charset UTF-8
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("❌ Error: " . $e->getMessage());
}



function redirect($url) {
    header("Location: $url");
    exit();
}


function isLoggedIn() {
    return isset($_SESSION['user_id']);
}


function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}


function isUser() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}


function requireLogin() {
    if (!isLoggedIn()) {
        redirect('index.php');
    }
}


function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect('dashboard.php');
    }
}


function requireUser() {
    requireLogin();
    if (isAdmin()) {
        redirect('admin/index.php');
    }
}


function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}


function formatWaktu($detik) {
    $jam = floor($detik / 3600);
    $menit = floor(($detik % 3600) / 60);
    $detik = $detik % 60;
    
    if ($jam > 0) {
        return sprintf("%02d:%02d:%02d", $jam, $menit, $detik);
    } else {
        return sprintf("%02d:%02d", $menit, $detik);
    }
}


function generateRandomOrder($max) {
    $numbers = range(0, $max - 1);
    shuffle($numbers);
    return $numbers;
}


function hitungPersentase($nilai, $total) {
    if ($total == 0) return 0;
    return round(($nilai / $total) * 100, 2);
}


function getUserById($user_id) {
    global $conn;
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


function alertSuccess($message) {
    return '<div class="alert alert-success">
                <i class="fas fa-check-circle"></i> ' . htmlspecialchars($message) . '
            </div>';
}

function alertError($message) {
    return '<div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($message) . '
            </div>';
}

function getBadgeClass($persentase) {
    if ($persentase >= 80) return 'badge-success';
    if ($persentase >= 60) return 'badge-warning';
    return 'badge-danger';
}


function formatTanggalIndonesia($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $split = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}


function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}


date_default_timezone_set('Asia/Jakarta');


?>