<?php
session_start();
include_once '../includes/db.php';

// Cek apakah data login sudah dikirim
if (!isset($_POST['nip'], $_POST['password'], $_POST['kategori'])) {
    $_SESSION['alert'] = [
        'type' => 'warning',
        'message' => 'Harap isi semua data login terlebih dahulu.'
    ];
    header("Location: ../login.php");
    exit;
}

$nip = trim($_POST['nip']);
$password = $_POST['password'];
$role = $_POST['kategori'];

// Query untuk cek berdasarkan NIP dan role (admin/guru)
$sql = "SELECT * FROM pengguna WHERE nip = ? AND role = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Kesalahan server: ' . $conn->error
    ];
    header("Location: ../login.php");
    exit;
}
$stmt->bind_param("ss", $nip, $role);

// Eksekusi query
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verifikasi password
    if (password_verify($password, $user['password'])) {
        // Set session login
        $_SESSION['login'] = true;
        $_SESSION['id_pengguna'] = $user['id_pengguna'];
        $_SESSION['nip'] = $user['nip'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama'];

        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Login berhasil. Selamat datang, ' . htmlspecialchars($user['nama']) . '!'
        ];

        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Password salah!'
        ];
        header("Location: ../login.php");
        exit;
    }
} else {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Pengguna tidak ditemukan!'
    ];
    header("Location: ../login.php");
    exit;
}
