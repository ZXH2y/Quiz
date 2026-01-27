<?php

require_once '../config.php';
requireLogin();
requireAdmin();

$success = '';
$error = '';


if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $sql = "SELECT COUNT(*) as total FROM hasil_test WHERE paket_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $total_test = $stmt->get_result()->fetch_assoc()['total'];
    
    if ($total_test > 0) {
        $error = "Tidak dapat menghapus paket! Ada $total_test test yang menggunakan paket ini.";
    } else {
        if ($conn->query("DELETE FROM paket_soal WHERE id = $id")) {
            $success = "Paket berhasil dihapus!";
        } else {
            $error = "Gagal menghapus paket!";
        }
    }
}


if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $sql = "UPDATE paket_soal SET status = IF(status = 'aktif', 'nonaktif', 'aktif') WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = "Status paket berhasil diubah!";
    } else {
        $error = "Gagal mengubah status!";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nama_paket = sanitize($_POST['nama_paket']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $durasi_menit = (int)$_POST['durasi_menit'];
    $jumlah_soal = (int)$_POST['jumlah_soal'];
    $status = sanitize($_POST['status']);
    
    // upload gambar
    $gambar = '';
    $gambar_lama = isset($_POST['gambar_lama']) ? $_POST['gambar_lama'] : '';
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 3 * 1024 * 1024; // 2MB
        
        $file_type = $_FILES['gambar']['type'];
        $file_size = $_FILES['gambar']['size'];
        
        if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
            $extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $new_filename = 'paket_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
            $upload_path = 'uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $gambar = $new_filename;
                
                // Hapus gambar lama jika ada
                if ($gambar_lama && file_exists('uploads/img' . $gambar_lama)) {
                    unlink('uploads/img' . $gambar_lama);
                }
            } else {
                $error = "Gagal mengupload gambar!";
            }
        } else {
            $error = "File harus berupa gambar (JPG, PNG, GIF) dan maksimal 2MB!";
        }
    } else {
        // Jika tidak ada upload baru, gunakan gambar lama
        $gambar = $gambar_lama;
    }

if ($id > 0) {
    // Update
    $sql = "UPDATE paket_soal SET 
            nama_paket = ?, 
            deskripsi = ?, 
            gambar = ?,
            durasi_menit = ?, 
            jumlah_soal = ?, 
            status = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiisi", $nama_paket, $deskripsi, $gambar, $durasi_menit, $jumlah_soal, $status, $id);
        
        if ($stmt->execute()) {
            $success = "Paket berhasil diupdate!";
        } else {
            $error = "Gagal mengupdate paket!";
        }
} else {
    // Insert
    $sql = "INSERT INTO paket_soal (nama_paket, deskripsi, gambar, durasi_menit, jumlah_soal, status) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiis", $nama_paket, $deskripsi, $gambar, $durasi_menit, $jumlah_soal, $status);
        
        if ($stmt->execute()) {
            $success = "Paket berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan paket!";
        }
    }
}

// =====================================================
// AMBIL DAFTAR PAKET
// =====================================================
$sql = "SELECT p.*, 
        (SELECT COUNT(*) FROM soal WHERE paket_id = p.id) as total_soal,
        (SELECT COUNT(*) FROM hasil_test WHERE paket_id = p.id) as total_test
        FROM paket_soal p 
        ORDER BY p.id DESC";
