<?php
include 'config.php';

// Kasir only

// Proses tambah ke keranjang
if (isset($_POST['tambah_obat'])) {
    $obat_id = intval($_POST['obat_id']);
    $qty = intval($_POST['qty']);
    
    $obat = $pdo->prepare("SELECT * FROM obat WHERE id = ? AND stok >= ?")->execute([$obat_id, $qty]);
    $obat = $pdo->prepare("SELECT * FROM obat WHERE id = ? AND stok >= ?")->fetch();
    
    if ($obat && $qty > 0) {
        if (!isset($keranjang[$obat_id])) {
            $keranjang[$obat_id] = ['data' => $obat, 'qty' => 0];
        }
        $keranjang[$obat_id]['qty'] += $qty;
        $_SESSION['keranjang'] = $keranjang;
    }
}

// Proses hapus item
if (isset($_POST['hapus_item'])) {
    $obat_id = intval($_POST['obat_id']);
    unset($keranjang[$obat_id]);
    $_SESSION['keranjang'] = $keranjang;
}

// Proses checkout
if (isset($_POST['checkout'])) {
    $metode = $_POST['metode_pembayaran'];
    $total = 0;
    
    $pdo->beginTransaction();
    try {
        // Buat transaksi header
        $kode_transaksi = 'TRX-' . date('YmdHis');
        $stmt = $pdo->prepare("INSERT INTO transaksi (user_id, kode_transaksi, total_item, total_harga, metode_pembayaran) VALUES (?, ?, ?, ?, ?)");
        $total_item = array_sum(array_column($keranjang, 'qty'));
        $stmt->execute([$_SESSION['user_id'], $kode_transaksi, $total_item, 0, $metode]);
        $transaksi_id = $pdo->lastInsertId();
        
        // Detail transaksi & update stok
        foreach ($keranjang as $id => $item) {
            $subtotal = $item['data']['harga_jual'] * $item['qty'];
            $total += $subtotal;
            
            $stmt = $pdo->prepare("INSERT INTO transaksi_detail (transaksi_id, obat_id, qty, harga_jual, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$transaksi_id, $id, $item['qty'], $item['data']['harga_jual'], $subtotal]);
            
            // Update stok
            $pdo->prepare("UPDATE obat SET stok = stok - ? WHERE id = ?")->execute([$item['qty'], $id]);
        }
        
        // Update total transaksi
        $pdo->prepare("UPDATE transaksi SET total_harga = ? WHERE id = ?")->execute([$total, $transaksi_id]);
        
        $pdo->commit();
        unset($_SESSION['keranjang']);
        $struk = $kode_transaksi;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Transaksi gagal: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sage-green: #9CAF88;
            --sage-green-dark: #78866B;
        }
        body { background: linear-gradient(135deg, #F8FAF5 0%, #F0F4ED 100%); font-family: 'Segoe UI', sans-serif; }
        .navbar { background: linear-gradient(135deg, var(--sage-green), var(--sage-green-dark)); }
        .main-container { background: white; border-radius: 20px; box-shadow: 0 20px 40px rgba(120, 134, 107, 0.15); overflow: hidden; }
        .cart-item { border-bottom: 1px solid #E8ECE4; padding: 1rem 0; }
        .btn-kasir { border-radius: 12px; font-weight: 600; padding: 0.75rem 1.5rem; }
        .total-display { font-size: 2rem; font-weight: 700; color: var(--sage-green-dark); }
        @media (max-width: 768px) { .total-display { font-size: 1.5rem; } }
    </style>
</head>
<body class="py-4">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark container-fluid mx-4 mb-4 rounded-3 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold fs-4">
                <i class="bi bi-capsule-pill me-2"></i>Apotek.id
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3 text-white-50">
                    <i class="bi bi-person-circle me-1"></i><?= $kasir_name ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="main-container">
            <div class="row g-0">
                <!-- Kolom Cari Obat (Kiri) -->
                <div class="col-lg-8 p-4 border-end">
                    <h3 class="fw-bold text-success mb-4">
                        <i class="bi bi-search me-2"></i>Cari Obat
                    </h3>
                    
                    <form method="POST" class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-success">
                                <i class="bi bi-search text-success"></i>
                            </span>
                            <input type="text" class="form-control border-success fs-6" 
                                   id="cariObat" placeholder="Ketik nama obat... (Paracetamol, Amoxicillin)" autocomplete="off">
                            <button class="btn btn-success" type="submit" name="cari">
                                <i class="bi bi-arrow-return-right"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Hasil Pencarian -->
                    <div id="hasilObat" class="list-group list-group-flush border rounded-3" style="max-height: 500px; overflow-y: auto;">
                        <?php
                        $stmt = $pdo->query("SELECT * FROM obat WHERE stok > 0 ORDER BY nama_obat LIMIT 10");
                        while ($obat = $stmt->fetch()):
                        ?>
                        <div class="list-group-item list-group-item-action p-3 cari-item" onclick="pilihObat(<?= $obat['id'] ?>, '<?= htmlspecialchars($obat['nama_obat']) ?>', <?= $obat['harga_jual'] ?>, <?= $obat['stok'] ?>)">
                            <div class="d-flex w-100 justify-content-between">
                                <div>
                                    <h6 class="mb-1 fw-semibold"><?= htmlspecialchars($obat['nama_obat']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($obat['kode_obat']) ?> | <?= $obat['kategori'] ?></small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success fs-6">Rp <?= number_format($obat['harga_jual'], 0, ',', '.') ?></div>
                                    <small class="text-muted">Stok: <?= $obat['stok'] ?> <?= $obat['satuan'] ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Kolom Keranjang & Total (Kanan) -->
                <div class="col-lg-4 p-4 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold text-success mb-0">
                            <i class="bi bi-cart4 me-2"></i>Keranjang
                            <span class="badge bg-success ms-2"><?= count($keranjang) ?></span>
                        </h3>
                        <?php if (!empty($keranjang)): ?>
                        <button class="btn btn-outline-danger btn-sm" onclick="kosongkanKeranjang()">
                            <i class="bi bi-trash"></i> Kosongkan
                        </button>
                        <?php endif; ?>
                    </div>

                    <!-- Items Keranjang -->
                    <div id="keranjangItems" style="max-height: 400px; overflow-y: auto;">
                        <?php if (empty($keranjang)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-cart-x fs-1 d-block mb-3 opacity-50"></i>
                                <p class="mb-0">Keranjang kosong</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($keranjang as $id => $item): 
                                $subtotal = $item['data']['harga_jual'] * $item['qty'];
                            ?>
                            <div class="cart-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1 me-3">
                                        <h6 class="fw-semibold mb-1"><?= htmlspecialchars($item['data']['nama_obat']) ?></h6>
                                        <small class="text-muted"><?= $item['data']['kode_obat'] ?></small>
                                    </div>
                                    <div class="text-end ms-2">
                                        <div class="fw-bold text-success fs-6 mb-1">Rp <?= number_format($subtotal, 0, ',', '.') ?></div>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-secondary" onclick="ubahQty(<?= $id ?>, -1)">-</button>
                                            <span class="btn btn-light px-3"><?= $item['qty'] ?></span>
                                            <button class="btn btn-outline-secondary" onclick="ubahQty(<?= $id ?>, 1)">+</button>
                                            <form method="POST" style="display: inline;" class="ms-1">
                                                <input type="hidden" name="obat_id" value="<?= $id ?>">
                                                <button type="submit" name="hapus_item" class="btn btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Total & Bayar -->
                    <div class="mt-4 pt-4 border-top">
                        <?php 
                        $grand_total = 0;
                        foreach ($keranjang as $item) {
                            $grand_total += $item['data']['harga_jual'] * $item['qty'];
                        }
                        ?>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-5 fw-bold text-muted">Grand Total:</span>
                            <div class="total-display">Rp <?= number_format($grand_total, 0, ',', '.') ?></div>
                        </div>

                        <?php if (!empty($keranjang)): ?>
                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">Metode Pembayaran</label>
                                <select class="form-select" name="metode_pembayaran" required>
                                    <option value="Tunai">Tunai</option>
                                    <option value="Transfer">Transfer Bank</option>
                                    <option value="Kartu">Kartu Debit/Kredit</option>
                                    <option value="QRIS">QRIS</option>
                                </select>
                            </div>
                            <button type="submit" name="checkout" class="btn btn-success w-100 btn-lg btn-kasir mb-3">
                                <i class="bi bi-credit-card me-2"></i>LUNAS Rp <?= number_format($grand_total, 0, ',', '.') ?>
                            </button>
                        </form>
                        <?php endif; ?>

                        <?php if (isset($struk)): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Transaksi sukses! Kode: <strong><?= $struk ?></strong>
                            <a href="#" class="btn btn-sm btn-outline-success ms-2" onclick="cetakStruk('<?= $struk ?>')">
                                <i class="bi bi-printer"></i> Cetak Struk
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let timeout;
        document.getElementById('cariObat').addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(cariObatLive, 300);
        });

        function cariObatLive() {
            const query = this.value;
            if (query.length > 2) {
                fetch(`cari_obat.php?q=${encodeURIComponent(query)}`)
                    .then(r => r.text())
                    .then(html => document.getElementById('hasilObat').innerHTML = html);
            }
        }

        function pilihObat(id, nama, harga, stok) {
            const qty = prompt(`Pilih jumlah ${nama} (stok: ${stok}):`, 1);
            if (qty && !isNaN(qty) && qty > 0 && qty <= stok) {
                document.querySelector('input[name="obat_id"]').value = id;
                document.querySelector('input[name="qty"]').value = qty;
                document.querySelector('form').submit();
            }
        }

        function ubahQty(id, delta) {
            fetch(`update_keranjang.php?id=${id}&delta=${delta}`, {method: 'POST'})
                .then(() => location.reload());
        }

        function kosongkanKeranjang() {
            if (confirm('Kosongkan keranjang?')) {
                fetch('kosongkan_keranjang.php', {method: 'POST'}).then(() => location.reload());
            }
        }

        function cetakStruk(kode) {
            window.open(`struk.php?kode=${kode}`, '_blank');
        }
    </script>
</body>
</html>
