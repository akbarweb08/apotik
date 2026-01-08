<?php
session_start();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            background: linear-gradient(135deg, var(--sage-green), var(--sage-green-dark)) !important;
            box-shadow: 0 4px 20px rgba(120, 134, 107, 0.2);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
            letter-spacing: -0.5px;
        }
        .welcome-card {
            background: linear-gradient(135deg, white 0%, rgba(156, 175, 136, 0.05) 100%);
            border: 1px solid rgba(156, 175, 136, 0.15);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(120, 134, 107, 0.1);
        }
        .stat-card {
            background: linear-gradient(135deg, white 0%, rgba(156, 175, 136, 0.05) 100%);
            border: 1px solid rgba(156, 175, 136, 0.15);
            border-radius: 16px;
            transition: all 0.3s ease;
            height: 120px;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(120, 134, 107, 0.15);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .btn-logout {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="admin.php">
                <i class="bi bi-capsule-pill me-2"></i>Apotek.id
            </a>
            <div class="navbar-nav ms-auto align-items-center">
                <span class="navbar-text me-3 text-white-50">
                    <i class="bi bi-person-circle me-1"></i>
                </span>
                <a href="logout.php" class="btn btn-logout btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Keluar
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4 mb-5">
        <!-- Welcome Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="welcome-card p-4 p-lg-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-5 fw-bold text-success mb-2">
                                Selamat Datang <?= htmlspecialchars('Admin') ?>!
                            </h1>
                            <p class="lead text-muted mb-0">
                                Kelola stok obat, pengguna, dan laporan penjualan apotek dengan mudah.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="stat-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-person-shield fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card p-4 h-100">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-3">
                        <i class="bi bi-capsule-pill"></i>
                    </div>
                    <h3 class="fs-3 fw-bold text-success mb-1">1,247</h3>
                    <p class="text-muted mb-0">Total Obat</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card p-4 h-100">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning mb-3">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <h3 class="fs-3 fw-bold text-success mb-1">Rp 45.7M</h3>
                    <p class="text-muted mb-0">Penjualan Hari Ini</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card p-4 h-100">
                    <div class="stat-icon bg-info bg-opacity-10 text-info mb-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="fs-3 fw-bold text-success mb-1">3</h3>
                    <p class="text-muted mb-0">Pengguna Aktif</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card p-4 h-100">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger mb-3">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h3 class="fs-3 fw-bold text-success mb-1">12</h3>
                    <p class="text-muted mb-0">Obat Habis</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="fw-bold text-success mb-4">
                    <i class="bi bi-lightning-charge me-2"></i>Aksi Cepat
                </h3>
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <a href="tambah_obat.php" class="btn btn-outline-success w-100 h-100 p-4 text-start">
                            <i class="bi bi-plus-circle fs-1 d-block mb-3"></i>
                            <span class="fw-semibold">Tambah Obat</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="kelola_karyawan.php" class="btn btn-outline-success w-100 h-100 p-4 text-start">
                            <i class="bi bi-person-plus fs-1 d-block mb-3"></i>
                            <span class="fw-semibold">Kelola Karyawan</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="laporan.php" class="btn btn-outline-success w-100 h-100 p-4 text-start">
                            <i class="bi bi-file-earmark-bar-graph fs-1 d-block mb-3"></i>
                            <span class="fw-semibold">Lihat Laporan</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="stok_obat.php" class="btn btn-outline-success w-100 h-100 p-4 text-start">
                            <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
                            <span class="fw-semibold">Obat</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>