<?php
global $conn;
require_once __DIR__ . '/../../includes/db.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// Hapus data siswa
if ($action === 'delete' && $id) {
    // Validasi relasi: cek apakah data siswa dipakai di tabel lain
    $relasi_cek = [
        'nilai_siswa' => mysqli_query($conn, "SELECT COUNT(*) AS total FROM nilai_siswa WHERE id_siswa = '$id'")
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
            'message' => 'Gagal menghapus data. Siswa masih terhubung dengan data lain seperti nilai.'
        ];
    } else {
        // Hapus foto jika ada
        $get_foto = mysqli_query($conn, "SELECT foto FROM siswa WHERE id_siswa = '$id'");
        $foto_data = mysqli_fetch_assoc($get_foto);
        if (!empty($foto_data['foto'])) {
            $foto_path = __DIR__ . '/../../Uploads/' . $foto_data['foto'];
            if (file_exists($foto_path)) {
                unlink($foto_path); // hapus file foto
            }
        }

        // Hapus hanya dari tabel siswa
        $stmt = $conn->prepare("DELETE FROM siswa WHERE id_siswa = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Data siswa berhasil dihapus.'
            ];
        } else {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Gagal menghapus data siswa. Silakan coba lagi.'
            ];
        }
        $stmt->close();
    }

    echo "<script>window.location.href = '/index.php?page=siswa';</script>";
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
    // Tampilkan daftar siswa
    $query = mysqli_query($conn, "
        SELECT 
            s.id_siswa AS id, 
            s.nis,
            s.nama_siswa AS nama, 
            s.foto,
            s.jenis_kelamin, 
            s.tanggal_lahir, 
            s.alamat, 
            k.nama_kelas
        FROM siswa s 
        LEFT JOIN kelas k ON k.id_kelas = s.id_kelas
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

    $page = 'siswa';
    $columns = [
        'nis' => 'NIS',
        'nama' => 'Nama',
        'foto' => 'Foto',
        'nama_kelas' => 'Kelas'
    ];

    $actions = [
        'view' => false,
        'edit' => true,
        'delete' => true,
    ];

    include __DIR__ . '/../../templates/alert.php';
    include __DIR__ . '/../../templates/table-template.php';
}
?>