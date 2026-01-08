<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // From edit_stok.php
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $kode_obat = $_POST['kode_obat'];
        $nama_obat = $_POST['nama_obat'];
        $kategori = $_POST['kategori'];
        $stok = intval($_POST['stok']);
        $satuan = $_POST['satuan'];
        $harga_beli = floatval($_POST['harga_beli']);
        $harga_jual = floatval($_POST['harga_jual']);
        $produsen = $_POST['produsen'];
        $tanggal_kadaluarsa = $_POST['tanggal_kadaluarsa'];

        $stmt = $pdo->prepare("UPDATE tambah_obat SET kode_obat = ?, nama_obat = ?, kategori = ?, stok = ?, satuan = ?, harga_beli = ?, harga_jual = ?, produsen = ?, tanggal_kadaluarsa = ? WHERE id = ?");
        $stmt->execute([$kode_obat, $nama_obat, $kategori, $stok, $satuan, $harga_beli, $harga_jual, $produsen, $tanggal_kadaluarsa, $id]);
    }
} else {
    // From tambahStok() in stok_obat.php
    if (isset($_GET['id']) && isset($_GET['qty'])) {
        $id = intval($_GET['id']);
        $qty = intval($_GET['qty']);

        $stmt = $pdo->prepare("UPDATE tambah_obat SET stok = stok + ? WHERE id = ?");
        $stmt->execute([$qty, $id]);
    }
}

header("Location: stok_obat.php");
exit;
?>