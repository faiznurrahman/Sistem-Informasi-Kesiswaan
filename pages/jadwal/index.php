<?php
global $conn;
require_once __DIR__ . '/../../includes/db.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// Hapus data jadwal (if delete action is enabled in the future)
if ($action === 'delete' && $id) {
    // Validasi relasi: cek apakah kelas dipakai di tabel jadwal
    $relasi_cek = [
        'jadwal' => mysqli_query($conn, "SELECT COUNT(*) AS total FROM jadwal WHERE id_kelas = '$id'")
    ];

    $terhubung = false;
    foreach ($relasi_cek as $tabel => $query_result) {
        $jumlah = mysqli_fetch_assoc($query_result)['total'] ?? 0;
        if ($jumlah > 0) {
            $terhubung = true;
            break;
        }
    }

    if ($terhubung) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Gagal menghapus kelas. Kelas masih memiliki jadwal terkait.'
        ];
    } else {
        $stmt = $conn->prepare("DELETE FROM kelas WHERE id_kelas = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Data kelas berhasil dihapus.'
            ];
        } else {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Gagal menghapus data kelas: ' . $stmt->error
            ];
        }
        $stmt->close();
    }

    echo "<script>window.location.href = '/index.php?page=jadwal';</script>";
    exit;
}

// Tambah/Edit/Lihat
if ($action === 'tambah') {
    include 'jadwal_kelas.php'; // For adding schedules (if enabled)
} elseif ($action === 'edit') {
    include 'edit_kelas.php'; // Assumes edit_kelas.php for editing class (if enabled)
} elseif ($action === 'lihat') {
    include 'view.php'; // View schedules for the class
} else {
    // Fetch list of classes
    $query = mysqli_query($conn, "
        SELECT k.id_kelas AS id, k.nama_kelas, p.nama AS nama_walikelas
        FROM kelas k
        LEFT JOIN pengguna p ON k.id_walikelas = p.id_pengguna
        ORDER BY k.nama_kelas ASC
    ");

    $rows = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $rows[] = $row;
    }

    $page = 'jadwal';
    $columns = [
        'nama_kelas' => 'Nama Kelas',
        'nama_walikelas' => 'Wali Kelas',
    ];

    $actions = [
        'view' => true,
        'edit' => false,
        'delete' => false,
    ];

    include __DIR__ . '/../../templates/alert.php';
    include __DIR__ . '/../../templates/table-template.php';
}
?>