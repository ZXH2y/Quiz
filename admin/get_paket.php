<?php
require_once '../config.php';
requireLogin();
requireAdmin();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = $conn->query("SELECT * FROM paket_soal WHERE id = $id");
    
    if ($paket = $result->fetch_assoc()) {
        header('Content-Type: application/json');
        echo json_encode($paket);
    } else {
        echo json_encode(['error' => 'Paket tidak ditemukan']);
    }
} else {
    echo json_encode(['error' => 'ID tidak valid']);
}
?>