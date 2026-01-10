<?php
require '../config/db.php';
// Cek akses admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php"); exit();
}

$id_transaksi = $_GET['id'];

// 1. Ambil Data Header Transaksi
$q_header = mysqli_query($conn, "SELECT t.*, u.nama_lengkap 
                                 FROM transaksi t 
                                 JOIN users u ON t.id_user = u.id_user 
                                 WHERE t.id_transaksi = '$id_transaksi'");
$header = mysqli_fetch_assoc($q_header);

// 2. Ambil Data Detail Obat
$q_detail = mysqli_query($conn, "SELECT dt.*, o.nama_obat, o.kode_obat 
                                 FROM detail_transaksi dt 
                                 JOIN obat o ON dt.id_obat = o.id_obat 
                                 WHERE dt.id_transaksi = '$id_transaksi'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?= $header['no_invoice'] ?></title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #555; padding: 20px; }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
        }
        
        /* Header Layout */
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        
        .top-title { font-size: 35px; line-height: 35px; color: #198754; font-weight: bold; }
        .info-apotik { font-size: 14px; color: #777; }
        
        /* Table Items */
        .heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        .item td { border-bottom: 1px solid #eee; }
        .total td { border-top: 2px solid #eee; font-weight: bold; }
        
        /* Tombol Print (Hilang saat diprint) */
        .no-print { margin-bottom: 20px; text-align: center; }
        @media print { .no-print { display: none; } .invoice-box { border: none; box-shadow: none; } }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background: #198754; color: white; border: none; cursor: pointer; border-radius: 5px;">
            Cetak Invoice
        </button>
    </div>

    <div class="invoice-box">
        <table>
            <tr>
                <td class="title">
                    <div class="top-title">APOTIK SEHAT</div>
                    <div class="info-apotik">
                        Jl. Kesehatan No. 123, Jakarta<br>
                        Telp: (021) 555-0199<br>
                        Email: info@apotiksehat.com
                    </div>
                </td>
                
                <td>
                    <b>INVOICE #<?= $header['no_invoice'] ?></b><br>
                    Tanggal: <?= date('d F Y', strtotime($header['tanggal_transaksi'])) ?><br>
                    Jam: <?= date('H:i', strtotime($header['tanggal_transaksi'])) ?><br>
                    Kasir: <?= $header['nama_lengkap'] ?>
                </td>
            </tr>
        </table>
        
        <br><br>

        <table cellpadding="0" cellspacing="0">
            <tr class="heading">
                <td width="40%">Item Obat</td>
                <td width="20%" style="text-align: center;">Qty</td>
                <td width="20%" style="text-align: right;">Harga</td>
                <td width="20%" style="text-align: right;">Subtotal</td>
            </tr>

            <?php while($item = mysqli_fetch_assoc($q_detail)): ?>
            <tr class="item">
                <td>
                    <?= $item['nama_obat'] ?><br>
                    <small style="font-size: 12px; color: #999;">Kode: <?= $item['kode_obat'] ?></small>
                </td>
                <td style="text-align: center;"><?= $item['qty'] ?></td>
                <td style="text-align: right;">Rp <?= number_format($item['harga_saat_transaksi'], 0, ',', '.') ?></td>
                <td style="text-align: right;">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endwhile; ?>

            <tr class="total">
                <td colspan="3" style="text-align: right; padding-top: 20px;">TOTAL TAGIHAN :</td>
                <td style="text-align: right; padding-top: 20px; font-size: 18px; color: #198754;">
                    Rp <?= number_format($header['total_bayar'], 0, ',', '.') ?>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">Tunai :</td>
                <td style="text-align: right;">Rp <?= number_format($header['tunai'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">Kembalian :</td>
                <td style="text-align: right;">Rp <?= number_format($header['kembalian'], 0, ',', '.') ?></td>
            </tr>
        </table>

        <br><br><br>
        
        <div style="text-align: center; font-size: 12px; color: #888;">
            <p>Terima kasih atas kunjungan Anda. Semoga lekas sembuh!</p>
            <p><i>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan kecuali ada perjanjian.</i></p>
        </div>
    </div>

</body>
</html>