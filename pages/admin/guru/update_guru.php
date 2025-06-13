<?php
session_start();
require_once dirname(__DIR__, 3) . '/includes/db.php';

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
$id_guru = filter_input(INPUT_POST, 'id_guru', FILTER_SANITIZE_NUMBER_INT) ?? 0;
$nip = filter_input(INPUT_POST, 'nip', FILTER_SANITIZE_STRING) ?? '';
$nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING) ?? '';
$jenis_kelamin = filter_input(INPUT_POST, 'jenis_kelamin', FILTER_SANITIZE_STRING) ?? '';
$ttl = filter_input(INPUT_POST, 'tempat_tanggal_lahir', FILTER_SANITIZE_STRING) ?? '';
$alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING) ?? '';
$no_hp = filter_input(INPUT_POST, 'no_hp', FILTER_SANITIZE_STRING) ?? '';
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
$pendidikan = filter_input(INPUT_POST, 'pendidikan_terakhir', FILTER_SANITIZE_STRING) ?? '';
$prodi = filter_input(INPUT_POST, 'program_studi', FILTER_SANITIZE_STRING) ?? '';

// Validasi input
$errors = [];
if ($id_guru <= 0) {
    $errors[] = 'ID guru tidak valid!';
}
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
$upload_dir = dirname(__DIR__, 2) . '/Uploads/';
$foto = '';
$existing_foto = '';
// Ambil foto yang sudah ada
$cek_foto = $conn->prepare("SELECT foto FROM guru WHERE id_guru = ?");
$cek_foto->bind_param("i", $id_guru);
$cek_foto->execute();
$hasil_cek_foto = $cek_foto->get_result();
if ($row = $hasil_cek_foto->fetch_assoc()) {
    $existing_foto = $row['foto'];
}
$cek_foto->close();

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
            // Hapus foto lama jika ada
            if ($existing_foto && file_exists($upload_dir . $existing_foto)) {
                unlink($upload_dir . $existing_foto);
            }
        } else {
            $errors[] = 'Gagal mengunggah foto!';
        }
    }
} else {
    $foto = $existing_foto; // Gunakan foto yang sudah ada
}

// Jika ada error, simpan pesan dan redirect
if (!empty($errors)) {
    $_SESSION['error'] = implode(' ', $errors);
    echo "<script>window.location.href = '/index.php?page=guru';</script>";
    exit;
}

// Cek apakah data guru ada
$cek = $conn->prepare("SELECT id_guru FROM guru WHERE id_guru = ?");
$cek->bind_param("i", $id_guru);
$cek->execute();
$hasil_cek = $cek->get_result();

if ($hasil_cek->num_rows === 0) {
    $_SESSION['error'] = 'Data guru tidak ditemukan!';
    echo "<script>window.location.href = '/index.php?page=guru';</script>";
    exit;
}
$cek->close();

// Cek apakah NIP sudah digunakan oleh pengguna lain
$cek_nip = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE nip = ? AND id_pengguna != ?");
$cek_nip->bind_param("si", $nip, $id_guru);
$cek_nip->execute();
$hasil_cek_nip = $cek_nip->get_result();
if ($hasil_cek_nip->num_rows > 0) {
    $_SESSION['error'] = 'NIP sudah digunakan oleh pengguna lain!';
    echo "<script>window.location.href = '/index.php?page=guru';</script>";
    exit;
}
$cek_nip->close();

// Mulai transaksi
$conn->begin_transaction();
try {
    // Update pengguna table
    $sql_pengguna = "UPDATE pengguna SET nama = ?, nip = ? WHERE id_pengguna = ?";
    $stmt_pengguna = $conn->prepare($sql_pengguna);
    $stmt_pengguna->bind_param("ssi", $nama, $nip, $id_guru);
    $stmt_pengguna->execute();
    $stmt_pengguna->close();

    // Update guru table
    $sql_guru = "UPDATE guru SET jenis_kelamin = ?, tempat_tanggal_lahir = ?, alamat = ?, no_hp = ?, email = ?, pendidikan_terakhir = ?, program_studi = ?, foto = ? WHERE id_guru = ?";
    $stmt_guru = $conn->prepare($sql_guru);
    $stmt_guru->bind_param("ssssssssi", $jenis_kelamin, $ttl, $alamat, $no_hp, $email, $pendidikan, $prodi, $foto, $id_guru);
    $stmt_guru->execute();
    $stmt_guru->close();

    $conn->commit();
    $_SESSION['success'] = 'Data guru berhasil diperbarui!';
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = 'Gagal memperbarui data guru!';
}

// Redirect ke halaman guru
echo "<script>window.location.href = '/index.php?page=guru';</script>";
exit;
?>