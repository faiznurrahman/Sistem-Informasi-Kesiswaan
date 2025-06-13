<?php
global $conn;
require_once dirname(__DIR__, 3) . '/includes/db.php';

// Pastikan guru sudah login
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'guru') {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Anda harus login sebagai guru untuk melihat jadwal.'
    ];
    echo "<script>window.location.href = '/login.php';</script>";
    exit;
}

$id_guru = $_SESSION['id_pengguna'];

// Query untuk mengambil jadwal guru, diurutkan berdasarkan hari dan jam_mulai
$query = mysqli_query($conn, "
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
    WHERE j.id_guru = '$id_guru'
    ORDER BY 
        FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'),
        j.jam_mulai
");

$rows = [];
$no = 1;
while ($row = mysqli_fetch_assoc($query)) {
    // Format waktu agar lebih rapi (misalnya, 08:00)
    $row['jam_mulai'] = date('H:i', strtotime($row['jam_mulai']));
    $row['jam_selesai'] = date('H:i', strtotime($row['jam_selesai']));
    $row['no'] = $no++;
    $rows[] = $row;
}

// Konfigurasi untuk tabel
$page = 'jadwal';
$columns = [
    'no' => 'No',
    'hari' => 'Hari',
    'jam_mulai' => 'Jam Mulai',
    'jam_selesai' => 'Jam Selesai',
    'ruang' => 'Ruang',
    'nama_kelas' => 'Kelas',
    'nama_mapel' => 'Mata Pelajaran',
    'tahun' => 'Tahun Ajaran',
    'semester' => 'Semester'
];

// Tidak ada aksi (view, edit, delete)
$actions = [];

require_once dirname(__DIR__, 3) . '/templates/alert.php';
require_once dirname(__DIR__, 3) . '/templates/table-template.php';
?>