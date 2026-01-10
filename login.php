<?php
require 'config/db.php';

// Logika Login
if (isset($_POST['login'])) {
    $username = clean_data($_POST['username']);
    $password = clean_data($_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($query);
        // Verifikasi Password Hash
        if (password_verify($password, $data['password'])) {
            $_SESSION['status'] = 'login';
            $_SESSION['role'] = $data['role'];
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['nama'] = $data['nama_lengkap'];

            // Redirect sesuai role
            if ($data['role'] == 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: kasir/index.php");
            }
            exit();
        }
    }
    
    // Jika gagal
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Apotik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #e9f7ef; /* Hijau Muda Lembut */ height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-login { max-width: 400px; width: 100%; border-top: 5px solid #198754; /* Hijau Bootstrap */ border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-success { background-color: #198754; border: none; }
        .btn-success:hover { background-color: #146c43; }
    </style>
</head>
<body>

    <div class="card card-login bg-white p-4">
        <div class="text-center mb-4">
            <i class="fas fa-clinic-medical fa-3x text-success"></i>
            <h3 class="mt-2 fw-bold text-success">Apotik Sehat</h3>
            <p class="text-muted">Silakan login untuk masuk</p>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required placeholder="Masukan username">
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Masukan password">
            </div>
            <button type="submit" name="login" class="btn btn-success w-100 py-2">Masuk Sekarang</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if (isset($error)) : ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Login',
            text: 'Username atau Password salah!',
            confirmButtonColor: '#198754'
        });
    </script>
    <?php endif; ?>

</body>
</html>