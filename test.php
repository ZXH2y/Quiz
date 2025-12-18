<?php
/**
 * =====================================================
 * TEST.PHP - Halaman Ujian
 * =====================================================
 * Fitur:
 * - Timer countdown
 * - Soal teracak setiap user (shuffle)
 * - Navigasi soal (nomor soal)
 * - Simpan jawaban sementara di session
 * - Auto submit saat waktu habis
 * - Submit manual oleh user
 * =====================================================
 */

require_once 'config.php';
requireLogin();
requireUser();

$user_id = $_SESSION['user_id'];

// Cek parameter paket
if (!isset($_GET['paket'])) {
    redirect('dashboard.php');
}

$paket_id = (int)$_GET['paket'];

// =====================================================
// AMBIL DATA PAKET
// =====================================================
$sql = "SELECT * FROM paket_soal WHERE id = ? AND status = 'aktif'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $paket_id);
$stmt->execute();
$paket = $stmt->get_result()->fetch_assoc();

if (!$paket) {
    redirect('dashboard.php');
}

// proses submit jawaban
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_test'])) {
    $jawaban_user = $_POST['jawaban'] ?? [];
    $waktu_pengerjaan = (int)$_POST['waktu_pengerjaan'];
    
    // Ambil soal berdasarkan urutan yang tersimpan di session
    $urutan_soal = $_SESSION['urutan_soal_' . $paket_id];
    $sql = "SELECT * FROM soal WHERE id IN (" . implode(',', $urutan_soal) . ")";
    $result = $conn->query($sql);
    
    $soal_data = [];
    while ($row = $result->fetch_assoc()) {
        $soal_data[$row['id']] = $row;
    }
    
    // Hitung skor
    $benar = 0;
    $salah = 0;
    $kosong = 0;
    
    foreach ($urutan_soal as $soal_id) {
        if (!isset($jawaban_user[$soal_id]) || empty($jawaban_user[$soal_id])) {
            $kosong++;
        } elseif ($jawaban_user[$soal_id] == $soal_data[$soal_id]['jawaban_benar']) {
            $benar++;
        } else {
            $salah++;
        }
    }
    
    // Hitung skor (misal: benar * 5 poin, salah tidak mengurangi)
    $skor = $benar * 5;
    
    // Simpan ke database hasil_test
    $sql = "INSERT INTO hasil_test (user_id, paket_id, skor, benar, salah, kosong, waktu_pengerjaan) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiiii", $user_id, $paket_id, $skor, $benar, $salah, $kosong, $waktu_pengerjaan);
    $stmt->execute();
    $hasil_test_id = $conn->insert_id;
    
    // Simpan detail jawaban
    foreach ($urutan_soal as $soal_id) {
        $jawaban = isset($jawaban_user[$soal_id]) ? $jawaban_user[$soal_id] : null;
        $is_correct = ($jawaban == $soal_data[$soal_id]['jawaban_benar']) ? 1 : 0;
        
        $sql = "INSERT INTO jawaban_detail (hasil_test_id, soal_id, jawaban_user, is_correct) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisi", $hasil_test_id, $soal_id, $jawaban, $is_correct);
        $stmt->execute();
    }
    
    // Hapus session test
    unset($_SESSION['urutan_soal_' . $paket_id]);
    unset($_SESSION['waktu_mulai_' . $paket_id]);
    
    // Redirect ke hasil
    redirect('hasil.php?id=' . $hasil_test_id);
}

// =====================================================
// INISIALISASI TEST
// =====================================================
// Jika belum ada session urutan soal, buat baru (SOAL TERACAK!)
if (!isset($_SESSION['urutan_soal_' . $paket_id])) {
    $sql = "SELECT id FROM soal WHERE paket_id = ? ORDER BY id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $paket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $soal_ids = [];
    while ($row = $result->fetch_assoc()) {
        $soal_ids[] = $row['id'];
    }
    
    // ACAK URUTAN SOAL (FITUR UTAMA!)
    shuffle($soal_ids);
    
    $_SESSION['urutan_soal_' . $paket_id] = $soal_ids;
    $_SESSION['waktu_mulai_' . $paket_id] = time();
}

// Ambil urutan soal dari session
$urutan_soal = $_SESSION['urutan_soal_' . $paket_id];
$waktu_mulai = $_SESSION['waktu_mulai_' . $paket_id];

