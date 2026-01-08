<?php
include 'config.php';

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM tambah_obat WHERE id = ?");
$stmt->execute([$id]);
$obat = $stmt->fetch();

if (!$obat) {
    die("Obat tidak ditemukan!");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Obat</title>
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
        .form-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(156, 175, 136, 0.15);
            box-shadow: 0 25px 50px rgba(120, 134, 107, 0.15);
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
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-card p-5">
                    <h2 class="text-center mb-4">Edit Obat</h2>
                    <form method="POST" action="update_stok.php">
                        <input type="hidden" name="id" value="<?= $obat['id'] ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode Obat</label>
                                <input type="text" class="form-control" name="kode_obat" value="<?= htmlspecialchars($obat['kode_obat']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Obat</label>
                                <input type="text" class="form-control" name="nama_obat" value="<?= htmlspecialchars($obat['nama_obat']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <input type="text" class="form-control" name="kategori" value="<?= htmlspecialchars($obat['kategori']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stok</label>
                                <input type="number" class="form-control" name="stok" value="<?= htmlspecialchars($obat['stok']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Satuan</label>
                                <input type="text" class="form-control" name="satuan" value="<?= htmlspecialchars($obat['satuan']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Harga Beli</label>
                                <input type="number" class="form-control" name="harga_beli" value="<?= htmlspecialchars($obat['harga_beli']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Harga Jual</label>
                                <input type="number" class="form-control" name="harga_jual" value="<?= htmlspecialchars($obat['harga_jual']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Produsen</label>
                                <input type="text" class="form-control" name="produsen" value="<?= htmlspecialchars($obat['produsen']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Kadaluarsa</label>
                                <input type="date" class="form-control" name="tanggal_kadaluarsa" value="<?= htmlspecialchars($obat['tanggal_kadaluarsa']) ?>">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="stok_obat.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>