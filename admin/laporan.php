<?php
require '../config/db.php';
cek_akses('admin');

// Default Tanggal: Hari ini
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01'); // Awal bulan
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : date('Y-m-d'); // Hari ini

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>.navbar { background-color: #198754; }</style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-clinic-medical me-2"></i>APOTIK SEHAT</a>
            <div class="d-flex">
                <a href="index.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
                <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-success"><i class="fas fa-chart-line me-2"></i> Laporan Penjualan</h5>
            </div>
            <div class="card-body">
                
                <form method="GET" class="row g-3 mb-4 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Tampilkan</button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" onclick="window.print()" class="btn btn-secondary w-100"><i class="fas fa-print"></i> Cetak</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-success">
                            <tr>
                                <th>No Invoice</th>
                                <th>Waktu Transaksi</th>
                                <th>Kasir</th>
                                <th class="text-end">Total Belanja</th>
                                <th class="text-end">Tunai</th>
                                <th class="text-end">Kembalian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_omzet = 0;
                            
                            // Query Join untuk ambil nama kasir
                            $query = "SELECT t.*, u.nama_lengkap 
                                      FROM transaksi t 
                                      JOIN users u ON t.id_user = u.id_user 
                                      WHERE DATE(t.tanggal_transaksi) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
                                      ORDER BY t.tanggal_transaksi DESC";
                            
                            $result = mysqli_query($conn, $query);
                            
                            if(mysqli_num_rows($result) > 0):
                                while($row = mysqli_fetch_assoc($result)): 
                                    $total_omzet += $row['total_bayar'];
                            ?>
                            <tr>
                                <td>
    <a href="cetak_invoice.php?id=<?= $row['id_transaksi'] ?>" target="_blank" class="fw-bold text-decoration-none" title="Cetak Invoice">
        <?= $row['no_invoice'] ?> <i class="fas fa-print small ms-1"></i>
    </a>
</td>
                                <td><?= date('d-m-Y H:i', strtotime($row['tanggal_transaksi'])) ?></td>
                                <td><?= $row['nama_lengkap'] ?></td>
                                <td class="text-end fw-bold">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format($row['tunai'], 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format($row['kembalian'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <tr class="table-warning fw-bold">
                                <td colspan="3" class="text-center">TOTAL OMZET PERIODE INI</td>
                                <td class="text-end text-success fs-5">Rp <?= number_format($total_omzet, 0, ',', '.') ?></td>
                                <td colspan="2"></td>
                            </tr>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Tidak ada transaksi pada periode ini.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>