// Hitung sisa waktu
$durasi_detik = $paket['durasi_menit'] * 60;
$waktu_berlalu = time() - $waktu_mulai;
$sisa_waktu = $durasi_detik - $waktu_berlalu;

// Jika waktu habis, auto submit
if ($sisa_waktu <= 0) {
    echo "<script>alert('Waktu habis! Test akan otomatis disubmit.'); document.getElementById('formTest').submit();</script>";
}

// Ambil semua soal berdasarkan urutan
$sql = "SELECT * FROM soal WHERE id IN (" . implode(',', $urutan_soal) . ")";
$result = $conn->query($sql);

$soal_list = [];
while ($row = $result->fetch_assoc()) {
    $soal_list[$row['id']] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test: <?= e($paket['nama_paket']) ?> - Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/test.css">
</head>
<body>
    <!-- HEADER -->
    <div class="test-header">
        <div class="header-content">
            <div class="test-title">
                <h2><i class="fas fa-file-alt"></i> <?= e($paket['nama_paket']) ?></h2>
                <p><?= count($urutan_soal) ?> Soal • <?= $paket['durasi_menit'] ?> Menit</p>
            </div>
            <div class="timer-box">
                <div class="timer-label">Sisa Waktu</div>
                <div class="timer-display" id="timer">--:--</div>
            </div>
        </div>
    </div>
    
    <!-- CONTAINER -->
    <div class="container">
        <!-- SIDEBAR NAVIGASI -->
        <div class="sidebar-nav">
            <div class="nav-title">Navigasi Soal</div>
            <div class="nomor-grid" id="nomorGrid">
                <?php foreach ($urutan_soal as $index => $soal_id): ?>
                <div class="nomor-soal" data-index="<?= $index ?>" onclick="goToSoal(<?= $index ?>)">
                    <?= $index + 1 ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-box" style="background: #2a5298;"></div>
                    <span>Soal Aktif</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box" style="background: #28a745;"></div>
                    <span>Sudah Dijawab</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box" style="border: 2px solid #ddd;"></div>
                    <span>Belum Dijawab</span>
                </div>
            </div>
            
            <button onclick="showSubmitModal()" class="btn btn-success" style="width: 100%; margin-top: 20px;">
                <i class="fas fa-paper-plane"></i> Selesai & Submit
            </button>
        </div>
        
        <!-- CONTENT AREA -->
        <div class="content-area">
            <form method="POST" id="formTest">
                <input type="hidden" name="waktu_pengerjaan" id="waktuPengerjaan">
                
                <?php foreach ($urutan_soal as $index => $soal_id): 
                    $soal = $soal_list[$soal_id];
                ?>
                <div class="soal-container" data-index="<?= $index ?>" id="soal-<?= $index ?>">
                    <div class="soal-header">
                        <div class="soal-number">Soal Nomor <?= $index + 1 ?></div>
                    </div>
                    
                    <div class="pertanyaan">
                        <?= nl2br(e($soal['pertanyaan'])) ?>
                    </div>
                    
                    <div class="pilihan-container">
                        <?php 
                        $pilihan = ['A', 'B', 'C', 'D', 'E'];
                        foreach ($pilihan as $p): 
                            $pilihan_text = $soal['pilihan_' . strtolower($p)];
                        ?>
                        <div class="pilihan-item">
                            <input type="radio" 
                                   name="jawaban[<?= $soal_id ?>]" 
                                   value="<?= $p ?>" 
                                   id="soal<?= $index ?>_<?= $p ?>"
                                   onchange="markAnswered(<?= $index ?>)">
                            <label for="soal<?= $index ?>_<?= $p ?>">
                                <strong><?= $p ?>.</strong> <?= e($pilihan_text) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="nav-buttons">
                        <button type="button" class="btn btn-secondary" onclick="prevSoal()" <?= $index == 0 ? 'disabled' : '' ?>>
                            <i class="fas fa-chevron-left"></i> Sebelumnya
                        </button>
                        
                        <?php if ($index < count($urutan_soal) - 1): ?>
                        <button type="button" class="btn btn-primary" onclick="nextSoal()">
                            Selanjutnya <i class="fas fa-chevron-right"></i>
                        </button>
                        <?php else: ?>
                        <button type="button" class="btn btn-success" onclick="showSubmitModal()">
                            <i class="fas fa-paper-plane"></i> Selesai & Submit
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <button type="submit" name="submit_test" id="btnSubmit" style="display: none;">Submit</button>
            </form>
        </div>
    </div>
    
    <!-- MODAL KONFIRMASI -->
    <div class="modal" id="modalSubmit">
        <div class="modal-content">
            <h3><i class="fas fa-question-circle"></i> Konfirmasi Submit</h3>
            <p>Apakah Anda yakin ingin menyelesaikan test ini?<br>
            <strong id="jawabInfo"></strong></p>
            <div class="modal-buttons">
                <button class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button class="btn btn-success" onclick="submitTest()">
                    <i class="fas fa-check"></i> Ya, Submit
                </button>
            </div>
        </div>
    </div>
    
    <script>
        let currentSoal = 0;
        let totalSoal = <?= count($urutan_soal) ?>;
        let sisaWaktu = <?= $sisa_waktu ?>;
        let waktuMulai = <?= $waktu_mulai ?>;
        
        // Tampilkan soal pertama
        showSoal(0);
        
        // Timer countdown
        function updateTimer() {
            if (sisaWaktu <= 0) {
                alert('⏰ Waktu habis! Test akan otomatis disubmit.');
                submitTest();
                return;
            }
            
            let jam = Math.floor(sisaWaktu / 3600);
            let menit = Math.floor((sisaWaktu % 3600) / 60);
            let detik = sisaWaktu % 60;
            
            let display = '';
            if (jam > 0) {
                display = String(jam).padStart(2, '0') + ':';
            }
            display += String(menit).padStart(2, '0') + ':' + String(detik).padStart(2, '0');
            
            document.getElementById('timer').textContent = display;
            
            // Warning jika waktu kurang dari 5 menit
            if (sisaWaktu <= 300) {
                document.getElementById('timer').classList.add('timer-warning');
            }
            
            sisaWaktu--;
        }
        
        setInterval(updateTimer, 1000);
        updateTimer();
        
        // Navigasi soal
        function showSoal(index) {
            document.querySelectorAll('.soal-container').forEach(el => {
                el.classList.remove('active');
            });
            document.getElementById('soal-' + index).classList.add('active');
            
            document.querySelectorAll('.nomor-soal').forEach(el => {
                el.classList.remove('active');
            });
            document.querySelector('.nomor-soal[data-index="' + index + '"]').classList.add('active');
            
            currentSoal = index;
            window.scrollTo(0, 0);
        }
        
        function goToSoal(index) {
            showSoal(index);
        }
        
        function nextSoal() {
            if (currentSoal < totalSoal - 1) {
                showSoal(currentSoal + 1);
            }
        }
        
        function prevSoal() {
            if (currentSoal > 0) {
                showSoal(currentSoal - 1);
            }
        }
        
        // Mark soal sebagai terjawab
        function markAnswered(index) {
            document.querySelector('.nomor-soal[data-index="' + index + '"]').classList.add('answered');
            
            // Update pilihan item
            const container = document.getElementById('soal-' + index);
            container.querySelectorAll('.pilihan-item').forEach(item => {
                item.classList.remove('selected');
            });
            const selectedInput = container.querySelector('input[type="radio"]:checked');
            if (selectedInput) {
                selectedInput.closest('.pilihan-item').classList.add('selected');
            }
        }
        
        // Modal submit
        function showSubmitModal() {
            const answered = document.querySelectorAll('.nomor-soal.answered').length;
            const unanswered = totalSoal - answered;
            
            document.getElementById('jawabInfo').textContent = 
                `Terjawab: ${answered} • Belum: ${unanswered}`;
            
            document.getElementById('modalSubmit').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('modalSubmit').classList.remove('active');
        }
        
        // Submit test
        function submitTest() {
            const waktuPengerjaan = <?= $durasi_detik ?> - sisaWaktu;
            document.getElementById('waktuPengerjaan').value = waktuPengerjaan;
            document.getElementById('btnSubmit').click();
        }
        
        // Cegah back/refresh
        window.onbeforeunload = function() {
            return "Test masih berlangsung. Yakin ingin keluar?";
        };
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                prevSoal();
            } else if (e.key === 'ArrowRight') {
                nextSoal();
            }
        });
    </script>
</body>
</html>