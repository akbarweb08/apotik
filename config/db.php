
<?php
// Memulai session di awal koneksi agar tidak perlu ngetik session_start() di tiap halaman
session_start();

// Konfigurasi Database
$host = 'localhost';
$db = 'apotikk';
$user = 'root';
$pass = 'tidaktau321';
// Melakukan koneksi ke MySQL
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Jakarta');

function clean_data($data) {
    global $conn;
    return htmlspecialchars(mysqli_real_escape_string($conn, $data));
}

// --- UPDATE FITUR KEAMANAN ---

function cek_akses($role_diizinkan) {
    // 1. Cek apakah user sudah login?
    if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
        // Jika belum, lempar ke login dengan pesan error
        header("Location: ../login.php?pesan=belum_login");
        exit();
    }

    // 2. Cek apakah Role-nya sesuai?
    // Jika role user saat ini TIDAK SAMA dengan role yang diminta halaman ini
    if ($_SESSION['role'] != $role_diizinkan) {
        // Lempar balik ke habitat aslinya
        if ($_SESSION['role'] == 'admin') {
            header("Location: ../admin/index.php");
        } else {
            header("Location: ../kasir/index.php");
        }
        exit(); // Stop script agar halaman tidak sempat dimuat
    }
}
?>