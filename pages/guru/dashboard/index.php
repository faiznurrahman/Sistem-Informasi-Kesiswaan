<?php
global $conn;
require_once dirname(__DIR__, 3) . '/includes/db.php';

// Pastikan guru sudah login
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'guru') {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Anda harus login sebagai guru untuk mengakses dashboard.'
    ];
    echo "<script>window.location.href = '/login.php';</script>";
    exit;
}

$id_guru = $_SESSION['id_pengguna'];

// Ambil nama kelas sebagai wali kelas
$kelas_query = mysqli_query($conn, "SELECT nama_kelas FROM kelas WHERE id_walikelas = '$id_guru'");
$kelas = mysqli_fetch_assoc($kelas_query);
$nama_kelas = $kelas['nama_kelas'] ?? '-';

// Tahun ajaran aktif
$tahun_ajaran_query = mysqli_query($conn, "SELECT tahun, semester FROM tahun_ajaran ORDER BY id_tahun DESC LIMIT 1");
$tahun_ajaran = mysqli_fetch_assoc($tahun_ajaran_query);
$tahun_ajaran_str = $tahun_ajaran ? $tahun_ajaran['tahun'] . ' - ' . $tahun_ajaran['semester'] : '-';

// Hari dan tanggal
$hari_ini = date('l');
$hari_map = [
    'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
];
$hari_ini_id = $hari_map[$hari_ini] ?? 'Sabtu';
$tanggal = date('d F Y');

// Jadwal hari ini
$jadwal_query = mysqli_query($conn, "
    SELECT j.jam_mulai, j.jam_selesai, m.nama_mapel, k.nama_kelas, j.ruang
    FROM jadwal j
    LEFT JOIN mapel m ON j.id_mapel = m.id_mapel
    LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
    WHERE j.id_guru = '$id_guru' AND j.hari = '$hari_ini_id'
    ORDER BY j.jam_mulai
");
?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold mb-6">Selamat Datang, <?= htmlspecialchars($_SESSION['nama'] ?? 'Guru') ?>!</h1>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
      <div class="bg-yellow-400 text-white p-5 rounded-lg shadow flex items-center">
        <i class="ri-calendar-todo-fill text-4xl mr-4"></i>
        <div>
          <p class="text-sm uppercase font-semibold">Hari Ini</p>
          <p class="text-2xl font-bold"><?= $hari_ini_id . ', ' . $tanggal ?></p>
        </div>
      </div>
      <div class="bg-blue-500 text-white p-5 rounded-lg shadow flex items-center">
        <i class="ri-book-open-fill text-4xl mr-4"></i>
        <div>
          <p class="text-sm uppercase font-semibold">Tahun Ajaran</p>
          <p class="text-2xl font-bold"><?= htmlspecialchars($tahun_ajaran_str) ?></p>
        </div>
      </div>
    </div>

    <!-- Jadwal Hari Ini -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
      <h2 class="text-xl font-semibold mb-4">Jadwal Mengajar Hari Ini</h2>
      <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse">
          <thead class="bg-blue-500 text-white text-sm uppercase">
            <tr>
              <th class="py-2 px-4 text-left">Jam</th>
              <th class="py-2 px-4 text-left">Mata Pelajaran</th>
              <th class="py-2 px-4 text-left">Kelas</th>
              <th class="py-2 px-4 text-left">Ruang</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($jadwal_query) === 0): ?>
              <tr>
                <td colspan="4" class="text-center py-4 text-gray-500">Tidak ada jadwal mengajar hari ini.</td>
              </tr>
            <?php else: ?>
              <?php while ($jadwal = mysqli_fetch_assoc($jadwal_query)): ?>
                <tr class="border-b border-gray-200">
                  <td class="py-2 px-4"><?= substr($jadwal['jam_mulai'], 0, 5) . ' - ' . substr($jadwal['jam_selesai'], 0, 5) ?></td>
                  <td class="py-2 px-4"><?= htmlspecialchars($jadwal['nama_mapel'] ?? '-') ?></td>
                  <td class="py-2 px-4"><?= htmlspecialchars($jadwal['nama_kelas'] ?? '-') ?></td>
                  <td class="py-2 px-4"><?= htmlspecialchars($jadwal['ruang'] ?? '-') ?></td>
                </tr>
              <?php endwhile ?>
            <?php endif ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>