<?php
require '../config/db.php';
cek_akses('kasir');

// --- PROSES PEMBAYARAN ---
if (isset($_POST['bayar'])) {
    $id_user = $_SESSION['id_user'];
    $no_invoice = "INV-" . date('YmdHis');
    $total_bayar = clean_data($_POST['total_bayar_final']);
    $uang_tunai = clean_data($_POST['uang_tunai']);
    $kembalian = clean_data($_POST['kembalian']);
    $tanggal = date('Y-m-d H:i:s');
    
    $keranjang = json_decode($_POST['isi_keranjang'], true);

    if ($keranjang && count($keranjang) > 0) {
        $query_header = "INSERT INTO transaksi (no_invoice, id_user, tanggal_transaksi, total_bayar, tunai, kembalian) 
                         VALUES ('$no_invoice', '$id_user', '$tanggal', '$total_bayar', '$uang_tunai', '$kembalian')";
        
        if (mysqli_query($conn, $query_header)) {
            $id_transaksi = mysqli_insert_id($conn);

            foreach ($keranjang as $item) {
                $id_obat = $item['id'];
                $qty = $item['qty'];
                $harga = $item['harga'];
                $subtotal = $item['subtotal'];

                mysqli_query($conn, "INSERT INTO detail_transaksi (id_transaksi, id_obat, qty, harga_saat_transaksi, subtotal) 
                                     VALUES ('$id_transaksi', '$id_obat', '$qty', '$harga', '$subtotal')");

                mysqli_query($conn, "UPDATE obat SET stok = stok - $qty WHERE id_obat = '$id_obat'");
            }
            $_SESSION['alert'] = ['success', 'Transaksi Berhasil!', 'Kembalian: Rp ' . number_format($kembalian, 0, ',', '.')];
            header("Location: transaksi.php"); 
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir - Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .bg-gradient-green { background: linear-gradient(to right, #198754, #20c997); color: white; }
        .total-display { font-size: 2.5rem; font-weight: bold; text-align: right; background: #333; color: #0f0; padding: 10px; border-radius: 5px; font-family: 'Courier New', monospace; }
        .select2-container .select2-selection--single { height: 38px !important; }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { line-height: 36px !important; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-success shadow-sm mb-3">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-chevron-left me-2"></i>Dashboard</a>
                <span class="text-white fw-bold ms-3">KASIR APOTIK</span>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white fw-bold text-success">
                        <i class="fas fa-search me-1"></i> Pilih Obat
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Cari Obat</label>
                            <select id="select_obat" class="form-select">
                                <option value="" selected disabled>-- Ketik Nama / Kode Obat --</option>
                                <?php
                                $q_obat = mysqli_query($conn, "SELECT * FROM obat WHERE stok > 0 ORDER BY nama_obat ASC");
                                while ($row = mysqli_fetch_assoc($q_obat)) {
                                    echo "<option value='{$row['id_obat']}' 
                                            data-kode='{$row['kode_obat']}'
                                            data-nama='{$row['nama_obat']}' 
                                            data-harga='{$row['harga_jual']}' 
                                            data-stok='{$row['stok']}'
                                            data-exp='{$row['tanggal_kadaluarsa']}'>
                                            {$row['kode_obat']} - {$row['nama_obat']}
                                          </option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label>Stok</label>
                                <input type="text" id="view_stok" class="form-control" readonly>
                            </div>
                            <div class="col-6 mb-3">
                                <label>Harga</label>
                                <input type="text" id="view_harga" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Jumlah</label>
                            <input type="number" id="input_qty" class="form-control" value="1" min="1">
                        </div>
                        <button type="button" class="btn btn-success w-100" onclick="tambahItem()">
                            <i class="fas fa-cart-plus me-1"></i> Masukan Keranjang
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-gradient-green d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-shopping-cart me-1"></i> Keranjang Belanja</span>
                        <small><?= date('d F Y') ?></small>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="table-responsive flex-grow-1" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Nama Obat</th>
                                        <th width="15%">Harga</th>
                                        <th width="10%">Qty</th>
                                        <th width="20%">Subtotal</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel_keranjang"></tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="row mt-auto">
                            <div class="col-md-6">
                                <div class="total-display mb-2">Rp <span id="label_total">0</span></div>
                            </div>
                            <div class="col-md-6">
                                <form method="POST" id="form_transaksi">
                                    <input type="hidden" name="isi_keranjang" id="isi_keranjang_json">
                                    <input type="hidden" name="total_bayar_final" id="total_bayar_final">
                                    <input type="hidden" name="kembalian" id="kembalian_final">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text fw-bold">Tunai (Rp)</span>
                                        <input type="number" name="uang_tunai" id="uang_tunai" class="form-control form-control-lg" required placeholder="0">
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" name="bayar" class="btn btn-primary btn-lg" id="btn_bayar" disabled>
                                            <i class="fas fa-print me-2"></i> PROSES & BAYAR
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let keranjang = [];
        let totalBelanja = 0;
        let tempId = null, tempNama = null, tempHarga = 0, tempStok = 0, tempExp = null;

        $(document).ready(function() {
            $('#select_obat').select2({ theme: 'bootstrap-5', placeholder: '-- Cari Obat --', allowClear: true });
            $('#select_obat').on('select2:select', function (e) {
                let s = $(this).find(':selected');
                tempId = s.val(); tempNama = s.data('nama'); tempHarga = parseInt(s.data('harga')); tempStok = parseInt(s.data('stok')); tempExp = s.data('exp');
                $('#view_stok').val(tempStok); $('#view_harga').val(new Intl.NumberFormat('id-ID').format(tempHarga));
            });
            $('#select_obat').on('select2:clear', function () { tempId = null; $('#view_stok').val(''); $('#view_harga').val(''); });
        });

        function tambahItem() {
            let qty = parseInt(document.getElementById('input_qty').value);
            if (!tempId) { Swal.fire('Error', 'Pilih obat dulu!', 'error'); return; }
            
            let expDate = new Date(tempExp); let today = new Date(); today.setHours(0,0,0,0);
            if (expDate < today) { Swal.fire('Expired!', `Obat expired tgl ${tempExp}`, 'error'); return; }
            if (qty > tempStok) { Swal.fire('Stok Kurang', `Sisa: ${tempStok}`, 'warning'); return; }
            if (qty <= 0) return;

            let idx = keranjang.findIndex(i => i.id === tempId);
            if (idx !== -1) {
                if (keranjang[idx].qty + qty > tempStok) { Swal.fire('Stok Full', 'Melebihi stok', 'warning'); return; }
                keranjang[idx].qty += qty; keranjang[idx].subtotal = keranjang[idx].qty * tempHarga;
            } else {
                keranjang.push({ id: tempId, nama: tempNama, harga: tempHarga, qty: qty, subtotal: qty * tempHarga });
            }
            renderKeranjang();
            $('#select_obat').val(null).trigger('change'); document.getElementById('input_qty').value = 1;
        }

        function renderKeranjang() {
            let html = ''; totalBelanja = 0;
            keranjang.forEach((i, idx) => {
                totalBelanja += i.subtotal;
                html += `<tr><td>${i.nama}</td><td>${new Intl.NumberFormat('id-ID').format(i.harga)}</td><td>${i.qty}</td>
                         <td class="fw-bold">${new Intl.NumberFormat('id-ID').format(i.subtotal)}</td>
                         <td><button class="btn btn-danger btn-sm" onclick="hapusItem(${idx})"><i class="fas fa-trash"></i></button></td></tr>`;
            });
            $('#tabel_keranjang').html(html);
            $('#label_total').text(new Intl.NumberFormat('id-ID').format(totalBelanja));
            $('#isi_keranjang_json').val(JSON.stringify(keranjang));
            $('#total_bayar_final').val(totalBelanja);
            hitungKembalian();
        }

        function hapusItem(i) { keranjang.splice(i, 1); renderKeranjang(); }
        
        $('#uang_tunai').on('keyup', hitungKembalian);
        function hitungKembalian() {
            let tunai = parseInt($('#uang_tunai').val()) || 0;
            let kembali = tunai - totalBelanja;
            $('#btn_bayar').prop('disabled', !(keranjang.length > 0 && tunai >= totalBelanja));
            $('#kembalian_final').val(tunai >= totalBelanja ? kembali : 0);
        }

        <?php if (isset($_SESSION['alert'])): ?>
            Swal.fire({ icon: '<?= $_SESSION['alert'][0] ?>', title: '<?= $_SESSION['alert'][1] ?>', text: '<?= $_SESSION['alert'][2] ?>' });
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>
    </script>
</body>
</html>