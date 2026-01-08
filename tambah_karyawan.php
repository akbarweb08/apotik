<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $username     = $_POST['username'];
    $email        = $_POST['email'];
    $password     = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_input   = $_POST['role']; // 'admin' atau 'kasir'
    
    // Perbaikan: Ambil data dari $_POST, bukan membuat array manual
    $telepon      = $_POST['telepon'] ?? ''; 
    $alamat       = $_POST['alamat'] ?? '';
    $status       = 'aktif'; 

    try {
        $pdo->beginTransaction();

        // 1. Masukkan ke tabel users (karena kolom username & password ada di sini)
        $sqlUser = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->execute([$username, $password, strtolower($role_input)]);
        
        // Ambil ID user yang baru saja dibuat
        $newUserId = $pdo->lastInsertId();

        // 2. Masukkan ke tabel tambah_karyawan (menggunakan user_id)
        // Kolom sesuai DB: id, user_id, nama_lengkap, email, status, telepon, alamat
        $sqlKaryawan = "INSERT INTO tambah_karyawan (nama_lengkap, user_id, email, status, telepon, alamat) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmtKaryawan = $pdo->prepare($sqlKaryawan);
        
        $stmtKaryawan->execute([
            $nama_lengkap, 
            $newUserId, 
            $email, 
            $status, 
            $telepon, 
            $alamat
        ]);

        $pdo->commit();
        header("Location: kelola_karyawan.php?success=1");
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>