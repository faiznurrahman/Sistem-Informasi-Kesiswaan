<?php
global $conn;
require_once __DIR__ . '/../../includes/db.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// Hapus data guru
if ($action === 'delete' && $id) {
    // Validasi relasi: cek apakah data guru dipakai di tabel lain
    $relasi_cek = [
        'kelas' => mysqli_query($conn, "SELECT COUNT(*) AS total FROM kelas WHERE id_walikelas = '$id'"),
        'mapel_guru' => mysqli_query($conn, "SELECT COUNT(*) AS total FROM mapel_guru WHERE id_guru = '$id'"),
        'nilai_siswa' => mysqli_query($conn, "SELECT COUNT(*) AS total FROM nilai_siswa WHERE id_guru = '$id'")
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
            'message' => 'Gagal menghapus data. Guru masih terhubung dengan data lain seperti wali kelas, mapel, atau nilai.'
        ];
    } else {
        // Hapus foto jika ada
        $get_foto = mysqli_query($conn, "SELECT foto FROM guru WHERE id_guru = '$id'");
        $foto_data = mysqli_fetch_assoc($get_foto);
        if (!empty($foto_data['foto'])) {
            $foto_path = __DIR__ . '/../../Uploads/' . $foto_data['foto'];
            if (file_exists($foto_path)) {
                unlink($foto_path); // hapus file foto
            }
        }

        // Hapus hanya dari tabel guru
        $stmt = $conn->prepare("DELETE FROM guru WHERE id_guru = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Data guru berhasil dihapus.'
            ];
        } else {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Gagal menghapus data guru. Silakan coba lagi.'
            ];
        }
        $stmt->close();
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
        $foto_path = __DIR__ . '/../../Uploads/' . $foto_nama_file;

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

    include __DIR__ . '/../../templates/alert.php';
    include __DIR__ . '/../../templates/table-template.php';
}
?>