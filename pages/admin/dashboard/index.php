<?php
global $conn;
require_once dirname(__DIR__, 3) . '/includes/db.php';

// Ambil total data dari masing-masing tabel
$totalSiswa = $conn->query("SELECT COUNT(*) as total FROM siswa")->fetch_assoc()['total'];
$totalGuru = $conn->query("SELECT COUNT(*) as total FROM guru")->fetch_assoc()['total'];
$totalKelas = $conn->query("SELECT COUNT(*) as total FROM kelas")->fetch_assoc()['total'];
$totalMapel = $conn->query("SELECT COUNT(*) as total FROM mapel")->fetch_assoc()['total'];
?>
<h1 class="text-2xl font-semibold mb-6">Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-green-500 text-white p-5 rounded-lg shadow flex items-center">
        <i class="ri-group-fill text-4xl mr-4"></i>
        <div>
            <p class="text-sm uppercase font-semibold">Total Siswa</p>
            <p class="text-2xl font-bold"><?= $totalSiswa ?></p>
        </div>
    </div>
    <div class="bg-blue-500 text-white p-5 rounded-lg shadow flex items-center">
        <i class="ri-user-3-fill text-4xl mr-4"></i>
        <div>
            <p class="text-sm uppercase font-semibold">Total Guru</p>
            <p class="text-2xl font-bold"><?= $totalGuru ?></p>
        </div>
    </div>
    <div class="bg-yellow-400 text-white p-5 rounded-lg shadow flex items-center">
        <i class="ri-building-2-fill text-4xl mr-4"></i>
        <div>
            <p class="text-sm uppercase font-semibold">Total Kelas</p>
            <p class="text-2xl font-bold"><?= $totalKelas ?></p>
        </div>
    </div>
    <div class="bg-purple-600 text-white p-5 rounded-lg shadow flex items-center">
        <i class="ri-book-fill text-4xl mr-4"></i>
        <div>
            <p class="text-sm uppercase font-semibold">Total Mapel</p>
            <p class="text-2xl font-bold"><?= $totalMapel ?></p>
        </div>
    </div>
</div>

