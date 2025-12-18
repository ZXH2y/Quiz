<?php
require_once '../config.php';
requireLogin();
requireAdmin();

$success = '';
$error = '';

// Ambil daftar paket untuk filter
$paket_list = $conn->query("SELECT * FROM paket_soal ORDER BY nama_paket");

// Filter paket
$filter_paket = isset($_GET['paket']) ? (int)$_GET['paket'] : 0;

// Proses Hapus Soal
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("DELETE FROM soal WHERE id = $id")) {
        $success = "Soal berhasil dihapus!";
    } else {
        $error = "Gagal menghapus soal!";
    }
}

// Proses Tambah/Edit Soal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $paket_id = (int)$_POST['paket_id'];
    $pertanyaan = sanitize($_POST['pertanyaan']);
    $pilihan_a = sanitize($_POST['pilihan_a']);
    $pilihan_b = sanitize($_POST['pilihan_b']);
    $pilihan_c = sanitize($_POST['pilihan_c']);
    $pilihan_d = sanitize($_POST['pilihan_d']);
    $pilihan_e = sanitize($_POST['pilihan_e']);
    $jawaban_benar = sanitize($_POST['jawaban_benar']);
    $pembahasan = sanitize($_POST['pembahasan']);
    
    if ($id > 0) {
        // Update
        $sql = "UPDATE soal SET 
                paket_id = ?, 
                pertanyaan = ?, 
                pilihan_a = ?, 
                pilihan_b = ?, 
                pilihan_c = ?, 
                pilihan_d = ?, 
                pilihan_e = ?, 
                jawaban_benar = ?, 
                pembahasan = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssssi", $paket_id, $pertanyaan, $pilihan_a, $pilihan_b, $pilihan_c, 
                         $pilihan_d, $pilihan_e, $jawaban_benar, $pembahasan, $id);
        
        if ($stmt->execute()) {
            $success = "Soal berhasil diupdate!";
        } else {
            $error = "Gagal mengupdate soal!";
        }
    } else {
        // Insert
        $sql = "INSERT INTO soal (paket_id, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, 
                pilihan_e, jawaban_benar, pembahasan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssss", $paket_id, $pertanyaan, $pilihan_a, $pilihan_b, $pilihan_c, 
                         $pilihan_d, $pilihan_e, $jawaban_benar, $pembahasan);
        
        if ($stmt->execute()) {
            $success = "Soal berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan soal!";
        }
    }
}

// Ambil data soal untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_data = $conn->query("SELECT * FROM soal WHERE id = $id")->fetch_assoc();
}

// Ambil daftar soal
$where = $filter_paket > 0 ? "WHERE s.paket_id = $filter_paket" : "";
$sql = "SELECT s.*, p.nama_paket 
        FROM soal s 
        JOIN paket_soal p ON s.paket_id = p.id 
        $where 
        ORDER BY s.paket_id, s.id DESC";
