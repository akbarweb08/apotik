<?php
require '../config/db.php';
cek_akses("admin");

// --- LOGIKA PHP (CRUD) DI SINI ---

// 1. Tambah Obat
if (isset($_POST['simpan'])) {
    $kode = clean_data($_POST['kode_obat']);
    $nama = clean_data($_POST['nama_obat']);
    $produsen = clean_data($_POST['produsen']);
    $kategori = clean_data($_POST['kategori']);
    $satuan = clean_data($_POST['satuan']);
    $stok = clean_data($_POST['stok']);
    $beli = clean_data($_POST['harga_beli']);
    $jual = clean_data($_POST['harga_jual']);
    $exp = clean_data($_POST['tanggal_kadaluarsa']);

    $q = mysqli_query($conn, "INSERT INTO obat (kode_obat, nama_obat, produsen, kategori, satuan, stok, harga_beli, harga_jual, tanggal_kadaluarsa) VALUES ('$kode', '$nama', '$produsen', '$kategori', '$satuan', '$stok', '$beli', '$jual', '$exp')");

    if ($q) $_SESSION['alert'] = ['success', 'Berhasil', 'Data obat berhasil ditambahkan!'];
    else $_SESSION['alert'] = ['error', 'Gagal', 'Terjadi kesalahan database'];
    
    header("Location: obat.php"); exit();
}

// 2. Edit Obat
if (isset($_POST['update'])) {
    $id = clean_data($_POST['id_obat']);
    $nama = clean_data($_POST['nama_obat']);
    $produsen = clean_data($_POST['produsen']);
    $kategori = clean_data($_POST['kategori']);
    $satuan = clean_data($_POST['satuan']);
    $stok = clean_data($_POST['stok']);
    $beli = clean_data($_POST['harga_beli']);
    $jual = clean_data($_POST['harga_jual']);
    $exp = clean_data($_POST['tanggal_kadaluarsa']);

    $q = mysqli_query($conn, "UPDATE obat SET nama_obat='$nama', produsen='$produsen', kategori='$kategori', satuan='$satuan', stok='$stok', harga_beli='$beli', harga_jual='$jual', tanggal_kadaluarsa='$exp' WHERE id_obat='$id'");

    if ($q) $_SESSION['alert'] = ['success', 'Berhasil', 'Data obat berhasil diperbarui!'];
    else $_SESSION['alert'] = ['error', 'Gagal', 'Gagal update data'];

    header("Location: obat.php"); exit();
}

