<?php
require '../config/db.php';
cek_akses('kasir'); // Wajib Role Kasir

// Info User
$nama_kasir = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style> body { background-color: #e9f7ef; } .navbar { background-color: #198754; } </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-cash-register me-2"></i>KASIR APOTIK</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3"><i class="fas fa-user-circle"></i> <?= $nama_kasir ?></span>
                <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h2 class="text-success fw-bold mb-4">Selamat Datang, <?= $nama_kasir ?>!</h2>
                <p class="text-muted mb-5">Silakan pilih menu di bawah ini untuk memulai aktivitas.</p>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100 border-success">
                            <div class="card-body p-4">
                                <i class="fas fa-cart-plus fa-4x text-success mb-3"></i>
                                <h4>Transaksi Baru</h4>
                                <p>Buat penjualan obat baru untuk pelanggan.</p>
                                <a href="transaksi.php" class="btn btn-success w-100 stretched-link">Buka Kasir</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body p-4">
                                <i class="fas fa-history fa-4x text-secondary mb-3"></i>
                                <h4>Riwayat Transaksi</h4>
                                <p>Lihat daftar penjualan yang pernah dilakukan.</p>
                                <a href="#" class="btn btn-secondary w-100">Lihat Riwayat</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>