<?php
global $conn;
require_once dirname(__DIR__, 3) . '/includes/db.php';

// Ambil ID kelas dari URL
$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = 'ID kelas tidak valid!';
    echo "<script>window.location.href = '/index.php?page=kelas';</script>";
    exit;
}

// Ambil data kelas
$stmt = $conn->prepare("
    SELECT k.id_kelas, k.nama_kelas, p.nama AS nama_walikelas
    FROM kelas k
    LEFT JOIN pengguna p ON k.id_walikelas = p.id_pengguna
    WHERE k.id_kelas = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$kelas = $result->fetch_assoc();
$stmt->close();

if (!$kelas) {
    $_SESSION['error'] = 'Data kelas tidak ditemukan!';
    echo "<script>window.location.href = '/index.php?page=kelas';</script>";
    exit;
}

// Ambil daftar siswa berdasarkan id_kelas
$siswa_query = $conn->prepare("
    SELECT nis, nama_siswa, jenis_kelamin, tanggal_lahir, alamat
    FROM siswa
    WHERE id_kelas = ?
");
$siswa_query->bind_param("i", $id);
$siswa_query->execute();
$siswa_result = $siswa_query->get_result();
$siswa_list = [];
$no = 1;
while ($row = $siswa_result->fetch_assoc()) {
    $row['no'] = $no++;
    $siswa_list[] = $row;
}
$siswa_query->close();
?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lihat Kelas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          animation: {
            'fade-in': 'fadeIn 0.5s ease-in-out',
            'slide-up': 'slideUp 0.6s ease-out',
          },
          keyframes: {
            fadeIn: {
              '0%': { opacity: '0' },
              '100%': { opacity: '1' },
            },
            slideUp: {
              '0%': { opacity: '0', transform: 'translateY(20px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' },
            },
          },
        },
      },
    };
  </script>
</head>
<body class="dark:bg-gray-900 dark:text-white min-h-screen transition-all duration-500">

  <div class="container mx-auto px-4 py-8 animate-fade-in">
    <div class="max-w-4xl mx-auto">
      <div class="text-center mb-8 animate-slide-up">
        <h1 class="text-4xl font-bold mb-2">Detail Kelas</h1>
        <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto mt-4 rounded-full"></div>
      </div>

      <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 animate-slide-up border border-white/20">
        <div class="space-y-6">
          <div>
            <label class="block mb-2 font-semibold text-gray-700 dark:text-gray-300">Nama Kelas</label>
            <p class="w-full px-4 py-3 rounded-xl border bg-gray-100 dark:bg-gray-700 dark:border-gray-600"><?= htmlspecialchars($kelas['nama_kelas']) ?></p>
          </div>

          <div>
            <label class="block mb-2 font-semibold text-gray-700 dark:text-gray-300">Wali Kelas</label>
            <p class="w-full px-4 py-3 rounded-xl border bg-gray-100 dark:bg-gray-700 dark:border-gray-600"><?= htmlspecialchars($kelas['nama_walikelas'] ?: 'Tidak ada wali kelas') ?></p>
          </div>
        </div>

        <div class="mt-8">
          <h2 class="text-2xl font-bold mb-4">Daftar Siswa</h2>
          <?php if (empty($siswa_list)): ?>
            <p class="text-center text-gray-500 dark:text-gray-400">Tidak ada siswa di kelas ini.</p>
          <?php else: ?>
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-gray-100 dark:bg-gray-700">
                    <th class="px-4 py-2 border">No</th>
                    <th class="px-4 py-2 border">NIS</th>
                    <th class="px-4 py-2 border">Nama Siswa</th>
                    <th class="px-4 py-2 border">Jenis Kelamin</th>
                    <th class="px-4 py-2 border">Tanggal Lahir</th>
                    <th class="px-4 py-2 border">Alamat</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($siswa_list as $siswa): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                      <td class="px-4 py-2 border"><?= $siswa['no'] ?></td>
                      <td class="px-4 py-2 border"><?= htmlspecialchars($siswa['nis']) ?></td>
                      <td class="px-4 py-2 border"><?= htmlspecialchars($siswa['nama_siswa']) ?></td>
                      <td class="px-4 py-2 border"><?= $siswa['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                      <td class="px-4 py-2 border"><?= htmlspecialchars($siswa['tanggal_lahir'] ?: '-') ?></td>
                      <td class="px-4 py-2 border"><?= htmlspecialchars($siswa['alamat'] ?: '-') ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>

        <div class="flex justify-center pt-8">
          <button type="button" onclick="history.back()" class="px-8 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700">Kembali</button>
        </div>
      </div>
    </div>
  </div>

</body>
</html>