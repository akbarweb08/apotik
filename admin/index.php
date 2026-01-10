<?php
require '../config/db.php';
cek_akses('admin'); // Hanya Admin yang boleh masuk

// Hitung data ringkasan
// 1. Hitung Obat
$jumlah_obat = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM obat"));

// 2. Hitung Stok Tipis
$stok_tipis = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM obat WHERE stok < 10"));

// 3. Hitung Obat Expired (Sudah lewat tanggal ATAU tinggal 30 hari lagi)
$tgl_warning = date('Y-m-d', strtotime('+30 days'));
$query_exp = mysqli_query($conn, "SELECT * FROM obat WHERE tanggal_kadaluarsa <= '$tgl_warning'");
$jumlah_expired = mysqli_num_rows($query_exp);

// 4. Omzet Hari Ini
$hari_ini = date('Y-m-d');
$query_omzet = mysqli_query($conn, "SELECT SUM(total_bayar) as omzet FROM transaksi WHERE DATE(tanggal_transaksi) = '$hari_ini'");
$data_omzet = mysqli_fetch_assoc($query_omzet);
$omzet_hari_ini = $data_omzet['omzet'] ?? 0;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background-color: #198754; }
        .bg-hijau-muda { background-color: #d1e7dd; color: #0f5132; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-clinic-medical me-2"></i>APOTIK SEHAT</a>
            <div class="d-flex">
                <span class="text-white me-3 align-self-center">Halo, <?= $_SESSION['nama'] ?> (Admin)</span>
                <a href="../logout.php" class="btn btn-outline-light btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3 class="mb-4 text-success fw-bold">Dashboard Admin</h3>
        
        <div class="row">
           <div class="container mt-4">
    <?php if ($jumlah_expired > 0): ?>
    <div class="alert alert-danger shadow-sm d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
        <div>
            <h5 class="alert-heading fw-bold">Perhatian!</h5>
            <p class="mb-0">Ada <strong><?= $jumlah_expired ?> obat</strong> yang sudah kadaluarsa atau akan kadaluarsa dalam 30 hari ke depan. Mohon cek stok segera.</p>
        </div>
        <a href="obat.php" class="btn btn-danger ms-auto fw-bold">Cek Obat</a>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body">
                    <h6>Total Obat</h6>
                    <h2 class="fw-bold"><?= $jumlah_obat ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark shadow-sm h-100">
                <div class="card-body">
                    <h6>Stok Menipis</h6>
                    <h2 class="fw-bold"><?= $stok_tipis ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white shadow-sm h-100">
                <div class="card-body">
                    <h6>Hampir Expired</h6>
                    <h2 class="fw-bold"><?= $jumlah_expired ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body">
                    <h6>Omzet Hari Ini</h6>
                    <h2 class="fw-bold">Rp <?= number_format($omzet_hari_ini, 0, ',', '.') ?></h2>
                </div>
            </div>
        </div>
    </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="list-group shadow-sm">
                    <a href="obat.php" class="list-group-item list-group-item-action py-3">
                        <i class="fas fa-box-open me-2 text-success"></i> Kelola Data Obat
                    </a>
                    <a href="karyawan.php" class="list-group-item list-group-item-action py-3">
                        <i class="fas fa-users me-2 text-success"></i> Kelola Karyawan (Kasir)
                    </a>
                    <a href="laporan.php" class="list-group-item list-group-item-action py-3">
                        <i class="fas fa-file-excel me-2 text-success"></i> Laporan & Export Excel
                    </a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>