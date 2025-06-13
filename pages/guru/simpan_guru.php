<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

// Periksa koneksi database
if ($conn->connect_error) {
    $_SESSION['error'] = 'Koneksi database gagal. Silakan coba lagi nanti.';
    echo "<script>window.location.href = '/index.php?page=error';</script>";
    exit;
}

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Metode tidak diizinkan!';
    echo "<script>window.location.href = '/index.php?page=guru';</script>";
    exit;
}

// Ambil dan sanitasi data dari form
$nip = filter_input(INPUT_POST, 'nip', FILTER_SANITIZE_STRING) ?? '';
$nama = filter_input(INPUT_POST, 'namaLengkap', FILTER_SANITIZE_STRING) ?? '';
$jenis_kelamin = filter_input(INPUT_POST, 'jenis_kelamin', FILTER_SANITIZE_STRING) ?? '';
$ttl = filter_input(INPUT_POST, 'tempat_tanggal_lahir', FILTER_SANITIZE_STRING) ?? '';
$alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING) ?? '';
$no_hp = filter_input(INPUT_POST, 'no_hp', FILTER_SANITIZE_STRING) ?? '';
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
$pendidikan = filter_input(INPUT_POST, 'pendidikan_terakhir', FILTER_SANITIZE_STRING) ?? '';
$prodi = filter_input(INPUT_POST, 'program_studi', FILTER_SANITIZE_STRING) ?? '';

// Validasi input
$errors = [];
if (empty($nip) || !preg_match('/^[0-9]{10,18}$/', $nip)) {
    $errors[] = 'NIP tidak valid!';
}
if (empty($nama)) {
    $errors[] = 'Nama lengkap wajib diisi!';
}
if (!in_array($jenis_kelamin, ['L', 'P'])) {
    $errors[] = 'Jenis kelamin tidak valid!';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email tidak valid!';
}
if (empty($no_hp) || !preg_match('/^08[0-9]{8,12}$/', $no_hp)) {
    $errors[] = 'Nomor telepon tidak valid!';
}

// Proses upload foto
$upload_dir = dirname(__DIR__, 2) . '/uploads/';
$foto = '';
if (isset($_FILES['file-upload']) && $_FILES['file-upload']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png'];
    $max_size = 10 * 1024 * 1024; // 10MB
    $file_type = mime_content_type($_FILES['file-upload']['tmp_name']);
    $file_size = $_FILES['file-upload']['size'];

    if (!in_array($file_type, $allowed_types)) {
        $errors[] = 'Tipe file tidak diizinkan! Gunakan JPG atau PNG.';
    } elseif ($file_size > $max_size) {
        $errors[] = 'Ukuran file terlalu besar! Maksimal 10MB.';
    } else {
        $namaFile = time() . '-' . basename($_FILES['file-upload']['name']);
        $target = $upload_dir . $namaFile;
        if (move_uploaded_file($_FILES['file-upload']['tmp_name'], $target)) {
            $foto = $namaFile;
        } else {
            $errors[] = 'Gagal mengunggah foto!';
        }
    }
}

// Jika ada error, simpan pesan dan redirect
if (!empty($errors)) {
    $_SESSION['error'] = implode(' ', $errors);
    echo "<script>window.location.href = '/index.php?page=guru';</script>";
    exit;
}

// Cari id_pengguna
$query = "SELECT id_pengguna FROM pengguna WHERE nip = ? AND nama = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $nip, $nama);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $id_pengguna = $row['id_pengguna'];

    // Cek apakah data guru sudah ada
    $cek = $conn->prepare("SELECT * FROM guru WHERE id_guru = ?");
    $cek->bind_param("i", $id_pengguna);
    $cek->execute();
    $hasil_cek = $cek->get_result();

    if ($hasil_cek->num_rows === 0) {
        // Insert data ke tabel guru
        $sql = "INSERT INTO guru (
            id_guru, jenis_kelamin, tempat_tanggal_lahir, alamat, no_hp, email, pendidikan_terakhir, program_studi, foto
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssss", $id_pengguna, $jenis_kelamin, $ttl, $alamat, $no_hp, $email, $pendidikan, $prodi, $foto);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'User berhasil ditambahkan!';
        } else {
            $_SESSION['error'] = 'Gagal menyimpan data guru!';
        }
    } else {
        $_SESSION['error'] = 'Data guru sudah terdaftar!';
    }
} else {
    $_SESSION['error'] = 'Pengguna tidak ditemukan!';
}

// Redirect ke halaman guru
echo "<script>window.location.href = '/index.php?page=guru';</script>";
exit;