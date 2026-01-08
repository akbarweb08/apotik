<?php
include 'config.php';

$tanggal_dari = $_GET['dari'] ?? date('Y-m-01');
$tanggal_sampai = $_GET['sampai'] ?? date('Y-m-d');

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

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="export_laporan_penjualan.csv"');

$output = fopen('php://output', 'w');

// Header
fputcsv($output, [
    'Kode Transaksi',
    'Kasir',
    'Tanggal Transaksi',
    'Total Item',
    'Total Harga',
    'Metode Pembayaran',
    'Status'
]);

// Data
foreach ($transaksi as $trx) {
    fputcsv($output, [
        $trx['kode_transaksi'],
        $trx['username'],
        $trx['tanggal_transaksi'],
        $trx['total_item'],
        $trx['total_harga'],
        $trx['metode_pembayaran'],
        $trx['status']
    ]);
}

fclose($output);
exit;
?>