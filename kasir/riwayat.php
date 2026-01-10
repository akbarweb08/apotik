<?php
require '../config/db.php';
cek_akses('kasir');

$id_user = $_SESSION['id_user'];
// Ambil riwayat hanya milik kasir ini
$query = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_user = '$id_user' ORDER BY tanggal_transaksi DESC LIMIT 50");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>.navbar { background-color: #198754; }</style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-chevron-left me-2"></i>Dashboard</a>
            <span class="text-white">Riwayat Transaksi</span>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="text-success mb-3"><i class="fas fa-history"></i> Transaksi Terakhir Anda</h4>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-success">
                            <tr>
                                <th>No Invoice</th>
                                <th>Waktu</th>
                                <th>Total Bayar</th>
                                <th>Tunai</th>
                                <th>Kembalian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($query) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td class="fw-bold text-primary"><?= $row['no_invoice'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['tanggal_transaksi'])) ?></td>
                                    <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($row['tunai'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($row['kembalian'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted">Belum ada riwayat transaksi.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>