$paket_result = $conn->query($sql);

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_data = $conn->query("SELECT * FROM paket_soal WHERE id = $id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Paket Soal - Admin Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/paket.css">
</head>
<body>
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>
                <i class="fas fa-graduation-cap"></i>
                <span>Quizly</span>
            </h2>
            <span class="admin-badge">ADMIN PANEL</span>
        </div>
        <nav class="sidebar-menu">
            <a href="index.php" class="menu-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="paket.php" class="menu-item active">
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
        <div class="top-bar">
            <h1 class="page-title">Kelola Paket Soal</h1>
            <button class="btn btn-primary" onclick="openModal()">
                <i class="fas fa-plus"></i> Tambah Paket Baru
            </button>
        </div>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= e($success) ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-list"></i> Daftar Paket Soal
                </h2>
            </div>
            
            <div class="paket-grid">
                <?php while ($paket = $paket_result->fetch_assoc()): ?>
                <div class="paket-card">
                    <div class="paket-header">
                        <?php if (!empty($paket['gambar']) && file_exists('uploads/' . $paket['gambar'])): ?>
                            <img src="uploads/<?= e($paket['gambar']) ?>" alt="<?= e($paket['nama_paket']) ?>" 
                                 style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 15px;">
                        <?php endif; ?>
                        <h3><?= e($paket['nama_paket']) ?></h3>
                        <p><?= e($paket['deskripsi']) ?></p>
                    </div>
                    <div class="paket-body">
                        <span class="paket-status status-<?= $paket['status'] ?>">
                            <?= ucfirst($paket['status']) ?>
                        </span>
                        
                        <div class="paket-info">
                            <div class="info-item">
                                <i class="fas fa-question-circle"></i>
                                <div class="info-value"><?= $paket['total_soal'] ?> / <?= $paket['jumlah_soal'] ?></div>
                                <div class="info-label">Soal Tersedia</div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <div class="info-value"><?= $paket['durasi_menit'] ?></div>
                                <div class="info-label">Menit</div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clipboard-check"></i>
                                <div class="info-value"><?= $paket['total_test'] ?></div>
                                <div class="info-label">Test Dikerjakan</div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <div class="info-value"><?= date('d/m/Y', strtotime($paket['created_at'])) ?></div>
                                <div class="info-label">Dibuat</div>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="?edit=<?= $paket['id'] ?>" class="btn btn-warning btn-sm" onclick="event.preventDefault(); editPaket(<?= $paket['id'] ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?toggle=<?= $paket['id'] ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-power-off"></i> Toggle
                            </a>
                            <button onclick="confirmDelete(<?= $paket['id'] ?>)" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    
    <!-- MODAL FORM -->
    <div id="modalPaket" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Tambah Paket Baru</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
        <form method="POST" action="" enctype="multipart/form-data" id="formPaket">
            <input type="hidden" name="id" id="paket_id">
            <input type="hidden" name="gambar_lama" id="gambar_lama">
                
                <div class="form-group">
                    <label>Nama Paket <span>*</span></label>
                    <input type="text" name="nama_paket" id="nama_paket" placeholder="Masukkan nama paket soal">
                </div>
                
                <div class="form-group">
                    <label>Deskripsi <span>*</span></label>
                    <textarea name="deskripsi" id="deskripsi" placeholder="Deskripsi paket soal..."></textarea>
                </div>

                <div class="form-group">
                     <label>Gambar Paket (Opsional)</label>
                     <input type="file" name="gambar" id="gambar" accept="image/*">
                     <small style="color: #666; font-size: 12px;">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                     <div id="preview_gambar" style="margin-top: 10px;"></div>
                </div>
                
                <div class="form-group">
                    <label>Durasi (Menit) <span>*</span></label>
                    <input type="number" name="durasi_menit" id="durasi_menit" value="90" min="1">
                </div>
                
                <div class="form-group">
                    <label>Jumlah Soal <span>*</span></label>
                    <input type="number" name="jumlah_soal" id="jumlah_soal" value="35" min="1">
                </div>
                
                <div class="form-group">
                    <label>Status <span>*</span></label>
                    <select name="status" id="status">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button type="submit" class="btn btn-success" style="flex: 1;">
                        <i class="fas fa-save"></i> Simpan Paket
                    </button>
                    <button type="button" class="btn btn-danger" onclick="closeModal()">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </form>
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
        
        function openModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Paket Baru';
            document.getElementById('formPaket').reset();
            document.getElementById('paket_id').value = '';
            document.getElementById('modalPaket').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('modalPaket').classList.remove('active');
        }
        
        function editPaket(id) {
            fetch('get_paket.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = 'Edit Paket';
                    document.getElementById('paket_id').value = data.id;
                    document.getElementById('nama_paket').value = data.nama_paket;
                    document.getElementById('deskripsi').value = data.deskripsi;
                    document.getElementById('durasi_menit').value = data.durasi_menit;
                    document.getElementById('jumlah_soal').value = data.jumlah_soal;
                    document.getElementById('status').value = data.status;
                    document.getElementById('gambar_lama').value = data.gambar || '';

                    // Preview gambar existing
                    const previewDiv = document.getElementById('preview_gambar');
                    if (data.gambar) {
                        previewDiv.innerHTML = '<img src="uploads/' + data.gambar + '" style="max-width: 200px; border-radius: 8px;">' +
                                               '<p style="font-size: 12px; color: #666;">Gambar saat ini (upload baru untuk mengganti)</p>';
                    } else {
                        previewDiv.innerHTML = '';
                    }

                    document.getElementById('modalPaket').classList.add('active');
                });
        }
        
        function confirmDelete(id) {
            if (confirm('Yakin ingin menghapus paket ini?\nSemua soal dalam paket akan ikut terhapus!')) {
                window.location.href = 'paket.php?delete=' + id;
            }
        }
        
        // Close modal on outside click
        window.onclick = function(event) {
            const modal = document.getElementById('modalPaket');
            if (event.target == modal) {
                closeModal();
            }
        }
        
        <?php if ($edit_data): ?>
        // Auto open edit modal
        document.addEventListener('DOMContentLoaded', function() {
            editPaket(<?= $edit_data['id'] ?>);
        });
        <?php endif; ?>
        // Preview gambar saat dipilih
        document.getElementById('gambar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewDiv = document.getElementById('preview_gambar');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.innerHTML = '<img src="' + e.target.result + '" style="max-width: 200px; border-radius: 8px; margin-top: 10px;">' +
                                           '<p style="font-size: 12px; color: #28a745;">✓ Gambar baru dipilih</p>';
                }
                reader.readAsDataURL(file);
            } else {
                previewDiv.innerHTML = '';
            }
        });

        // Auto hide alerts dengan animasi
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
        alert.style.transition = 'all 0.5s ease';
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(-20px)';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);

