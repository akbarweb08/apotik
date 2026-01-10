<?php
// Masukan koneksi tanpa cek_akses UI (karena ini file download)
require '../config/db.php';

// Pastikan hanya admin yang bisa download
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

// 1. Set Header agar browser membacanya sebagai file Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_Obat_".date('Y-m-d').".xls");

// 2. Buat Tampilan Tabel HTML (Excel akan merender ini sebagai sel)
?>
<h3>Data Stok Obat - Apotik Sehat</h3>
<table border="1">
    <thead>
        <tr style="background-color: #90EE90;">
            <th>No</th>
            <th>Kode Obat</th>
            <th>Nama Obat</th>
            <th>Produsen</th>
            <th>Kategori</th>
            <th>Satuan</th>
            <th>Stok</th>
            <th>Harga Beli</th>
            <th>Harga Jual</th>
            <th>Kadaluarsa</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $query = mysqli_query($conn, "SELECT * FROM obat ORDER BY nama_obat ASC");
        while ($row = mysqli_fetch_assoc($query)) :
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td>'<?= $row['kode_obat'] ?></td>
            <td><?= $row['nama_obat'] ?></td>
            <td><?= $row['produsen'] ?></td>
            <td><?= $row['kategori'] ?></td>
            <td><?= $row['satuan'] ?></td>
            <td><?= $row['stok'] ?></td>
            <td><?= $row['harga_beli'] ?></td>
            <td><?= $row['harga_jual'] ?></td>
            <td><?= $row['tanggal_kadaluarsa'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>