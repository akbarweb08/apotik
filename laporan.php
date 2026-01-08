<?php
include 'config.php';

// Admin only

// Filter tanggal
$tanggal_dari = $_GET['dari'] ?? date('Y-m-01');
$tanggal_sampai = $_GET['sampai'] ?? date('Y-m-d');

// Query laporan
$stmt = $pdo->prepare("
    SELECT 
        t.*,
        u.username,
        COUNT(td.id) as detail_count,
        SUM(td.subtotal) as total_penjualan
    FROM transaksi t 
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN transaksi_detail td ON t.id = td.transaksi_id
    WHERE t.tanggal_transaksi BETWEEN ? AND ?
    GROUP BY t.id
    ORDER BY t.tanggal_transaksi DESC
");
$stmt->execute([$tanggal_dari, $tanggal_sampai . ' 23:59:59']);
$transaksi = $stmt->fetchAll();

$total_penjualan = array_sum(array_column($transaksi, 'total_harga'));
$total_transaksi = count($transaksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - SageMed Apotek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .stats-card {
            background: linear-gradient(135deg, white 0%, rgba(156, 175, 136, 0.05) 100%);
            border: 1px solid rgba(156, 175, 136, 0.15);
            border-radius: 20px;
            height: 140px;
            transition: all 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(120, 134, 107, 0.2);
        }
        .table-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(156, 175, 136, 0.15);
            box-shadow: 0 20px 40px rgba(120, 134, 107, 0.15);
        }
        .chart-container {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(120, 134, 107, 0.1);
        }
    </style>
</head>
<body class="py-4">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="admin.php">
                <i class="bi bi-capsule-pill me-2"></i>SageMed Apotek
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3 text-white-50">(Admin)
                </span>
                <a href="admin.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Keluar
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <!-- Header & Filter -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 fw-bold text-success mb-2">
                                <i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan Penjualan
                            </h1>
                            <p class="text-muted mb-0">Analisis penjualan apotek periode <?= date('d/m/Y', strtotime($tanggal_dari)) ?> - <?= date('d/m/Y', strtotime($tanggal_sampai)) ?></p>
                        </div>
                        <div class="col-md-4">
                            <div class="row g-2">
                                <form method="GET" class="col-md-8">
                                    <input type="date" name="dari" class="form-control" value="<?= $tanggal_dari ?>">
                                </form>
                                <form method="GET" class="col-md-4">
                                    <input type="date" name="sampai" class="form-control" value="<?= $tanggal_sampai ?>">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-4 text-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 70px; height: 70px;">
                        <i class="bi bi-currency-exchange fs-2"></i>
                    </div>
                    <h3 class="fs-2 fw-bold text-success mb-1">Rp <?= number_format($total_penjualan, 0, ',', '.') ?></h3>
                    <p class="text-muted mb-0">Total Penjualan</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-4 text-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 70px; height: 70px;">
                        <i class="bi bi-receipt fs-2"></i>
                    </div>
                    <h3 class="fs-2 fw-bold text-primary mb-1"><?= $total_transaksi ?></h3>
                    <p class="text-muted mb-0">Transaksi</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-4 text-center">
                    <div class="stat-icon bg-info bg-opacity-10 text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 70px; height: 70px;">
                        <i class="bi bi-cart-check fs-2"></i>
                    </div>
                    <h3 class="fs-2 fw-bold text-info mb-1"><?= array_sum(array_column($transaksi, 'total_item')) ?></h3>
                    <p class="text-muted mb-0">Total Item</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-4 text-center">
                    <a href="#" class="btn btn-success w-100" onclick="exportCSV()">
                        <i class="bi bi-download me-2"></i>Export CSV
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Chart -->
            <div class="col-lg-8">
                <div class="chart-container">
                    <h5 class="fw-bold text-success mb-4">Grafik Penjualan Harian</h5>
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>

            <!-- Table Transaksi -->
            <div class="col-lg-4">
                <div class="table-card p-4 h-100">
                    <h5 class="fw-bold text-success mb-4">Transaksi Terbaru</h5>
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-hover table-sm">
                            <thead class="table-success">
                                <tr>
                                    <th>Kode</th>
                                    <th>Kasir</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($transaksi, 0, 10) as $trx): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($trx['kode_transaksi']) ?></strong></td>
                                    <td><?= htmlspecialchars($trx['username'] ?: '-') ?></td>
                                    <td><strong>Rp <?= number_format($trx['total_harga'], 0, ',', '.') ?></strong></td>
                                    <td>
                                        <span class="badge <?= $trx['status']=='Sukses' ? 'bg-success' : 'bg-warning' ?>">
                                            <?= ucfirst($trx['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Chart.js
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['28 Dec', '29 Dec', '30 Dec', '31 Dec'],
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: [47000, 85000, 5000, 65000],
                    borderColor: '#9CAF88',
                    backgroundColor: 'rgba(156, 175, 136, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        function exportCSV() {
            window.location.href = 'export_laporan.php?dari=<?= $tanggal_dari ?>&sampai=<?= $tanggal_sampai ?>';
        }
    </script>
</body>
</html>
