<?php
global $conn;

$action = $_GET['action'] ?? '';

// Handler DELETE
if ($action === 'delete') {
    $id = $_GET['id'] ?? '';
    if ($id) {
        // Mulai transaksi untuk memastikan integritas data
        $conn->begin_transaction();
        try {
            // Hapus data terkait di tabel mapel_guru
            $stmt = $conn->prepare("DELETE FROM mapel_guru WHERE id_mapel = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Hapus data terkait di tabel jadwal_mapel
            $stmt = $conn->prepare("DELETE FROM jadwal_mapel WHERE id_mapel = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Hapus data terkait di tabel nilai_siswa
            $stmt = $conn->prepare("DELETE FROM nilai_siswa WHERE id_mapel = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Hapus data dari tabel mapel
            $stmt = $conn->prepare("DELETE FROM mapel WHERE id_mapel = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Data mata pelajaran berhasil dihapus!';
            } else {
                throw new Exception('Gagal menghapus data mata pelajaran: ' . $stmt->error);
            }
            $stmt->close();

            // Commit transaksi
            $conn->commit();
        } catch (Exception $e) {
            // Rollback jika ada error
            $conn->rollback();
            $_SESSION['error'] = $e->getMessage();
        }
    }
    // Gunakan JavaScript untuk redirect
    echo "<script>window.location.href = '/index.php?page=mapel';</script>";
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
    // Query untuk mengambil data mapel dan guru dengan JOIN
    $query = mysqli_query($conn, "
        SELECT m.id_mapel AS id, m.nama_mapel, GROUP_CONCAT(p.nama) AS nama_guru
        FROM mapel m
        LEFT JOIN mapel_guru mg ON m.id_mapel = mg.id_mapel
        LEFT JOIN guru g ON mg.id_guru = g.id_guru
        LEFT JOIN pengguna p ON g.id_guru = p.id_pengguna
        GROUP BY m.id_mapel, m.nama_mapel
    ");

    $rows = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $row['nama_guru'] = $row['nama_guru'] ?? 'Belum ada guru';
        $rows[] = $row;
    }

    $page = 'mapel';
    $columns = [
        'nama_mapel' => 'Nama Mata Pelajaran',
        'nama_guru' => 'Guru Pengajar',
    ];

    $actions = [
        'view' => false,
        'edit' => true,
        'delete' => true,
    ];
    require_once dirname(__DIR__, 3) . '/templates/alert.php';
    require_once dirname(__DIR__, 3) . '/templates/table-template.php';
}
?>