// Toggle Sidebar untuk Mobile
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar') || document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    const body = document.body;
    
    if (!overlay) {
        // Buat overlay jika belum ada
        const newOverlay = document.createElement('div');
        newOverlay.className = 'sidebar-overlay';
        newOverlay.onclick = toggleSidebar;
        document.body.appendChild(newOverlay);
    }
    
    sidebar.classList.toggle('active');
    document.querySelector('.sidebar-overlay').classList.toggle('active');
    body.classList.toggle('sidebar-open');
}

// Tambah toggle button jika belum ada
if (window.innerWidth <= 768) {
    if (!document.querySelector('.sidebar-toggle')) {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'sidebar-toggle';
        toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
        toggleBtn.onclick = toggleSidebar;
        document.body.insertBefore(toggleBtn, document.body.firstChild);
    }
}

// Open Modal dengan animasi
function openModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Paket Baru';
    document.getElementById('formPaket').reset();
    document.getElementById('paket_id').value = '';
    document.getElementById('gambar_lama').value = '';
    document.getElementById('preview_gambar').innerHTML = '';
    
    const modal = document.getElementById('modalPaket');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Close Modal dengan animasi
function closeModal() {
    const modal = document.getElementById('modalPaket');
    const modalContent = modal.querySelector('.modal-content');
    
    // Animasi keluar
    modalContent.style.animation = 'modalSlideDown 0.3s ease';
    setTimeout(() => {
        modal.classList.remove('active');
        modalContent.style.animation = '';
        document.body.style.overflow = '';
    }, 250);
}

// Animasi slide down untuk modal close
const style = document.createElement('style');
style.textContent = `
    @keyframes modalSlideDown {
        from {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        to {
            opacity: 0;
            transform: translateY(50px) scale(0.95);
        }
    }
`;
document.head.appendChild(style);

