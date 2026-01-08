<?php
include 'config.php';

$kode_transaksi = $_GET['kode'] ?? '';

if (empty($kode_transaksi)) {
    die("Kode transaksi tidak valid.");
}

$stmt = $pdo->prepare("SELECT t.*, u.username FROM transaksi t JOIN users u ON t.user_id = u.id WHERE t.kode_transaksi = ?");
$stmt->execute([$kode_transaksi]);
$transaksi = $stmt->fetch();

if (!$transaksi) {
    die("Transaksi tidak ditemukan.");
}

$stmt_detail = $pdo->prepare("SELECT td.*, o.nama_obat FROM transaksi_detail td JOIN tambah_obat o ON td.obat_id = o.id WHERE td.transaksi_id = ?");
$stmt_detail->execute([$transaksi['id']]);
$detail_transaksi = $stmt_detail->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Transaksi</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 0 auto; padding: 20px; }
        h2, h3, p { margin: 0; text-align: center; }
        hr { border: 1px dashed #000; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 5px; }
        .text-right { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <h2>Apotek.id</h2>
    <p>Jl. Sehat Selalu No. 1</p>
    <hr>
    <p>Kode: <?= htmlspecialchars($transaksi['kode_transaksi']) ?></p>
    <p>Kasir: <?= htmlspecialchars($transaksi['username']) ?></p>
    <p>Tanggal: <?= date('d/m/Y H:i:s', strtotime($transaksi['tanggal_transaksi'])) ?></p>
    <hr>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detail_transaksi as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['nama_obat']) ?></td>
                <td><?= $item['qty'] ?></td>
                <td class="text-right">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    <h3>Total: Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?></h3>
    <hr>
    <p>Terima kasih!</p>
</body>
</html>