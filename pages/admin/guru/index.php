<?php
global $conn;
require_once dirname(__DIR__, 3) . '/includes/db.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

if ($action === 'delete' && $id) {
    // Mulai transaksi untuk memastikan integritas data
    $conn->begin_transaction();
    try {
        // Hapus foto jika ada
        $get_foto = mysqli_query($conn, "SELECT foto FROM guru WHERE id_guru = '$id'");
        $foto_data = mysqli_fetch_assoc($get_foto);
        if (!empty($foto_data['foto'])) {
            $foto_path = dirname(__DIR__, 3) . '/uploads/' . $foto_data['foto'];
            if (file_exists($foto_path)) {
                unlink($foto_path); // Hapus file foto
            }
        }

        // Hapus data terkait dari tabel-tabel yang berelasi
        $stmt = $conn->prepare("DELETE FROM kelas WHERE id_walikelas = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM mapel_guru WHERE id_guru = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM nilai_siswa WHERE id_guru = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM jadwal WHERE id_guru = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Hapus data dari tabel guru
        $stmt = $conn->prepare("DELETE FROM guru WHERE id_guru = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Data guru dan data terkait berhasil dihapus.'
            ];
        } else {
            throw new Exception('Gagal menghapus data guru.');
        }
        $stmt->close();

        // Commit transaksi jika semua berhasil
        $conn->commit();
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        $conn->rollback();
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Gagal menghapus data: ' . $e->getMessage()
        ];
    }

    echo "<script>window.location.href = '/index.php?page=guru';</script>";
    exit;
}

// Tambah/Edit/Lihat
if ($action === 'tambah') {
    include 'tambah.php';
} elseif ($action === 'edit') {
    include 'edit.php';
} elseif ($action === 'lihat') {
    include 'view.php';
} else {
    // Tampilkan daftar guru
    $query = mysqli_query($conn, "
        SELECT 
            g.id_guru AS id, 
            p.nip,
            p.nama AS nama_pengguna, 
            g.foto,
            g.jenis_kelamin, 
            g.tempat_tanggal_lahir, 
            g.alamat, 
            g.no_hp, 
            g.email 
        FROM guru g 
        JOIN pengguna p ON p.id_pengguna = g.id_guru
    ");

    $rows = [];
    $no = 1;
    while ($row = mysqli_fetch_assoc($query)) {
        $foto_nama_file = $row['foto'];
        $foto_url = 'http://kesiswaan.test/uploads/' . $foto_nama_file;
        $foto_path =  dirname(__DIR__, 3) . '/uploads/' . $foto_nama_file;

        if (is_file($foto_path)) {
            $row['foto'] = '<img src="' . $foto_url . '" alt="Foto" class="w-16 h-16 object-cover rounded-lg">';
        } else {
            $row['foto'] = '<span class="text-red-500">Foto tidak ditemukan</span>';
        }

        $row['no'] = $no++;
        $rows[] = $row;
    }

    $page = 'guru';
    $columns = [
        'nip' => 'NIP',
        'nama_pengguna' => 'Nama',
        'foto' => 'Foto',
    ];

    $actions = [
        'view' => true,
        'edit' => true,
        'delete' => true,
    ];

    require_once dirname(__DIR__, 3) . '/templates/alert.php';
    require_once dirname(__DIR__, 3) . '/templates/table-template.php';
}
?>