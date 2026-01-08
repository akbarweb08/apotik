<?php
session_start();
require_once 'config.php';

$stmt = $pdo->query("SELECT * FROM tambah_karyawan");
$users = $stmt->fetchAll(); // 🔥 INI PENTING
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sage-green: #9CAF88;
            --sage-green-dark: #78866B;
            --sage-light: #B8C4B4;
            --sage-bg: #F8FAF5;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--sage-bg) 0%, #F0F4ED 100%);
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, var(--sage-green), var(--sage-green-dark));
            box-shadow: 0 4px 20px rgba(120, 134, 107, 0.2);
        }
        .page-header {
            background: linear-gradient(135deg, white 0%, rgba(156, 175, 136, 0.05) 100%);
            border-radius: 20px;
            border: 1px solid rgba(156, 175, 136, 0.15);
            box-shadow: 0 10px 40px rgba(120, 134, 107, 0.1);
        }
        .table-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(156, 175, 136, 0.15);
            box-shadow: 0 20px 40px rgba(120, 134, 107, 0.15);
        }
        .btn-user {
            border-radius: 12px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-success { background: var(--sage-green); border-color: var(--sage-green); }
        .btn-success:hover { background: var(--sage-green-dark); transform: translateY(-1px); }
        .status-badge { font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 20px; }
        .stats-card {
            background: linear-gradient(135deg, white 0%, rgba(156, 175, 136, 0.05) 100%);
            border: 1px solid rgba(156, 175, 136, 0.15);
            border-radius: 16px;
            height: 100px;
        }
    </style>
</head>
<body class="py-4">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="admin.php">
                <i class="bi bi-capsule-pill me-2"></i>Apotek.id
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3 text-white-50">
                    <i class="bi bi-person-circle me-1"></i> Admin
                </span>
                <a href="admin.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Keluar
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <!-- Header -->
        <div class="page-header p-4 mb-5">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold text-success mb-2">
                        <i class="bi bi-people-fill me-3"></i>Kelola Karyawan
                    </h1>
                    <p class="lead text-muted">Tambah, edit, dan hapus pengguna sistem apotek</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="admin.php" class="btn btn-outline-success btn-lg me-2">
                        <i class="bi bi-arrow-left me-2"></i>Dashboard
                    </a>
                    <br>
                    <br>
                    <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#tambahUserModal  ">
                        <i class="bi bi-plus-circle me-2"></i>Tambah User
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="row mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-9 text-center">
                    <i class="bi bi-people fs-1 text-success mb-3"></i>
                    <h3 class="fs-2 fw-bold text-success"></h3>
                    <p class="text-muted mb-0">Total User</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-9 text-center">
                    <i class="bi bi-person-check fs-1 text-success mb-3"></i>
                    <h3 class="fs-2 fw-bold text-success"> </h3>
                    <p class="text-muted mb-0">Aktif</p>
                </div>
            </div>
        </div>

        <!-- Table Users -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>ID</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th>Created At</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><strong>0<?= $user['id'] ?></strong></td>
                            <td><?= htmlspecialchars($user['nama_lengkap'] ?: '-') ?></td>
                            <td>
                                <strong><?= htmlspecialchars($user['user_id']) ?></strong>
                                
                            </td>
                            <td><?= htmlspecialchars($user['email'] ?: '-') ?></td>
                            <td>
                                <span class="badge <?= $user['role']=='admin' ? 'bg-danger' : 'bg-success' ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge status-badge <?= $user['status']=='aktif' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst($user['status']) ?>
                                </span>
                            </td>
                            <td><strong>0<?= $user['telepon'] ?></strong></td>
                            <td><?= htmlspecialchars($user['alamat'] ?: '-') ?></td>
                            <td>
                                <strong><?= htmlspecialchars($user['created_at']) ?></strong>
                                
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="?status=<?= $user['id'] ?>" 
                                       class="btn btn-sm btn-outline-success" 
                                       onclick="return confirm('Ubah status <?= $user['username'] ?>?')">
                                        <i class="bi bi-power"></i>
                                    </a>
                                    
                                    <a href="?delete=<?= $user['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Hapus <?= $user['username'] ?>?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-3"></i>
                                Belum ada pengguna
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah User -->
    <div class="modal fade" id="tambahUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i>Tambah Pengguna Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="tambah_karyawan.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username *</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password *</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Role *</label>
                            <select class="form-select" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="kasir">Kasir</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Telepon *</label>
                            <input type="text" class="form-control" name="telepon" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat *</label>
                            <input type="text" class="form-control" name="alamat" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>Tambah User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
