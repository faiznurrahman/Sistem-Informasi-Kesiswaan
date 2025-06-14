<?php
global $conn;
$id_guru = $_SESSION['id_pengguna'] ?? null; // Get logged-in teacher's ID from session

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
    // Use JavaScript for redirect
    echo "<script>window.location.href = '/index.php?page=kelas';</script>";
    exit();
}

// Other actions
if ($action === 'tambah') {
    include 'tambah.php';
} elseif ($action === 'edit') {
    include 'edit.php';
} elseif ($action === 'lihat') {
    include 'view.php';
} else {
    // Query to get classes where the logged-in teacher teaches
    $query = mysqli_query($conn, "
        SELECT DISTINCT k.id_kelas AS id, k.nama_kelas, p.nama AS nama_walikelas
        FROM kelas k
        LEFT JOIN pengguna p ON k.id_walikelas = p.id_pengguna
        INNER JOIN jadwal j ON k.id_kelas = j.id_kelas
        INNER JOIN mapel_guru mg ON j.id_mapel = mg.id_mapel AND j.id_guru = mg.id_guru
        WHERE j.id_guru = '$id_guru'
    ");

    $rows = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $rows[] = $row;
    }

    $page = 'nilai';
    $columns = [
        'nama_kelas' => 'Nama Kelas',
    ];

    $actions = [
        'view' => true,
        'edit' => false,
        'delete' => false,
    ];
    $showAddButton = false;
    require_once dirname(__DIR__, 3) . '/templates/alert.php';
    require_once dirname(__DIR__, 3) . '/templates/table-template.php';
}
?>