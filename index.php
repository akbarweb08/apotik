<?php
session_start();

if (isset($_SESSION['status']) && $_SESSION['status'] == 'login') {
    // Jika sudah login, cek rolenya
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/index.php");
    } else if ($_SESSION['role'] == 'kasir') {
        header("Location: kasir/index.php");
    }
} else {
    // Jika belum login
    header("Location: login.php");
}
exit();
?>