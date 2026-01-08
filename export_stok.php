<?php
include 'config.php';

$stmt = $pdo->query("SELECT * FROM tambah_obat ORDER BY nama_obat ASC");
$tambah_obat = $stmt->fetchAll();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="export_stok_obat.csv"');

$output = fopen('php://output', 'w');

// Header
fputcsv($output, [
    'Kode Obat',
    'Nama Obat',
    'Kategori',
    'Stok',
    'Satuan',
    'Harga Beli',
    'Harga Jual',
    'Produsen',
    'Tanggal Kadaluarsa',
    'Created At',
    'Updated At'
]);

// Data
foreach ($tambah_obat as $item) {
    fputcsv($output, [
        $item['kode_obat'],
        $item['nama_obat'],
        $item['kategori'],
        $item['stok'],
        $item['satuan'],
        $item['harga_beli'],
        $item['harga_jual'],
        $item['produsen'],
        $item['tanggal_kadaluarsa'],
        $item['created_at'],
        $item['updated_at']
    ]);
}

fclose($output);
exit;
?>