$soal_result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Soal - Admin Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/soal.css">
</head>
<body>
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
            <a href="paket.php" class="menu-item">
                <i class="fas fa-box"></i>
                <span>Paket Soal</span>
            </a>
            <a href="soal.php" class="menu-item active">
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
    
    <div class="main-content">
        <div class="top-bar">
            <h1 class="page-title">Kelola Soal</h1>
            <button class="btn btn-primary" onclick="openModal()">
                <i class="fas fa-plus"></i> Tambah Soal Baru
            </button>
        </div>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-list"></i> Daftar Soal
                </h2>
            </div>
            
            <div class="filter-bar">
                <label style="margin: 0;">
                    <i class="fas fa-filter"></i> Filter Paket:
                </label>
                <select onchange="window.location.href='soal.php?paket=' + this.value">
                    <option value="0">Semua Paket</option>
                    <?php while ($paket = $paket_list->fetch_assoc()): ?>
                    <option value="<?= $paket['id'] ?>" <?= $filter_paket == $paket['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($paket['nama_paket']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Paket</th>
                            <th>Pertanyaan</th>
                            <th>Jawaban</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($soal_result->num_rows > 0): ?>
                            <?php while ($soal = $soal_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $soal['id'] ?></td>
                                <td><?= htmlspecialchars($soal['nama_paket']) ?></td>
                                <td>
                                    <div class="pertanyaan-preview" title="<?= htmlspecialchars($soal['pertanyaan']) ?>">
                                        <?= htmlspecialchars($soal['pertanyaan']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?= strtolower($soal['jawaban_benar']) ?>">
                                        <?= strtoupper($soal['jawaban_benar']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="editSoal(<?= $soal['id'] ?>)" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button onclick="confirmDelete(<?= $soal['id'] ?>)" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 10px;"></i>
                                    <p style="color: #666;">Belum ada soal. Klik "Tambah Soal Baru" untuk memulai.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal Form Tambah/Edit Soal -->
    <div id="modalSoal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Tambah Soal Baru</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <form method="POST" action="" id="formSoal">
                <input type="hidden" name="id" id="soal_id">
                
                <div class="form-group">
                    <label>Pilih Paket Soal <span>*</span></label>
                    <select name="paket_id" id="paket_id" required>
                        <option value="">-- Pilih Paket --</option>
                        <?php
                        $paket_list = $conn->query("SELECT * FROM paket_soal ORDER BY nama_paket");
                        while ($p = $paket_list->fetch_assoc()): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_paket']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Pertanyaan <span>*</span></label>
                    <textarea name="pertanyaan" id="pertanyaan" required placeholder="Tuliskan pertanyaan soal di sini..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Pilihan A <span>*</span></label>
                    <input type="text" name="pilihan_a" id="pilihan_a" required placeholder="Pilihan jawaban A">
                </div>
                
                <div class="form-group">
                    <label>Pilihan B <span>*</span></label>
                    <input type="text" name="pilihan_b" id="pilihan_b" required placeholder="Pilihan jawaban B">
                </div>
                
                <div class="form-group">
                    <label>Pilihan C <span>*</span></label>
                    <input type="text" name="pilihan_c" id="pilihan_c" required placeholder="Pilihan jawaban C">
                </div>
                
                <div class="form-group">
                    <label>Pilihan D <span>*</span></label>
                    <input type="text" name="pilihan_d" id="pilihan_d" required placeholder="Pilihan jawaban D">
                </div>
                
                <div class="form-group">
                    <label>Pilihan E <span>*</span></label>
                    <input type="text" name="pilihan_e" id="pilihan_e" required placeholder="Pilihan jawaban E">
                </div>
                
                <div class="form-group">
                    <label>Jawaban Benar <span>*</span></label>
                    <select name="jawaban_benar" id="jawaban_benar" required>
                        <option value="">-- Pilih Jawaban Benar --</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Pembahasan</label>
                    <textarea name="pembahasan" id="pembahasan" placeholder="Tuliskan pembahasan soal (opsional)"></textarea>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button type="submit" class="btn btn-success" style="flex: 1;">
                        <i class="fas fa-save"></i> Simpan Soal
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
            document.getElementById('modalTitle').textContent = 'Tambah Soal Baru';
            document.getElementById('formSoal').reset();
            document.getElementById('soal_id').value = '';
            document.getElementById('modalSoal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('modalSoal').classList.remove('active');
        }
        
        function editSoal(id) {
            // Fetch data soal via AJAX (simplified version - gunakan data dari PHP)
            fetch(`get_soal.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = 'Edit Soal';
                    document.getElementById('soal_id').value = data.id;
                    document.getElementById('paket_id').value = data.paket_id;
                    document.getElementById('pertanyaan').value = data.pertanyaan;
                    document.getElementById('pilihan_a').value = data.pilihan_a;
                    document.getElementById('pilihan_b').value = data.pilihan_b;
                    document.getElementById('pilihan_c').value = data.pilihan_c;
                    document.getElementById('pilihan_d').value = data.pilihan_d;
                    document.getElementById('pilihan_e').value = data.pilihan_e;
                    document.getElementById('jawaban_benar').value = data.jawaban_benar;
                    document.getElementById('pembahasan').value = data.pembahasan;
                    document.getElementById('modalSoal').classList.add('active');
                })
                .catch(error => {
                    alert('Error loading data soal');
                });
        }
        
        function confirmDelete(id) {
            if (confirm('Yakin ingin menghapus soal ini?\nData yang sudah dihapus tidak dapat dikembalikan!')) {
                window.location.href = 'soal.php?delete=' + id;
            }
        }
        
        // Close modal on outside click
        window.onclick = function(event) {
            const modal = document.getElementById('modalSoal');
            if (event.target == modal) {
                closeModal();
            }
        }
        
        <?php if ($edit_data): ?>
        // Auto open edit modal if edit parameter exists
        document.addEventListener('DOMContentLoaded', function() {
            editSoal(<?= $edit_data['id'] ?>);
        });
        <?php endif; ?>
        
        // =====================================================
// MOBILE SIDEBAR TOGGLE - SOAL PAGE
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

// =====================================================
// MODAL FUNCTIONS (Keep existing functions)
// =====================================================

function openModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Soal Baru';
    document.getElementById('formSoal').reset();
    document.getElementById('soal_id').value = '';
    document.getElementById('modalSoal').classList.add('active');
    // Prevent body scroll when modal is open
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modalSoal').classList.remove('active');
    // Restore body scroll
    document.body.style.overflow = '';
}

function editSoal(id) {
    // Fetch data soal via AJAX
    fetch(`get_soal.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalTitle').textContent = 'Edit Soal';
            document.getElementById('soal_id').value = data.id;
            document.getElementById('paket_id').value = data.paket_id;
            document.getElementById('pertanyaan').value = data.pertanyaan;
            document.getElementById('pilihan_a').value = data.pilihan_a;
            document.getElementById('pilihan_b').value = data.pilihan_b;
            document.getElementById('pilihan_c').value = data.pilihan_c;
            document.getElementById('pilihan_d').value = data.pilihan_d;
            document.getElementById('pilihan_e').value = data.pilihan_e;
            document.getElementById('jawaban_benar').value = data.jawaban_benar;
            document.getElementById('pembahasan').value = data.pembahasan || '';
            document.getElementById('modalSoal').classList.add('active');
            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading data soal. Silakan coba lagi.');
        });
}

function confirmDelete(id) {
    if (confirm('Yakin ingin menghapus soal ini?\nData yang sudah dihapus tidak dapat dikembalikan!')) {
        window.location.href = 'soal.php?delete=' + id;
    }
}

// =====================================================
// MODAL CLOSE ON OUTSIDE CLICK
// =====================================================

window.onclick = function(event) {
    const modal = document.getElementById('modalSoal');
    if (event.target == modal) {
        closeModal();
    }
}

// =====================================================
// KEYBOARD SHORTCUTS
// =====================================================

document.addEventListener('keydown', function(e) {
    // ESC to close modal
    if (e.key === 'Escape') {
        const modal = document.getElementById('modalSoal');
        if (modal && modal.classList.contains('active')) {
            closeModal();
        }
    }
    
    // Ctrl/Cmd + K to open modal (quick add)
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        openModal();
    }
});

// =====================================================
// FORM VALIDATION ENHANCEMENT
// =====================================================

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formSoal');
    
    if (form) {
        // Add custom validation messages
        form.addEventListener('submit', function(e) {
            const paketId = document.getElementById('paket_id').value;
            const pertanyaan = document.getElementById('pertanyaan').value.trim();
            const jawaban = document.getElementById('jawaban_benar').value;
            
            if (!paketId) {
                e.preventDefault();
                alert('Silakan pilih paket soal terlebih dahulu!');
                document.getElementById('paket_id').focus();
                return false;
            }
            
            if (!pertanyaan) {
                e.preventDefault();
                alert('Pertanyaan tidak boleh kosong!');
                document.getElementById('pertanyaan').focus();
                return false;
            }
            
            if (!jawaban) {
                e.preventDefault();
                alert('Silakan pilih jawaban yang benar!');
                document.getElementById('jawaban_benar').focus();
                return false;
            }
            
            // Show loading state on submit button
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                submitBtn.disabled = true;
            }
        });
        
        // Real-time character counter for pertanyaan (optional)
        const pertanyaanField = document.getElementById('pertanyaan');
        if (pertanyaanField) {
            pertanyaanField.addEventListener('input', function() {
                const length = this.value.length;
                if (length > 500) {
                    this.style.borderColor = 'var(--warning)';
                } else {
                    this.style.borderColor = 'var(--border)';
                }
            });
        }
    }
});

