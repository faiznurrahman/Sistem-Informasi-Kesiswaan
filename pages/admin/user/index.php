<?php
global $conn;

$action = $_GET['action'] ?? '';

// Handler DELETE
if ($action === 'delete') {
    $id = $_GET['id'] ?? '';
    if ($id) {
        // Cek apakah id_pengguna masih digunakan di tabel guru
        $check = $conn->prepare("SELECT COUNT(*) FROM guru WHERE id_guru = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
            $_SESSION['error'] = 'Gagal menghapus data: Pengguna masih terdaftar sebagai guru.';
        } else {
            // Jika tidak ada relasi, lanjutkan penghapusan
            $stmt = $conn->prepare("DELETE FROM pengguna WHERE id_pengguna = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Data berhasil dihapus!';
            } else {
                $_SESSION['error'] = 'Gagal menghapus data: ' . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Gunakan JavaScript untuk redirect
    echo "<script>window.location.href = '/index.php?page=user';</script>";
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
        SELECT id_pengguna AS id, nip, nama, role 
        FROM pengguna
    ");

    $rows = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $rows[] = $row;
    }

    $page = 'user';
    $columns = [
        'nip' => 'NIP',
        'nama' => 'Nama',
        'role' => 'Role',
    ];

    $actions = [
        'view' => false,
        'edit' => false,
        'delete' => true,
    ];

   require_once dirname(__DIR__, 3) . '/templates/alert.php';
    require_once dirname(__DIR__, 3) . '/templates/table-template.php';
}
