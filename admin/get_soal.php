<?php
require_once '../config.php';
requireLogin();
requireAdmin();

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID tidak ditemukan']);
    exit;
}

$id = (int)$_GET['id'];
$sql = "SELECT * FROM soal WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(['error' => 'Soal tidak ditemukan']);
}
?>