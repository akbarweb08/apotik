<?php
include 'config.php';
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sage-green: #9CAF88;
            --sage-green-dark: #78866B;
        }
        body { background: linear-gradient(135deg, #F8FAF5 0%, #F0F4ED 100%); }
        .navbar {
            background: linear-gradient(135deg, var(--sage-green), var(--sage-green-dark)) !important;
            box-shadow: 0 4px 20px rgba(120, 134, 107, 0.2);
        }
        .main-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(120, 134, 107, 0.1);
            border: 1px solid rgba(156, 175, 136, 0.15);
        }
        .btn-kasir {
            background: linear-gradient(135deg, var(--sage-green), var(--sage-green-dark));
            border-radius: 15px;
            padding: 1.5rem;
            font-weight: 600;
            height: 120px;
            transition: all 0.3s ease;
        }
        .btn-kasir:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(120, 134, 107, 0.3);
            color: white !important;
        }
    </style>
</head>
<body class="py-4">
    <div class="container">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark rounded-3 mb-4">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="#">
                    <i class="bi bi-capsule-pill me-2"></i>Apotek.id
                </a>
                <div class="navbar-nav ms-auto">
                    <span class="navbar-text me-3 text-white-50">
                        <i class="bi bi-person-circle me-1"></i>Kasir
                    </span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </a>
                </div>
            </div>
        </nav>

        <div class="main-card p-4 p-lg-5">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-success mb-3">
                    <i class="bi bi-cash-coin me-3"></i>Dashboard Kasir
                </h1>
                <p class="lead text-muted">Transaksi penjualan obat cepat dan mudah</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <a href="stok_obat.php" class="btn btn-kasir text-white w-100 text-start">
                        <i class=" bi bi-search fs-2 d-block mb-3"></i>
                        <span>Cari Obat</span>
                    </a>
                </div>

                <div class="col-lg-4 col-md-6">
                    <a href="transaksi.php" class="btn btn-kasir text-white w-100 text-start">
                        <i class="bi bi-cart-plus fs-2 d-block mb-3"></i>
                        <span>Transaksi Baru</span>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <button class="btn btn-kasir text-white w-100 text-start" data-bs-toggle="modal" data-bs-target="#cetakStrukModal">
                        <i class="bi bi-receipt fs-2 d-block mb-3"></i>
                        <span>Cetak Struk</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cetak Struk -->
    <div class="modal fade" id="cetakStrukModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak Ulang Struk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form onsubmit="cetakStruk(event)">
                        <div class="mb-3">
                            <label class="form-label">Kode Transaksi</label>
                            <input type="text" class="form-control" id="kodeTransaksi" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cetak</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cetakStruk(event) {
            event.preventDefault();
            const kode = document.getElementById('kodeTransaksi').value;
            if (kode) {
                window.open(`struk.php?kode=${kode}`, '_blank');
            }
        }
    </script>
</body>
</html>
