<?php
global $conn;
require_once dirname(__DIR__, 3) . '/includes/db.php';

// Pastikan guru sudah login
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'guru') {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Anda harus login sebagai sebagai guru untuk melihat jadwal.'
    ];
    echo "<script>window.location.href = '/login.php';</script>";
    exit;
}

$id_guru = $_SESSION['id_pengguna'];

// Query untuk mengambil jadwal guru, hanya menampilkan jadwal yang sesuai dengan mapel_guru
$query = "
    SELECT 
        j.id_jadwal,
        j.hari,
        j.jam_mulai,
        j.jam_selesai,
        j.ruang,
        k.nama_kelas,
        m.nama_mapel,
        t.tahun,
        t.semester
    FROM jadwal j
    JOIN kelas k ON j.id_kelas = k.id_kelas
    JOIN mapel m ON j.id_mapel = m.id_mapel
    JOIN tahun_ajaran t ON j.id_tahun = t.id_tahun
    JOIN mapel_guru mg ON j.id_guru = mg.id_guru AND j.id_mapel = mg.id_mapel
    WHERE j.id_guru = ?
    ORDER BY 
        FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'),
        j.jam_mulai
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_guru);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
$no = 1;
while ($row = $result->fetch_assoc()) {
    $row['jam_mulai'] = date('H:i', strtotime($row['jam_mulai']));
    $row['jam_selesai'] = date('H:i', strtotime($row['jam_selesai']));
    $row['no'] = $no++;
    $rows[] = $row;
}
$stmt->close();

// Konfigurasi untuk tabel
$page = 'jadwal';
$columns = [
    'hari' => 'Hari',
    'jam_mulai' => 'Jam Mulai',
    'jam_selesai' => 'Jam Selesai',
    'ruang' => 'Ruang',
    'nama_kelas' => 'Kelas',
    'nama_mapel' => 'Mata Pelajaran',
    'tahun' => 'Tahun Ajaran',
    'semester' => 'Semester'
];

$actions = [];
$showAddButton = false;
require_once dirname(__DIR__, 3) . '/templates/alert.php';
require_once dirname(__DIR__, 3) . '/templates/table-template.php';
?>