<?php

require_once 'config.php';
requireLogin();
requireUser();

// Cek parameter ID hasil test
if (!isset($_GET['id'])) {
    redirect('dashboard.php');
}

$hasil_test_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];


$sql = "SELECT ht.*, ps.nama_paket, ps.jumlah_soal, u.nama_lengkap
        FROM hasil_test ht
        JOIN paket_soal ps ON ht.paket_id = ps.id
        JOIN users u ON ht.user_id = u.id
        WHERE ht.id = ? AND ht.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $hasil_test_id, $user_id);
$stmt->execute();
$hasil = $stmt->get_result()->fetch_assoc();

if (!$hasil) {
    redirect('dashboard.php');
}

// Hitung persentase
$total_soal = $hasil['benar'] + $hasil['salah'] + $hasil['kosong'];
$persentase_benar = ($total_soal > 0) ? round(($hasil['benar'] / $total_soal) * 100, 1) : 0;
$persentase_salah = ($total_soal > 0) ? round(($hasil['salah'] / $total_soal) * 100, 1) : 0;
$persentase_kosong = ($total_soal > 0) ? round(($hasil['kosong'] / $total_soal) * 100, 1) : 0;

// Status lulus (misal: skor >= 65 atau benar >= 60%)
$is_lulus = ($hasil['skor'] >= 65) || ($persentase_benar >= 60);

// Ambil ranking user
$sql_rank = "SELECT COUNT(*) + 1 as ranking
             FROM hasil_test
             WHERE paket_id = ? AND skor > ?";
$stmt = $conn->prepare($sql_rank);
$stmt->bind_param("ii", $hasil['paket_id'], $hasil['skor']);
$stmt->execute();
$ranking_data = $stmt->get_result()->fetch_assoc();
$ranking = $ranking_data['ranking'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Test - Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/hasil.css">
    <style>
        .result-header {
            background: <?= $is_lulus ? 'var(--gradient-success)' : 'var(--gradient-danger)' ?>;
            color: white;
            padding: 48px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
}

        .skor-display {
            font-size: 80px;
            font-weight: 800;
            background: <?= $is_lulus ? 'var(--gradient-success)' : 'var(--gradient-danger)' ?>;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            line-height: 1;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="result-card">
            <!-- HEADER -->
            <div class="result-header">
                <div class="status-icon">
                    <?= $is_lulus ? '🎉' : '😔' ?>
                </div>
                <h1 class="status-text">
                    <?= $is_lulus ? 'Selamat!' : 'Tetap Semangat!' ?>
                </h1>
                <p class="status-subtitle">
                    <?= $is_lulus ? 'Anda LULUS dengan hasil yang memuaskan!' : 'Jangan menyerah, terus berlatih!' ?>
                </p>
            </div>
            
            <!-- SKOR -->
            <div class="skor-section">
                <div class="skor-display"><?= $hasil['skor'] ?></div>
                <div class="skor-label">Skor Anda</div>
            </div>
            
            <!-- STATS GRID -->
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon green">✓</div>
                    <div class="stat-value"><?= $hasil['benar'] ?></div>
                    <div class="stat-label">Jawaban Benar</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-icon red">✗</div>
                    <div class="stat-value"><?= $hasil['salah'] ?></div>
                    <div class="stat-label">Jawaban Salah</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-icon orange">−</div>
                    <div class="stat-value"><?= $hasil['kosong'] ?></div>
                    <div class="stat-label">Tidak Dijawab</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-icon blue"><i class="fas fa-clock"></i></div>
                    <div class="stat-value"><?= formatWaktu($hasil['waktu_pengerjaan']) ?></div>
                    <div class="stat-label">Waktu Pengerjaan</div>
                </div>
            </div>
            
            <!-- CHART -->
            <div class="chart-section">
                <div class="chart-title">Distribusi Jawaban</div>
                <div class="chart-container">
                    <?php if ($hasil['benar'] > 0): ?>
                    <div class="chart-bar correct" style="width: <?= $persentase_benar ?>%;">
                        <?= $persentase_benar ?>%
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($hasil['salah'] > 0): ?>
                    <div class="chart-bar wrong" style="width: <?= $persentase_salah ?>%;">
                        <?= $persentase_salah ?>%
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($hasil['kosong'] > 0): ?>
                    <div class="chart-bar empty" style="width: <?= $persentase_kosong ?>%;">
                        <?= $persentase_kosong ?>%
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="chart-legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background: #28a745;"></div>
                        <span>Benar (<?= $hasil['benar'] ?>)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #dc3545;"></div>
                        <span>Salah (<?= $hasil['salah'] ?>)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #ffc107;"></div>
                        <span>Kosong (<?= $hasil['kosong'] ?>)</span>
                    </div>
                </div>
            </div>
            
            <!-- INFO BOXES -->
            <div class="info-boxes">
                <div class="info-box blue">
                    <h4>Paket Soal</h4>
                    <p><?= e($hasil['nama_paket']) ?></p>
                </div>
                
                <div class="info-box purple">
                    <h4>Ranking Anda</h4>
                    <p>Peringkat #<?= $ranking ?></p>
                </div>
            </div>
            
            <!-- ACTION BUTTONS -->
            <div class="action-buttons">
                <a href="pembahasan.php?hasil=<?= $hasil_test_id ?>" class="btn btn-primary">
                    <i class="fas fa-book-open"></i> Lihat Pembahasan
                </a>
                <a href="ranking.php?paket=<?= $hasil['paket_id'] ?>" class="btn btn-success">
                    <i class="fas fa-trophy"></i> Lihat Ranking
                </a>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>