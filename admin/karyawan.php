<?php
require '../config/db.php';
cek_akses('admin'); // Halaman ini HANYA untuk Admin

// --- LOGIKA PHP (CRUD USER) ---

// 1. Tambah Karyawan Baru
if (isset($_POST['simpan'])) {
    $nama = clean_data($_POST['nama_lengkap']);
    $user = clean_data($_POST['username']);
    $role = clean_data($_POST['role']);
    $pass = $_POST['password']; // Jangan di-clean dulu karena password bisa karakter aneh

    // Cek apakah username sudah ada?
    $cek = mysqli_query($conn, "SELECT username FROM users WHERE username = '$user'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['alert'] = ['error', 'Gagal', 'Username sudah digunakan!'];
    } else {
        // Hash Password
        $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
        
        $q = mysqli_query($conn, "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$user', '$pass_hash', '$nama', '$role')");
        
        if ($q) $_SESSION['alert'] = ['success', 'Berhasil', 'Karyawan baru ditambahkan!'];
        else $_SESSION['alert'] = ['error', 'Gagal', 'Terjadi kesalahan database'];
    }
    header("Location: karyawan.php"); exit();
}

// 2. Edit Karyawan
if (isset($_POST['update'])) {
    $id   = clean_data($_POST['id_user']);
    $nama = clean_data($_POST['nama_lengkap']);
    $user = clean_data($_POST['username']);
    $role = clean_data($_POST['role']);
    $pass = $_POST['password'];

    // Cek username unik (kecuali milik sendiri)
    $cek = mysqli_query($conn, "SELECT username FROM users WHERE username = '$user' AND id_user != '$id'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['alert'] = ['error', 'Gagal', 'Username sudah dipakai orang lain!'];
    } else {
        // Logika Update Password
        if (!empty($pass)) {
            // Jika password diisi, update password baru
            $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
            $query = "UPDATE users SET nama_lengkap='$nama', username='$user', role='$role', password='$pass_hash' WHERE id_user='$id'";
        } else {
            // Jika password kosong, jangan ubah password
            $query = "UPDATE users SET nama_lengkap='$nama', username='$user', role='$role' WHERE id_user='$id'";
        }

        if (mysqli_query($conn, $query)) {
            $_SESSION['alert'] = ['success', 'Berhasil', 'Data karyawan diperbarui!'];
        } else {
            $_SESSION['alert'] = ['error', 'Gagal', 'Gagal update database'];
        }
    }
    header("Location: karyawan.php"); exit();
}

// 3. Hapus Karyawan
if (isset($_GET['hapus'])) {
    $id = clean_data($_GET['hapus']);

    // Proteksi: Jangan hapus diri sendiri
    if ($id == $_SESSION['id_user']) {
        $_SESSION['alert'] = ['error', 'Ditolak', 'Anda tidak bisa menghapus akun sendiri!'];
    } else {
        $q = mysqli_query($conn, "DELETE FROM users WHERE id_user='$id'");
        if ($q) $_SESSION['alert'] = ['success', 'Berhasil', 'User dihapus!'];
        else $_SESSION['alert'] = ['error', 'Gagal', 'Gagal menghapus user (Mungkin sudah ada transaksi)'];
    }
    header("Location: karyawan.php"); exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background-color: #198754; }
        .sidebar-link { text-decoration: none; color: #333; padding: 10px; display: block; border-radius: 5px; }
        .sidebar-link:hover, .sidebar-link.active { background-color: #e9f7ef; color: #198754; font-weight: bold; }
        .btn-green { background-color: #198754; color: white; }
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
                        <a href="obat.php" class="sidebar-link"><i class="fas fa-pills me-2"></i> Data Obat</a>
                        <a href="karyawan.php" class="sidebar-link active"><i class="fas fa-users me-2"></i> Data Karyawan</a>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-success">Daftar Karyawan (User)</h5>
                        <button type="button" class="btn btn-green btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fas fa-user-plus"></i> Tambah User
                        </button>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Lengkap</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $data_user = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, nama_lengkap ASC");
                                while ($row = mysqli_fetch_assoc($data_user)) :
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nama_lengkap'] ?></td>
                                    <td><?= $row['username'] ?></td>
                                    <td>
                                        <?php if($row['role'] == 'admin'): ?>
                                            <span class="badge bg-primary">ADMIN</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">KASIR</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm text-white btn-edit" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEdit"
                                            data-id="<?= $row['id_user'] ?>"
                                            data-nama="<?= $row['nama_lengkap'] ?>"
                                            data-user="<?= $row['username'] ?>"
                                            data-role="<?= $row['role'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <?php if($row['id_user'] != $_SESSION['id_user']): ?>
                                            <a href="#" class="btn btn-danger btn-sm btn-hapus" data-id="<?= $row['id_user'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled><i class="fas fa-trash"></i></button>
                                        <?php endif; ?>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <select name="role" class="form-select">
                                <option value="kasir">Kasir</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_user" id="edit_id">
                        
                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" id="edit_user" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                            <small class="text-muted">*Isi hanya jika ingin mengganti password</small>
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <select name="role" id="edit_role" class="form-select">
                                <option value="kasir">Kasir</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update" class="btn btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Script isi Modal Edit
        const modalEdit = document.getElementById('modalEdit');
        modalEdit.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_nama').value = button.getAttribute('data-nama');
            document.getElementById('edit_user').value = button.getAttribute('data-user');
            document.getElementById('edit_role').value = button.getAttribute('data-role');
        });

        // Script Hapus dengan SweetAlert
        document.querySelectorAll('.btn-hapus').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const url = `karyawan.php?hapus=${id}`;

                Swal.fire({
                    title: 'Hapus User ini?',
                    text: "User tidak akan bisa login lagi!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });

        <?php if (isset($_SESSION['alert'])): ?>
            Swal.fire({
                icon: '<?= $_SESSION['alert'][0] ?>',
                title: '<?= $_SESSION['alert'][1] ?>',
                text: '<?= $_SESSION['alert'][2] ?>',
                showConfirmButton: false,
                timer: 2000
            });
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>
    </script>
</body>
</html>