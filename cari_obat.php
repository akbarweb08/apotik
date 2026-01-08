<?php
include 'config.php';

$q = $_GET['q'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM tambah_obat WHERE nama_obat LIKE ? AND stok > 0 ORDER BY nama_obat LIMIT 10");
$stmt->execute(["%$q%"]);

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