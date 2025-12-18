<?php
/**
 * =====================================================
 * CONFIG.PHP - Konfigurasi Database & Helper Functions
 * =====================================================
 * File ini berisi:
 * 1. Koneksi database
 * 2. Session management
 * 3. Helper functions (redirect, login check, dll)
 * =====================================================
 */

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

// =====================================================
// HELPER FUNCTIONS
// =====================================================

/**
 * Redirect ke halaman lain
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Cek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Cek apakah user adalah admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Cek apakah user adalah user biasa
 */
function isUser() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

/**
 * Paksa user harus login (jika belum, redirect ke login)
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('index.php');
    }
}

/**
 * Paksa harus admin (jika bukan, redirect ke dashboard user)
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect('dashboard.php');
    }
}

/**
 * Paksa harus user biasa (jika admin, redirect ke admin panel)
 */
function requireUser() {
    requireLogin();
    if (isAdmin()) {
        redirect('admin/index.php');
    }
}

/**
 * Sanitize input untuk menghindari SQL Injection
 */
function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

/**
 * Format waktu dari detik ke format jam:menit:detik
 * Contoh: 3665 detik = 01:01:05
 */
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

/**
 * Generate urutan acak untuk soal
 * Agar setiap user mendapat urutan soal berbeda
 */
function generateRandomOrder($max) {
    $numbers = range(0, $max - 1);
    shuffle($numbers);
    return $numbers;
}

/**
 * Hitung persentase
 */
function hitungPersentase($nilai, $total) {
    if ($total == 0) return 0;
    return round(($nilai / $total) * 100, 2);
}

/**
 * Get user data by ID
 */
function getUserById($user_id) {
    global $conn;
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Alert message helper
 */
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

/**
 * Get badge class berdasarkan persentase
 */
function getBadgeClass($persentase) {
    if ($persentase >= 80) return 'badge-success';
    if ($persentase >= 60) return 'badge-warning';
    return 'badge-danger';
}

/**
 * Format tanggal Indonesia
 */
function formatTanggalIndonesia($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $split = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

/**
 * Escape HTML untuk keamanan
 */
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}


date_default_timezone_set('Asia/Jakarta');


?>