// Edit Paket dengan loading state
function editPaket(id) {
    // Tampilkan loading indicator
    const btn = event.target.closest('.btn');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<span class="loading"></span> Loading...';
    btn.disabled = true;
    
    fetch('get_paket.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            // Reset button
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            
            // Populate form
            document.getElementById('modalTitle').textContent = 'Edit Paket';
            document.getElementById('paket_id').value = data.id;
            document.getElementById('nama_paket').value = data.nama_paket;
            document.getElementById('deskripsi').value = data.deskripsi;
            document.getElementById('durasi_menit').value = data.durasi_menit;
            document.getElementById('jumlah_soal').value = data.jumlah_soal;
            document.getElementById('status').value = data.status;
            document.getElementById('gambar_lama').value = data.gambar || '';

            // Preview gambar existing
            const previewDiv = document.getElementById('preview_gambar');
            if (data.gambar) {
                previewDiv.innerHTML = `
                    <img src="uploads/${data.gambar}" style="max-width: 200px; border-radius: var(--radius-md); border: 2px solid var(--border);">
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 8px;">
                        <i class="fas fa-info-circle"></i> Gambar saat ini (upload baru untuk mengganti)
                    </p>
                `;
            } else {
                previewDiv.innerHTML = '';
            }

            openModal();
        })
        .catch(error => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            alert('Error loading data: ' + error);
        });
}

// Confirm Delete dengan animasi
function confirmDelete(id) {
    const paketCard = event.target.closest('.paket-card');
    
    if (confirm('⚠️ Yakin ingin menghapus paket ini?\n\nSemua soal dalam paket akan ikut terhapus!')) {
        // Animasi hapus
        paketCard.style.transition = 'all 0.4s ease';
        paketCard.style.opacity = '0';
        paketCard.style.transform = 'scale(0.9)';
        
        setTimeout(() => {
            window.location.href = 'paket.php?delete=' + id;
        }, 400);
    }
}

// Close modal saat klik di luar
window.onclick = function(event) {
    const modal = document.getElementById('modalPaket');
    if (event.target == modal) {
        closeModal();
    }
}

// Preview gambar saat dipilih dengan animasi
document.getElementById('gambar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const previewDiv = document.getElementById('preview_gambar');

    if (file) {
        // Validasi ukuran
        if (file.size > 3 * 1024 * 1024) {
            previewDiv.innerHTML = `
                <p style="color: var(--danger); font-size: 12px; margin-top: 8px;">
                    <i class="fas fa-exclamation-triangle"></i> File terlalu besar! Maksimal 3MB
                </p>
            `;
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewDiv.innerHTML = `
                <img src="${e.target.result}" 
                     style="max-width: 200px; border-radius: var(--radius-md); border: 2px solid var(--border); 
                            animation: scaleIn 0.3s ease;">
                <p style="font-size: 12px; color: var(--success); margin-top: 8px;">
                    <i class="fas fa-check-circle"></i> Gambar baru dipilih
                </p>
            `;
        }
        reader.readAsDataURL(file);
    } else {
        previewDiv.innerHTML = '';
    }
});

// Smooth scroll untuk animasi yang lebih halus
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Animasi card saat scroll (Intersection Observer)
if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1
    });

    // Observe paket cards
    document.querySelectorAll('.paket-card').forEach(card => {
        observer.observe(card);
    });
}

// Loading state untuk form submit
document.getElementById('formPaket').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalHTML = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<span class="loading"></span> Menyimpan...';
    submitBtn.disabled = true;
    
    // Re-enable setelah submit (akan redirect)
    setTimeout(() => {
        submitBtn.innerHTML = originalHTML;
        submitBtn.disabled = false;
    }, 5000);
});

// Tambah ripple effect pada buttons
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            left: ${x}px;
            top: ${y}px;
            pointer-events: none;
            animation: rippleEffect 0.6s ease-out;
        `;
        
        this.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    });
});

// Tambahkan keyframe untuk ripple effect
const rippleStyle = document.createElement('style');
rippleStyle.textContent = `
    @keyframes rippleEffect {
        from {
            transform: scale(0);
            opacity: 1;
        }
        to {
            transform: scale(2);
            opacity: 0;
        }
    }
`;
document.head.appendChild(rippleStyle);

// ESC key untuk close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('modalPaket');
        if (modal.classList.contains('active')) {
            closeModal();
        }
    }
});

// Resize handler untuk responsive
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        // Close sidebar jika resize ke desktop
        if (window.innerWidth > 768) {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            if (sidebar && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            }
        }
    }, 250);
});

console.log('🎨 Quizly Modern Theme Loaded!');

    </script>
    <script src="Js/anti-screenshot.js"></script>
</body>
</html>