// 3. Hapus Obat
if (isset($_GET['hapus'])) {
    $id = clean_data($_GET['hapus']);
    $q = mysqli_query($conn, "DELETE FROM obat WHERE id_obat='$id'");
    
    if ($q) $_SESSION['alert'] = ['success', 'Berhasil', 'Data obat dihapus!'];
    else $_SESSION['alert'] = ['error', 'Gagal', 'Gagal menghapus data'];

    header("Location: obat.php"); exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Obat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Gaya Hijau Custom */
        .navbar { background-color: #198754; }
        .sidebar-link { text-decoration: none; color: #333; padding: 10px; display: block; border-radius: 5px; }
        .sidebar-link:hover, .sidebar-link.active { background-color: #e9f7ef; color: #198754; font-weight: bold; }
        .btn-green { background-color: #198754; color: white; }
        .btn-green:hover { background-color: #157347; color: white; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-clinic-medical me-2"></i>APOTIK SEHAT</a>
            <div class="d-flex">
                <a href="../logout.php" class="btn btn-outline-light btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-2">
                        <a href="index.php" class="sidebar-link"><i class="fas fa-home me-2"></i> Dashboard</a>
                        <a href="obat.php" class="sidebar-link active"><i class="fas fa-pills me-2"></i> Data Obat</a>
                        <a href="karyawan.php" class="sidebar-link"><i class="fas fa-users me-2"></i> Data Karyawan</a>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-success">Daftar Obat</h5>
<div>
    <a href="export_obat.php" class="btn btn-success btn-sm me-1">
        <i class="fas fa-file-excel"></i> Export Excel
    </a>
    
    <button type="button" class="btn btn-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalImport">
        <i class="fas fa-file-upload"></i> Import CSV
    </button>

    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fas fa-plus"></i> Tambah
    </button>
</div>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-success">
                                tr>
                                    <th>Kode</th>
                                    <th>Nama Obat</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Harga Jual</th>
                                    <th>Exp</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $data_obat = mysqli_query($conn, "SELECT * FROM obat ORDER BY id_obat DESC");
                                while ($row = mysqli_fetch_assoc($data_obat)) :
                                ?>
                                <tr>
                                    <td><?= $row['kode_obat'] ?></td>
                                    <td>
                                        <strong><?= $row['nama_obat'] ?></strong><br>
                                        <small class="text-muted"><?= $row['produsen'] ?></small>
                                    </td>
                                    <td><?= $row['kategori'] ?></td>
                                    <td>
                                        <span class="badge <?= $row['stok'] < 10 ? 'bg-danger' : 'bg-success' ?>">
                                            <?= $row['stok'] ?> <?= $row['satuan'] ?>
                                        </span>
                                    </td>
                                    <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                                    <td><?= $row['tanggal_kadaluarsa'] ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm text-white btn-edit" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEdit"
                                            data-id="<?= $row['id_obat'] ?>"
                                            data-nama="<?= $row['nama_obat'] ?>"
                                            data-produsen="<?= $row['produsen'] ?>"
                                            data-kategori="<?= $row['kategori'] ?>"
                                            data-satuan="<?= $row['satuan'] ?>"
                                            data-stok="<?= $row['stok'] ?>"
                                            data-beli="<?= $row['harga_beli'] ?>"
                                            data-jual="<?= $row['harga_jual'] ?>"
                                            data-exp="<?= $row['tanggal_kadaluarsa'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="#" class="btn btn-danger btn-sm btn-hapus" data-id="<?= $row['id_obat'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Tambah Obat Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Kode Obat</label>
                                <input type="text" name="kode_obat" class="form-control" required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label>Nama Obat</label>
                                <input type="text" name="nama_obat" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Produsen</label>
                                <input type="text" name="produsen" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Kategori</label>
                                <select name="kategori" class="form-select">
                                    <option>Tablet</option>
                                    <option>Sirup</option>
                                    <option>Salep</option>
                                    <option>Alat Kesehatan</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Satuan</label>
                                <select name="satuan" class="form-select">
                                    <option>Strip</option>
                                    <option>Botol</option>
                                    <option>Pcs</option>
                                    <option>Box</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Stok Awal</label>
                                <input type="number" name="stok" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Tgl Kadaluarsa</label>
                                <input type="date" name="tanggal_kadaluarsa" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Harga Beli</label>
                                <input type="number" name="harga_beli" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Harga Jual</label>
                                <input type="number" name="harga_jual" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="simpan" class="btn btn-success">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Edit Data Obat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_obat" id="edit_id">
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>Nama Obat</label>
                                <input type="text" name="nama_obat" id="edit_nama" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Produsen</label>
                                <input type="text" name="produsen" id="edit_produsen" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Kategori</label>
                                <select name="kategori" id="edit_kategori" class="form-select">
                                    <option>Tablet</option>
                                    <option>Sirup</option>
                                    <option>Salep</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Satuan</label>
                                <select name="satuan" id="edit_satuan" class="form-select">
                                    <option>Strip</option>
                                    <option>Botol</option>
                                    <option>Pcs</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Stok</label>
                                <input type="number" name="stok" id="edit_stok" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Tgl Kadaluarsa</label>
                                <input type="date" name="tanggal_kadaluarsa" id="edit_exp" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Harga Beli</label>
                                <input type="number" name="harga_beli" id="edit_beli" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Harga Jual</label>
                                <input type="number" name="harga_jual" id="edit_jual" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update" class="btn btn-warning">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Import Data Obat (CSV)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="import_obat.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info small">
                        <strong>Format CSV:</strong><br>
                        Kode, Nama, Produsen, Kategori, Satuan, Stok, HrgBeli, HrgJual, Exp(YYYY-MM-DD)
                    </div>
                    <div class="mb-3">
                        <label>Pilih File CSV</label>
                        <input type="file" name="file_csv" class="form-control" required accept=".csv">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="import" class="btn btn-primary">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. Script untuk mengisi Modal Edit secara Dinamis
        const modalEdit = document.getElementById('modalEdit');
        modalEdit.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            // Ambil data dari atribut data- di tombol edit
            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_nama').value = button.getAttribute('data-nama');
            document.getElementById('edit_produsen').value = button.getAttribute('data-produsen');
            document.getElementById('edit_kategori').value = button.getAttribute('data-kategori'); // Perlu logika extra jika option dinamis
            document.getElementById('edit_satuan').value = button.getAttribute('data-satuan');
            document.getElementById('edit_stok').value = button.getAttribute('data-stok');
            document.getElementById('edit_beli').value = button.getAttribute('data-beli');
            document.getElementById('edit_jual').value = button.getAttribute('data-jual');
            document.getElementById('edit_exp').value = button.getAttribute('data-exp');
        });

        // 2. Script SweetAlert untuk Konfirmasi Hapus
        document.querySelectorAll('.btn-hapus').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const url = `obat.php?hapus=${id}`;

                Swal.fire({
                    title: 'Yakin hapus data?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });

        // 3. Script Menampilkan Alert dari Session PHP
        <?php if (isset($_SESSION['alert'])): ?>
            Swal.fire({
                icon: '<?= $_SESSION['alert'][0] ?>',
                title: '<?= $_SESSION['alert'][1] ?>',
                text: '<?= $_SESSION['alert'][2] ?>',
                timer: 3000,
                showConfirmButton: false
            });
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>
    </script>
</body>
</html>