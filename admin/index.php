<?php
require '../config/db.php';
cek_akses('admin'); // Hanya Admin yang boleh masuk

// Hitung data ringkasan
$jumlah_obat = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM obat"));
$stok_tipis = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM obat WHERE stok < 10"));
// Hitung total transaksi hari ini
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
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Obat</h6>
                                <h2 class="fw-bold"><?= $jumlah_obat ?></h2>
                            </div>
                            <i class="fas fa-pills fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="obat.php" class="text-white text-decoration-none small">Lihat Detail <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card bg-warning text-dark shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Stok Menipis (< 10)</h6>
                                <h2 class="fw-bold"><?= $stok_tipis ?></h2>
                            </div>
                            <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="obat.php" class="text-dark text-decoration-none small">Cek Segera <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card bg-primary text-white shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Omzet Hari Ini</h6>
                                <h2 class="fw-bold">Rp <?= number_format($omzet_hari_ini, 0, ',', '.') ?></h2>
                            </div>
                            <i class="fas fa-cash-register fa-3x opacity-50"></i>
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
                    <a href="#" class="list-group-item list-group-item-action py-3">
                        <i class="fas fa-file-excel me-2 text-success"></i> Laporan & Export Excel
                    </a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>