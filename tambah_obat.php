<?php
include 'config.php';

// Session check ADMIN ONLY

$success = $error = '';
// Proses tambah obat
if ($_POST) {
    $kode_obat = trim($_POST['kode_obat']);
    $nama_obat = trim($_POST['nama_obat']);
    $kategori = $_POST['kategori'];
    $harga_beli = floatval($_POST['harga_beli']);
    $harga_jual = floatval($_POST['harga_jual']);
    $stok = intval($_POST['stok']);
    $satuan = trim($_POST['satuan']);
    $produsen = trim($_POST['produsen']);
    $tgl_kadaluarsa = $_POST['tanggal_kadaluarsa'] ?: null;

    // Validasi
    if (empty($kode_obat) || empty($nama_obat) || empty($kategori)) {
        $error = 'Kode obat, nama obat, dan kategori wajib diisi!';
    } elseif ($harga_beli >= $harga_jual) {
        $error = 'Harga jual harus lebih besar dari harga beli!';
    } else {
        // Cek jika kode obat sudah ada
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM tambah_obat WHERE kode_obat = ?");
        $stmt_check->execute([$kode_obat]);
        if ($stmt_check->fetchColumn() > 0) {
            $error = 'Kode obat sudah ada!';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO tambah_obat (kode_obat, nama_obat, kategori, harga_beli, harga_jual, stok, satuan, produsen, tanggal_kadaluarsa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$kode_obat, $nama_obat, $kategori, $harga_beli, $harga_jual, $stok, $satuan, $produsen, $tgl_kadaluarsa]);
                $success = "Obat '$nama_obat' berhasil ditambahkan!";
            } catch (PDOException $e) {
                $error = 'Error database!';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Obat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sage-green: #9CAF88;
            --sage-green-dark: #78866B;
            --sage-light: #B8C4B4;
            --sage-bg: #F8FAF5;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--sage-bg) 0%, #F0F4ED 100%);
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, var(--sage-green), var(--sage-green-dark));
            box-shadow: 0 4px 20px rgba(120, 134, 107, 0.2);
        }
        .page-header {
            background: linear-gradient(135deg, white 0%, rgba(156, 175, 136, 0.05) 100%);
            border-radius: 20px;
            border: 1px solid rgba(156, 175, 136, 0.15);
            box-shadow: 0 10px 40px rgba(120, 134, 107, 0.1);
        }
        .form-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(156, 175, 136, 0.15);
            box-shadow: 0 25px 50px rgba(120, 134, 107, 0.15);
        }
        .form-control, .form-select {
            border: 2px solid #E8ECE4;
            border-radius: 14px;
            padding: 1rem;
            background: #FAFDF7;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--sage-green);
            box-shadow: 0 0 0 0.25rem rgba(156, 175, 136, 0.15);
            background: white;
            transform: translateY(-1px);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--sage-green), var(--sage-green-dark));
            border: none;
            border-radius: 14px;
            padding: 1rem 2rem;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(120, 134, 107, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(120, 134, 107, 0.4);
        }
        .input-group-text {
            background: var(--sage-green);
            border-color: var(--sage-green);
            color: white;
        }
    </style>
</head>
<body class="py-4">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="admin.php">
                <i class="bi bi-capsule-pill me-2"></i>Apotek.id
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3 text-white-50">
                    <i class="bi bi-person-circle me-1"></i>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Keluar
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <!-- Header -->
        <div class="page-header p-4 mb-5">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold text-success mb-2">
                        <i class="bi bi-plus-circle me-3"></i>Tambah Obat Baru
                    </h1>
                    <p class="lead text-muted">Kelola stok obat apotek dengan lengkap dan akurat</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="admin.php" class="btn btn-outline-success btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Tambah Obat -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-card p-5">
                    <?php if ($success): ?>
                        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                            <?= $success ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error):?>
                        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row g-4">
                            <!-- Kode & Nama Obat -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted mb-3">Kode Obat *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-capsule-pill"></i></span>
                                    <input type="text" class="form-control" name="kode_obat" 
                                           value="<?= htmlspecialchars($_POST['kode_obat'] ?? 'P' . rand(100,999)) ?>"
                                           maxlength="20" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted mb-3">Nama Obat *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-capsule-pill"></i></span>
                                    <input type="text" class="form-control" name="nama_obat" 
                                           value="<?= htmlspecialchars($_POST['nama_obat'] ?? '') ?>" required>
                                </div>
                            </div>

                            <!-- Kategori & Satuan -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted mb-3">Kategori *</label>
                                <select class="form-select" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Tablet" <?= ($_POST['kategori'] ?? '') == 'Tablet' ? 'selected' : '' ?>>Tablet</option>
                                    <option value="Kapsul" <?= ($_POST['kategori'] ?? '') == 'Kapsul' ? 'selected' : '' ?>>Kapsul</option>
                                    <option value="Sirup" <?= ($_POST['kategori'] ?? '') == 'Sirup' ? 'selected' : '' ?>>Sirup</option>
                                    <option value="Injeksi" <?= ($_POST['kategori'] ?? '') == 'Injeksi' ? 'selected' : '' ?>>Injeksi</option>
                                    <option value="Salep" <?= ($_POST['kategori'] ?? '') == 'Salep' ? 'selected' : '' ?>>Salep</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted mb-3">Satuan</label>
                                <input type="text" class="form-control" name="satuan" 
                                       value="<?= htmlspecialchars($_POST['satuan'] ?? 'Strip') ?>" placeholder="Strip/Box">
                            </div>

                            <!-- Harga -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted mb-3">Harga Beli (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" name="harga_beli" 
                                           value="<?= htmlspecialchars($_POST['harga_beli'] ?? '') ?>" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted mb-3">Harga Jual (Rp) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" name="harga_jual" 
                                           value="<?= htmlspecialchars($_POST['harga_jual'] ?? '') ?>" step="0.01" min="0" required>
                                </div>
                            </div>

                            <!-- Stok & Produsen -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted mb-3">Stok Awal</label>
                                <input type="number" class="form-control" name="stok" 
                                       value="<?= htmlspecialchars($_POST['stok'] ?? '0') ?>" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted mb-3">Produsen</label>
                                <input type="text" class="form-control" name="produsen" 
                                       value="<?= htmlspecialchars($_POST['produsen'] ?? '') ?>" placeholder="PT. Kimia Farma">
                            </div>

                            <!-- Tanggal Kadaluarsa -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted mb-3">Tanggal Kadaluarsa</label>
                                <input type="date" class="form-control" name="tanggal_kadaluarsa" 
                                       value="<?= htmlspecialchars($_POST['tanggal_kadaluarsa'] ?? '') ?>">
                            </div>

                            <!-- Buttons -->
                            <div class="col-12 mt-4">
                                <div class="d-grid gap-3 d-md-flex justify-content-md-end">
                                    <a href="admin.php" class="btn btn-outline-secondary px-4">Batal</a>
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="bi bi-plus-circle me-2"></i>Tambah Obat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto calculate harga jual (1.5x harga beli)
        document.querySelector('input[name="harga_beli"]').addEventListener('input', function() {
            let beli = parseFloat(this.value) || 0;
            document.querySelector('input[name="harga_jual"]').value = (beli * 1.5).toFixed(2);
        });
    </script>
</body>
</html>
