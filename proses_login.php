<?php
session_start();
require_once 'config.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Ambil data user berdasarkan username
$stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
$stmt->execute([$username]);
$users = $stmt->fetch();

// PENTING: Pilih salah satu metode verifikasi di bawah (A atau B)

// OPSI A: Gunakan ini jika password di database SUDAH di-hash (Enkripsi)
// $verifikasi_password = ($users && password_verify($password, $users['password']));

// OPSI B: Gunakan ini jika password di database masih POLOS (Plain Text/Manual ketik)
$verifikasi_password = ($users && $password == $users['password']);


if ($verifikasi_password) {
    // Set Session
    $_SESSION['id'] = $users['id'];
    $_SESSION['username'] = $users['username'];
    $_SESSION['role'] = $users['role'];

    // Cek Role untuk Redirect yang benar
    if ($users['role'] == 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: kasir.php");
    }
    exit;

} else {
    // Login Gagal
    header("Location: login.php?error=login_gagal");
    exit;
}
?>