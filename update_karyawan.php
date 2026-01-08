<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $nama_lengkap = $_POST['nama_lengkap'];
    $username     = $_POST['username'];
    $email        = $_POST['email'];
    $telepon      = $_POST['telepon'] ?? '';
    $alamat       = $_POST['alamat'] ?? '';
    $password     = $_POST['password'];

    try {
        $pdo->beginTransaction();

        // Update tabel tambah_karyawan
        $sqlKaryawan = "UPDATE tambah_karyawan SET nama_lengkap = ?, email = ?, telepon = ?, alamat = ? WHERE id = ?";
        $stmtKaryawan = $pdo->prepare($sqlKaryawan);
        $stmtKaryawan->execute([$nama_lengkap, $email, $telepon, $alamat, $id]);

        // Update tabel users
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sqlUser = "UPDATE users SET username = ?, password = ? WHERE id = (SELECT user_id FROM tambah_karyawan WHERE id = ?)";
            $stmtUser = $pdo->prepare($sqlUser);
            $stmtUser->execute([$username, $hashed_password, $id]);
        } else {
            $sqlUser = "UPDATE users SET username = ? WHERE id = (SELECT user_id FROM tambah_karyawan WHERE id = ?)";
            $stmtUser = $pdo->prepare($sqlUser);
            $stmtUser->execute([$username, $id]);
        }

        $pdo->commit();
        header("Location: kelola_karyawan.php?success=update");
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>