<?php
include 'config.php';

// Admin only

// Search & Filter
$kategori = $_GET['kategori'] ?? '';
$search = $_GET['search'] ?? '';
$stok_min = $_GET['stok_min'] ?? 0;

// Query stok obat
$sql = "SELECT * FROM tambah_obat WHERE 1=1";
$params = [];
if ($search) {
    $sql .= " AND nama_obat LIKE ?";
    $params[] = "%$search%";
}
if ($kategori) {
    $sql .= " AND kategori = ?";
    $params[] = $kategori;
}
if ($stok_min > 0) {
    $sql .= " AND stok <= ?";
    $params[] = $stok_min;
}
$sql .= " ORDER BY stok ASC, nama_obat ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tambah_obat = $stmt->fetchAll();

$low_stock = array_filter($tambah_obat, fn($o) => $o['stok'] <= 10);
$habis = array_filter($tambah_obat, fn($o) => $o['stok'] == 0);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Obat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sage-green: #9CAF88;
            --sage-green-dark: #78866B;
            --sage-light: #B8C4B4;
            --sage-bg: #F8FAF5;
            --danger-red: #EF4444;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--sage-bg) 0%, #F0F4ED 100%);
        }
        .navbar { background: linear-gradient(135deg, var(--sage-green), var(--sage-green-dark)); }
        .page-header {
            background: linear-gradient(135deg, white 0%, rgba(156, 175, 136, 0.05) 100%);
            border-radius: 20px;
            border: 1px solid rgba(156, 175, 136, 0.15);
            box-shadow: 0 10px 40px rgba(120, 134, 107, 0.1);
        }
        .stats-card { 
            height: 120px; 
            border-radius: 16px;
            transition: all 0.3s ease;
        }
        .stats-card:hover { transform: translateY(-5px); }
        .low-stock { border-left: 4px solid var(--danger-red); }
        .table-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(156, 175, 136, 0.15);
            box-shadow: 0 20px 40px rgba(120, 134, 107, 0.15);
        }
        .btn-stok { border-radius: 12px; font-weight: 500; }
        .badge-stok-kritis { background: var(--danger-red); color: white; }
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
                <span class="navbar-text me-3 text-white-50">Admin</span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <!-- Header -->
        <div class="page-header p-4 mb-5">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 fw-bold text-success mb-2">
                        <i class="bi bi-box-seam me-2"></i>Manajemen Stok Obat
                    </h1>
                    <p class="text-muted">Pantau stok obat, identifikasi low stock & habis</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="tambah_obat.php" class="btn btn-success me-2">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Obat
                    </a>
                    <a href="admin.php" class="btn btn-outline-success">
                        <i class="bi bi-arrow-left me-2"></i>Dashboard
                    </a>    
                </div>
            </div>
        </div>

        <!-- Stats & Alert -->
        <div class="row g-4 mb-5">
            <!-- Total Obat -->
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-4 bg-white border-success">
                    <i class="bi bi-capsule-pills fs-1 text-success mb-3"></i>
                    <h3 class="fs-2 fw-bold text-success"><?= count($tambah_obat) ?></h3>
                    <p class="text-muted mb-0">Total Obat</p>
                </div>
            </div>
            <!-- Low Stock -->
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-4 bg-warning bg-opacity-10 border-warning low-stock">
                    <i class="bi bi-exclamation-triangle fs-1 text-warning mb-3"></i>
                    <h3 class="fs-2 fw-bold text-warning"><?= count($low_stock) ?></h3>
                    <p class="text-muted mb-0">Low Stock (≤10)</p>
                </div>
            </div>
            <!-- Habis -->
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-4 bg-danger bg-opacity-10 border-danger low-stock">
                    <i class="bi bi-x-circle fs-1 text-danger mb-3"></i>
                    <h3 class="fs-2 fw-bold text-danger"><?= count($habis) ?></h3>
                    <p class="text-muted mb-0">Habis</p>
                </div>
            </div>
            <!-- Search & Filter -->
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-4 d-flex align-items-center h-100">
                    <form method="GET" class="w-100">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari obat..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-success">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table Stok -->
        <div class="table-container">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-success mb-0">
                        <i class="bi bi-list-ul me-2"></i>Daftar Stok Obat
                    </h5>
                    <div class="btn-group">
                        <a href="export_stok.php" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-download me-1"></i>Export Excel
                        </a>
                    </div>
                </div>

                <?php if (empty($tambah_obat)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inboxes fs-1 d-block mb-4 opacity-50"></i>
                        <h5>Belum ada data obat</h5>
                        <a href="tambah_obat.php" class="btn btn-success mt-3">Tambah Obat Pertama</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th>Kode</th>
                                    <th>Obat</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Satuan</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th>Produsen</th>
                                    <th>Tgl EXP</th>
                                    <th>Created At</th>
                                    <th>Update At</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tambah_obat as $item): 
                                    $stok_class = $item['stok'] == 0 ? 'badge-stok-kritis' : 
                                                 ($item['stok'] <= 10 ? 'bg-warning text-dark' : 'bg-success');
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($item['kode_obat']) ?></strong></td>
                                    <td>
                                        <?= htmlspecialchars($item['nama_obat']) ?>
                                        <?php if($item['stok'] <= 10): ?>
                                        <span class="badge bg-warning ms-2">Low Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-success border">
                                            <?= htmlspecialchars($item['kategori']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $stok_class ?> fs-6">
                                            <?= $item['stok'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($item['satuan']) ?></td>
                                    <td>Rp <?= number_format($item['harga_beli'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($item['harga_jual'], 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($item['produsen'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($item['tanggal_kadaluarsa']) ?></td>
                                    <td><?= htmlspecialchars($item['created_at']) ?></td>
                                    <td><?= htmlspecialchars($item['updated_at']) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="edit_stok.php?id=<?= $item['id'] ?>" class="btn btn-outline-primary btn-stok">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button class="btn btn-outline-success btn-stok" onclick="tambahStok(<?= $item['id'] ?>)">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function tambahStok(id) {
            const stok = prompt('Masukkan jumlah stok tambahan:');
            if (stok && !isNaN(stok) && stok > 0) {
                window.location.href = `update_stok.php?id=${id}&qty=${stok}`;
            }
        }
    </script>
</body>
</html>
