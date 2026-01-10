<?php
require '../config/db.php';
cek_akses('kasir');

// --- PROSES PEMBAYARAN (BACKEND) ---
if (isset($_POST['bayar'])) {
    // 1. Ambil Data dari Form
    $id_user = $_SESSION['id_user'];
    $no_invoice = "INV-" . date('YmdHis'); // Contoh: INV-20231025103055
    $total_bayar = clean_data($_POST['total_bayar_final']);
    $uang_tunai = clean_data($_POST['uang_tunai']);
    $kembalian = clean_data($_POST['kembalian']);
    $tanggal = date('Y-m-d H:i:s');
    
    // Decode JSON Keranjang (Dari string JSON menjadi Array PHP)
    $keranjang = json_decode($_POST['isi_keranjang'], true);

    if ($keranjang && count($keranjang) > 0) {
        // 2. Simpan ke Tabel TRANSAKSI (Header)
        $query_header = "INSERT INTO transaksi (no_invoice, id_user, tanggal_transaksi, total_bayar, tunai, kembalian) 
                         VALUES ('$no_invoice', '$id_user', '$tanggal', '$total_bayar', '$uang_tunai', '$kembalian')";
        
        if (mysqli_query($conn, $query_header)) {
            // Ambil ID Transaksi yang baru saja dibuat
            $id_transaksi = mysqli_insert_id($conn);

            // 3. Simpan ke Tabel DETAIL & UPDATE STOK (Looping)
            foreach ($keranjang as $item) {
                $id_obat = $item['id'];
                $qty = $item['qty'];
                $harga = $item['harga']; // Harga saat transaksi
                $subtotal = $item['subtotal'];

                // Insert Detail
                mysqli_query($conn, "INSERT INTO detail_transaksi (id_transaksi, id_obat, qty, harga_saat_transaksi, subtotal) 
                                     VALUES ('$id_transaksi', '$id_obat', '$qty', '$harga', '$subtotal')");

                // Update Stok Obat (Kurangi stok)
                mysqli_query($conn, "UPDATE obat SET stok = stok - $qty WHERE id_obat = '$id_obat'");
            }

            // Sukses
            $_SESSION['alert'] = ['success', 'Transaksi Berhasil!', 'Kembalian: Rp ' . number_format($kembalian, 0, ',', '.')];
            header("Location: transaksi.php"); 
            exit();

        } else {
            $_SESSION['alert'] = ['error', 'Gagal', 'Gagal menyimpan transaksi header.'];
        }
    } else {
        $_SESSION['alert'] = ['error', 'Keranjang Kosong', 'Belum ada obat yang dipilih.'];
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
    <style>
        .bg-gradient-green { background: linear-gradient(to right, #198754, #20c997); color: white; }
        .total-display { font-size: 2.5rem; font-weight: bold; text-align: right; background: #333; color: #0f0; padding: 10px; border-radius: 5px; font-family: 'Courier New', monospace; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-success shadow-sm mb-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-chevron-left me-2"></i>Dashboard</a>
            <span class="text-white fw-bold">KASIR APOTIK</span>
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
                            <label>Cari Obat (Kode / Nama)</label>
                            <input class="form-control" list="datalistOptions" id="input_obat" placeholder="Ketik nama obat...">
                            <datalist id="datalistOptions">
                                <?php
                                $q_obat = mysqli_query($conn, "SELECT * FROM obat WHERE stok > 0 ORDER BY nama_obat ASC");
                                while ($row = mysqli_fetch_assoc($q_obat)) {
                                    // Simpan data harga & stok di atribut data- (trik JS)
                                    echo "<option value='{$row['kode_obat']} - {$row['nama_obat']}' 
                                            data-id='{$row['id_obat']}' 
                                            data-nama='{$row['nama_obat']}' 
                                            data-harga='{$row['harga_jual']}' 
                                            data-stok='{$row['stok']}'>";
                                }
                                ?>
                            </datalist>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label>Stok Tersedia</label>
                                <input type="text" id="view_stok" class="form-control" readonly>
                            </div>
                            <div class="col-6 mb-3">
                                <label>Harga Satuan</label>
                                <input type="text" id="view_harga" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Jumlah Beli (Qty)</label>
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
                                <tbody id="tabel_keranjang">
                                    </tbody>
                            </table>
                        </div>

                        <hr>

                        <div class="row mt-auto">
                            <div class="col-md-6">
                                <div class="total-display mb-2">
                                    Rp <span id="label_total">0</span>
                                </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let keranjang = [];
        let totalBelanja = 0;

        // 1. Deteksi saat obat dipilih dari Datalist
        const inputObat = document.getElementById('input_obat');
        const viewStok = document.getElementById('view_stok');
        const viewHarga = document.getElementById('view_harga');
        
        // Variabel penampung sementara
        let tempId = null;
        let tempNama = null;
        let tempHarga = 0;
        let tempStok = 0;

        inputObat.addEventListener('change', function() {
            // Cari option yang value-nya sama dengan input user
            let val = this.value;
            let options = document.getElementById('datalistOptions').childNodes;
            
            // Reset dulu
            tempId = null; 
            viewStok.value = ''; 
            viewHarga.value = '';

            for (let i = 0; i < options.length; i++) {
                if (options[i].value === val) {
                    tempId = options[i].getAttribute('data-id');
                    tempNama = options[i].getAttribute('data-nama');
                    tempHarga = parseInt(options[i].getAttribute('data-harga'));
                    tempStok = parseInt(options[i].getAttribute('data-stok'));
                    
                    viewStok.value = tempStok;
                    viewHarga.value = new Intl.NumberFormat('id-ID').format(tempHarga);
                    break;
                }
            }
        });

        // 2. Fungsi Tambah ke Keranjang
        function tambahItem() {
            let qty = parseInt(document.getElementById('input_qty').value);

            if (!tempId) {
                Swal.fire('Error', 'Silakan pilih obat terlebih dahulu!', 'error');
                return;
            }
            if (qty > tempStok) {
                Swal.fire('Stok Kurang', `Stok tersisa hanya ${tempStok}`, 'warning');
                return;
            }
            if (qty <= 0) {
                Swal.fire('Error', 'Jumlah beli minimal 1', 'warning');
                return;
            }

            // Cek apakah obat sudah ada di keranjang?
            let indexAda = keranjang.findIndex(item => item.id === tempId);
            
            if (indexAda !== -1) {
                // Jika sudah ada, update qty saja (cek stok lagi)
                if (keranjang[indexAda].qty + qty > tempStok) {
                    Swal.fire('Stok Full', 'Total qty melebihi stok tersedia', 'warning');
                    return;
                }
                keranjang[indexAda].qty += qty;
                keranjang[indexAda].subtotal = keranjang[indexAda].qty * tempHarga;
            } else {
                // Jika belum ada, push baru
                keranjang.push({
                    id: tempId,
                    nama: tempNama,
                    harga: tempHarga,
                    qty: qty,
                    subtotal: qty * tempHarga
                });
            }

            renderKeranjang();
            // Reset form input
            inputObat.value = '';
            viewStok.value = '';
            viewHarga.value = '';
            document.getElementById('input_qty').value = 1;
            tempId = null;
        }

        // 3. Render Tabel Keranjang
        function renderKeranjang() {
            let tbody = document.getElementById('tabel_keranjang');
            tbody.innerHTML = '';
            totalBelanja = 0;

            keranjang.forEach((item, index) => {
                totalBelanja += item.subtotal;
                
                let tr = `
                    <tr>
                        <td>${item.nama}</td>
                        <td>${new Intl.NumberFormat('id-ID').format(item.harga)}</td>
                        <td>${item.qty}</td>
                        <td class="fw-bold">${new Intl.NumberFormat('id-ID').format(item.subtotal)}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="hapusItem(${index})"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += tr;
            });

            // Update Tampilan Total
            document.getElementById('label_total').innerText = new Intl.NumberFormat('id-ID').format(totalBelanja);
            
            // Update Input Hidden untuk Form
            document.getElementById('isi_keranjang_json').value = JSON.stringify(keranjang);
            document.getElementById('total_bayar_final').value = totalBelanja;

            hitungKembalian(); // Cek lagi kembalian kalau total berubah
        }

        // 4. Hapus Item
        function hapusItem(index) {
            keranjang.splice(index, 1);
            renderKeranjang();
        }

        // 5. Hitung Kembalian Realtime
        const inputTunai = document.getElementById('uang_tunai');
        inputTunai.addEventListener('keyup', hitungKembalian);

        function hitungKembalian() {
            let tunai = parseInt(inputTunai.value) || 0;
            let kembalian = tunai - totalBelanja;
            
            // Validasi Tombol Bayar
            let btnBayar = document.getElementById('btn_bayar');
            
            if (keranjang.length > 0 && tunai >= totalBelanja) {
                btnBayar.disabled = false;
                document.getElementById('kembalian_final').value = kembalian;
            } else {
                btnBayar.disabled = true;
                document.getElementById('kembalian_final').value = 0;
            }
        }

        // 6. SweetAlert Notifikasi Sukses (Dari Session PHP)
        <?php if (isset($_SESSION['alert'])): ?>
            Swal.fire({
                icon: '<?= $_SESSION['alert'][0] ?>',
                title: '<?= $_SESSION['alert'][1] ?>',
                text: '<?= $_SESSION['alert'][2] ?>'
            });
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>
    </script>
</body>
</html>