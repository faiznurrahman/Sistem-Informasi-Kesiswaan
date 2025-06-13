<?php
global $conn;

$action = $_GET['action'] ?? '';

// Handler DELETE
if ($action === 'delete') {
    $id = $_GET['id'] ?? '';
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM kelas WHERE id_kelas = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Data kelas berhasil dihapus!';
        } else {
            $_SESSION['error'] = 'Gagal menghapus data kelas: ' . $stmt->error;
        }
        $stmt->close();
    }
    // Gunakan JavaScript untuk redirect
    echo "<script>window.location.href = '/index.php?page=kelas';</script>";
    exit();
}

// Aksi lainnya
if ($action === 'tambah') {
    include 'tambah.php';
} elseif ($action === 'edit') {
    include 'edit.php';
} elseif ($action === 'lihat') {
    include 'view.php';
} else {
    $query = mysqli_query($conn, "
        SELECT k.id_kelas AS id, k.nama_kelas, p.nama AS nama_walikelas
        FROM kelas k
        LEFT JOIN pengguna p ON k.id_walikelas = p.id_pengguna
    ");

    $rows = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $rows[] = $row;
    }

    $page = 'kelas';
    $columns = [
        'nama_kelas' => 'Nama Kelas',
        'nama_walikelas' => 'Wali Kelas',
    ];

    $actions = [
        'view' => true,
        'edit' => true,
        'delete' => false,
    ];
    include __DIR__ . '/../../templates/alert.php';
    include __DIR__ . '/../../templates/table-template.php';
}
?>