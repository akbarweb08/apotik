<?php
require '../config/db.php';
cek_akses('admin');

if (isset($_POST['import'])) {
    $file = $_FILES['file_csv']['tmp_name'];

    // Validasi apakah ada file
    if (empty($file)) {
        $_SESSION['alert'] = ['error', 'Gagal', 'Pilih file CSV terlebih dahulu'];
        header("Location: obat.php"); exit();
    }

    // Buka file CSV
    $handle = fopen($file, "r");
    $berhasil = 0;

    // Loop baris per baris
    // fgetcsv akan memecah data berdasarkan koma (,) atau titik koma (;)
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Asumsi urutan kolom di CSV:
        // [0]Kode, [1]Nama, [2]Produsen, [3]Kategori, [4]Satuan, [5]Stok, [6]Beli, [7]Jual, [8]Exp

        // Skip baris header (jika baris pertama adalah judul kolom)
        if ($row[0] == 'Kode Obat' || $row[0] == 'kode_obat') continue;

        $kode = clean_data($row[0]);
        $nama = clean_data($row[1]);
        $produsen = clean_data($row[2]);
        $kategori = clean_data($row[3]);
        $satuan = clean_data($row[4]);
        $stok = clean_data($row[5]);
        $beli = clean_data($row[6]);
        $jual = clean_data($row[7]);
        $exp  = clean_data($row[8]); // Format harus YYYY-MM-DD

        // Cek apakah kode obat sudah ada?
        $cek = mysqli_query($conn, "SELECT kode_obat FROM obat WHERE kode_obat = '$kode'");
        if (mysqli_num_rows($cek) == 0) {
            // Insert Baru
            $sql = "INSERT INTO obat (kode_obat, nama_obat, produsen, kategori, satuan, stok, harga_beli, harga_jual, tanggal_kadaluarsa) 
                    VALUES ('$kode', '$nama', '$produsen', '$kategori', '$satuan', '$stok', '$beli', '$jual', '$exp')";
            mysqli_query($conn, $sql);
            $berhasil++;
        }
    }

    fclose($handle);
    $_SESSION['alert'] = ['success', 'Selesai', "$berhasil data obat berhasil diimpor!"];
    header("Location: obat.php"); exit();
}
?>