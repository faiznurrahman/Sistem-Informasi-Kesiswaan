<?php
header('Content-Type: application/json');

// Include koneksi ke database langsung
require_once __DIR__ . '/../../includes/db.php'; // ← Sesuaikan path ini
global $conn;

// Get NIP dari request body (POST JSON)
$input = json_decode(file_get_contents('php://input'), true);
$nip = isset($input['nip']) ? trim($input['nip']) : '';

if (empty($nip)) {
    echo json_encode(['success' => false, 'message' => 'NIP tidak boleh kosong']);
    exit;
}

// Query ambil nama berdasarkan NIP
$nip = mysqli_real_escape_string($conn, $nip);
$query = "SELECT nama FROM pengguna WHERE nip = '$nip'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode(['success' => true, 'nama' => $row['nama']]);
} else {
    echo json_encode(['success' => false, 'message' => 'NIP tidak ditemukan']);
}