// =====================================================
// SMOOTH SCROLL TO TOP AFTER ACTION
// =====================================================

window.addEventListener('load', function() {
    // If there's a success/error message, scroll to it smoothly
    const alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(() => {
            alert.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }
});

// =====================================================
// PREVENT DOUBLE SUBMIT
// =====================================================

let isSubmitting = false;

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formSoal');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
            
            // Reset after 3 seconds (safety)
            setTimeout(() => {
                isSubmitting = false;
            }, 3000);
        });
    }
});

// =====================================================
// ACCESSIBILITY IMPROVEMENTS
// =====================================================

document.addEventListener('DOMContentLoaded', function() {
    // Add aria-label to action buttons
    const editButtons = document.querySelectorAll('.btn-warning');
    editButtons.forEach(btn => {
        if (!btn.getAttribute('aria-label')) {
            btn.setAttribute('aria-label', 'Edit soal');
        }
    });
    
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(btn => {
        if (!btn.getAttribute('aria-label')) {
            btn.setAttribute('aria-label', 'Hapus soal');
        }
    });
    
    // Focus management for modal
    const modal = document.getElementById('modalSoal');
    if (modal) {
        // Focus first input when modal opens
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (modal.classList.contains('active')) {
                        const firstInput = modal.querySelector('select, input, textarea');
                        if (firstInput) {
                            setTimeout(() => firstInput.focus(), 100);
                        }
                    }
                }
            });
        });
        
        observer.observe(modal, { attributes: true });
    }
});
    </script>
</body>
</html>