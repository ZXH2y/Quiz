<?php
/**
 * =====================================================
 * PEMBAHASAN.PHP - Pembahasan Soal Detail
 * =====================================================
 * Fitur:
 * - Tampilkan semua soal dengan jawaban user
 * - Highlight jawaban benar/salah
 * - Pembahasan detail setiap soal
 * - Filter soal (semua/benar/salah/kosong)
 * =====================================================
 */

require_once 'config.php';
requireLogin();
requireUser();

// Cek parameter
if (!isset($_GET['hasil'])) {
    redirect('dashboard.php');
}

$hasil_test_id = (int)$_GET['hasil'];
$user_id = $_SESSION['user_id'];

// =====================================================
// AMBIL DATA HASIL TEST
// =====================================================
$sql = "SELECT ht.*, ps.nama_paket
        FROM hasil_test ht
        JOIN paket_soal ps ON ht.paket_id = ps.id
        WHERE ht.id = ? AND ht.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $hasil_test_id, $user_id);
$stmt->execute();
$hasil = $stmt->get_result()->fetch_assoc();

if (!$hasil) {
    redirect('dashboard.php');
}

// =====================================================
// AMBIL JAWABAN DETAIL
// =====================================================
$sql = "SELECT jd.*, s.pertanyaan, s.pilihan_a, s.pilihan_b, s.pilihan_c, s.pilihan_d, s.pilihan_e, s.jawaban_benar, s.pembahasan
        FROM jawaban_detail jd
        JOIN soal s ON jd.soal_id = s.id
        WHERE jd.hasil_test_id = ?
        ORDER BY jd.id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hasil_test_id);
$stmt->execute();
$jawaban_result = $stmt->get_result();

// Filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembahasan - <?= e($hasil['nama_paket']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
        }
        
        /* NAVBAR */
        .navbar {
            background: linear-gradient(135deg, #030712 0%, #2a5298 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-title h2 {
            font-size: 24px;
        }
        
        .navbar-title p {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }
        
        .btn-back {
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-back:hover {
            background: rgba(255,255,255,0.3);
        }
        
        /* CONTAINER */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        /* SUMMARY */
        .summary-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            align-items: center;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .summary-value.green { color: #28a745; }
        .summary-value.red { color: #dc3545; }
        .summary-value.orange { color: #ffc107; }
        
        .summary-label {
            color: #666;
            font-size: 14px;
        }
        
        /* FILTER */
        .filter-bar {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-label {
            font-weight: 600;
            color: #333;
        }
        
        .filter-btn {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            text-decoration: none;
            color: #333;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-btn:hover {
            border-color: #2a5298;
            color: #2a5298;
        }
        
        .filter-btn.active {
            background: #2a5298;
            color: white;
            border-color: #2a5298;
        }
        
        /* SOAL ITEM */
        .soal-item {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .soal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        
        .soal-number {
            font-size: 18px;
            font-weight: 600;
            color: #2a5298;
        }
        
        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-badge.correct {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.wrong {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-badge.empty {
            background: #fff3cd;
            color: #856404;
        }
        
        .pertanyaan {
            font-size: 16px;
            line-height: 1.8;
            color: #333;
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #2a5298;
        }
        
        .pilihan-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 25px;
        }
        
        .pilihan-item {
            padding: 15px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .pilihan-item .pilihan-label {
            font-weight: 700;
            width: 30px;
        }
        
        .pilihan-item.correct {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        .pilihan-item.wrong {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        
        .pilihan-item.user-answer {
            border-width: 3px;
        }
        
        .pilihan-icon {
            margin-left: auto;
            font-size: 18px;
        }
        
        .pembahasan-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .pembahasan-title {
            font-weight: 600;
            color: #1976d2;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .pembahasan-text {
            color: #333;
            line-height: 1.8;
        }
        
        .no-pembahasan {
            color: #999;
            font-style: italic;
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .summary-card {
                flex-direction: column;
                gap: 20px;
            }
            
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-btn {
                justify-content: center;
            }
            
            .navbar-content {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <div class="navbar">
        <div class="navbar-content">
            <div class="navbar-title">
                <h2><i class="fas fa-book-open"></i> Pembahasan Soal</h2>
                <p><?= e($hasil['nama_paket']) ?></p>
            </div>
            <a href="dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <!-- CONTAINER -->
    <div class="container">
        <!-- SUMMARY -->
        <div class="summary-card">
            <div class="summary-item">
                <div class="summary-value green">✓ <?= $hasil['benar'] ?></div>
                <div class="summary-label">Jawaban Benar</div>
            </div>
            <div class="summary-item">
                <div class="summary-value red">✗ <?= $hasil['salah'] ?></div>
                <div class="summary-label">Jawaban Salah</div>
            </div>
            <div class="summary-item">
                <div class="summary-value orange">− <?= $hasil['kosong'] ?></div>
                <div class="summary-label">Tidak Dijawab</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" style="color: #2a5298;"><?= $hasil['skor'] ?></div>
                <div class="summary-label">Total Skor</div>
            </div>
        </div>
        
        <!-- FILTER -->
        <div class="filter-bar">
            <span class="filter-label"><i class="fas fa-filter"></i> Filter:</span>
            <a href="?hasil=<?= $hasil_test_id ?>&filter=all" class="filter-btn <?= $filter == 'all' ? 'active' : '' ?>">
                <i class="fas fa-list"></i> Semua Soal
            </a>
            <a href="?hasil=<?= $hasil_test_id ?>&filter=correct" class="filter-btn <?= $filter == 'correct' ? 'active' : '' ?>">
                <i class="fas fa-check"></i> Benar
            </a>
            <a href="?hasil=<?= $hasil_test_id ?>&filter=wrong" class="filter-btn <?= $filter == 'wrong' ? 'active' : '' ?>">
                <i class="fas fa-times"></i> Salah
            </a>
            <a href="?hasil=<?= $hasil_test_id ?>&filter=empty" class="filter-btn <?= $filter == 'empty' ? 'active' : '' ?>">
                <i class="fas fa-minus"></i> Kosong
            </a>
        </div>
        
        <!-- SOAL LIST -->
        <?php 
        $nomor = 1;
        while ($jawab = $jawaban_result->fetch_assoc()): 
            // Filter
            if ($filter == 'correct' && !$jawab['is_correct']) continue;
            if ($filter == 'wrong' && ($jawab['is_correct'] || empty($jawab['jawaban_user']))) continue;
            if ($filter == 'empty' && !empty($jawab['jawaban_user'])) continue;
            
            $is_correct = $jawab['is_correct'];
            $is_empty = empty($jawab['jawaban_user']);
            $status_class = $is_empty ? 'empty' : ($is_correct ? 'correct' : 'wrong');
            $status_text = $is_empty ? 'Tidak Dijawab' : ($is_correct ? 'Benar' : 'Salah');
            $status_icon = $is_empty ? '−' : ($is_correct ? '✓' : '✗');
        ?>
        <div class="soal-item">
            <div class="soal-header">
                <div class="soal-number">Soal Nomor <?= $nomor++ ?></div>
                <div class="status-badge <?= $status_class ?>">
                    <span><?= $status_icon ?></span>
                    <span><?= $status_text ?></span>
                </div>
            </div>
            
            <div class="pertanyaan">
                <?= nl2br(e($jawab['pertanyaan'])) ?>
            </div>
            
            <div class="pilihan-list">
                <?php 
                $pilihan = ['A', 'B', 'C', 'D', 'E'];
                foreach ($pilihan as $p): 
                    $pilihan_text = $jawab['pilihan_' . strtolower($p)];
                    $is_jawaban_benar = ($p == $jawab['jawaban_benar']);
                    $is_jawaban_user = ($p == $jawab['jawaban_user']);
                    
                    $class = '';
                    $icon = '';
                    
                    if ($is_jawaban_benar) {
                        $class = 'correct';
                        $icon = '<i class="fas fa-check-circle pilihan-icon" style="color: #28a745;"></i>';
                    }
                    
                    if ($is_jawaban_user && !$is_correct) {
                        $class = 'wrong user-answer';
                        $icon = '<i class="fas fa-times-circle pilihan-icon" style="color: #dc3545;"></i>';
                    } elseif ($is_jawaban_user) {
                        $class .= ' user-answer';
                    }
                ?>
                <div class="pilihan-item <?= $class ?>">
                    <span class="pilihan-label"><?= $p ?>.</span>
                    <span><?= e($pilihan_text) ?></span>
                    <?= $icon ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (!empty($jawab['pembahasan'])): ?>
            <div class="pembahasan-box">
                <div class="pembahasan-title">
                    <i class="fas fa-lightbulb"></i> Pembahasan:
                </div>
                <div class="pembahasan-text">
                    <?= nl2br(e($jawab['pembahasan'])) ?>
                </div>
            </div>
            <?php else: ?>
            <div class="pembahasan-box">
                <div class="pembahasan-text no-pembahasan">
                    <i class="fas fa-info-circle"></i> Pembahasan belum tersedia untuk soal ini.
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
        
        <?php if ($nomor == 1): ?>
        <div class="soal-item" style="text-align: center; padding: 60px;">
            <i class="fas fa-inbox" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: #666; margin-bottom: 10px;">Tidak ada soal yang ditampilkan</h3>
            <p style="color: #999;">Coba ubah filter untuk melihat soal lainnya</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>