<?php
global $conn;

$id_guru = $_SESSION['id_pengguna'] ?? null; // Get logged-in teacher's ID
$id_kelas = $_GET['id'] ?? '';

if (!$id_kelas || !$id_guru) {
    $_SESSION['error'] = 'Kelas atau pengguna tidak ditemukan!';
    echo "<script>window.location.href = '/index.php?page=nilai';</script>";
    exit();
}

// Get the teacher's subject (mapel) and its name from mapel_guru and mapel
$stmt = $conn->prepare("SELECT mg.id_mapel, m.nama_mapel FROM mapel_guru mg JOIN mapel m ON mg.id_mapel = m.id_mapel WHERE mg.id_guru = ?");
$stmt->bind_param("i", $id_guru);
$stmt->execute();
$mapel = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$mapel) {
    $_SESSION['error'] = 'Mata pelajaran tidak ditemukan untuk guru ini!';
    echo "<script>window.location.href = '/index.php?page=nilai';</script>";
    exit();
}
$id_mapel = $mapel['id_mapel'];
$nama_mapel = $mapel['nama_mapel'];

// Get current academic year (assuming the latest id_tahun is the current one)
$stmt = $conn->prepare("SELECT id_tahun FROM tahun_ajaran ORDER BY id_tahun DESC LIMIT 1");
$stmt->execute();
$tahun = $stmt->get_result()->fetch_assoc();
$stmt->close();
$id_tahun = $tahun['id_tahun'] ?? null;

if (!$id_tahun) {
    $_SESSION['error'] = 'Tahun ajaran tidak ditemukan!';
    echo "<script>window.location.href = '/index.php?page=nilai';</script>";
    exit();
}

// Handle form submission for saving grades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_grades'])) {
    $siswa_ids = $_POST['id_siswa'] ?? [];
    $nilai_uh = $_POST['nilai_uh'] ?? [];
    $nilai_uts = $_POST['nilai_uts'] ?? [];
    $nilai_uas = $_POST['nilai_uas'] ?? [];

    foreach ($siswa_ids as $index => $id_siswa) {
        // Check if a grade record exists
        $stmt = $conn->prepare("SELECT id_nilai FROM nilai_siswa WHERE id_siswa = ? AND id_kelas = ? AND id_mapel = ? AND id_guru = ? AND id_tahun = ?");
        $stmt->bind_param("iiiii", $id_siswa, $id_kelas, $id_mapel, $id_guru, $id_tahun);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing = $result->fetch_assoc();
        $stmt->close();

        $uh = !empty($nilai_uh[$index]) ? floatval($nilai_uh[$index]) : null;
        $uts = !empty($nilai_uts[$index]) ? floatval($nilai_uts[$index]) : null;
        $uas = !empty($nilai_uas[$index]) ? floatval($nilai_uas[$index]) : null;

        // Calculate nilai_akhir as the average of non-null values
        $grades = array_filter([$uh, $uts, $uas], fn($val) => !is_null($val));
        $akhir = !empty($grades) ? array_sum($grades) / count($grades) : null;

        if ($existing) {
            // Update existing record
            $stmt = $conn->prepare("UPDATE nilai_siswa SET nilai_uh = ?, nilai_uts = ?, nilai_uas = ?, nilai_akhir = ? WHERE id_nilai = ?");
            $stmt->bind_param("ddddi", $uh, $uts, $uas, $akhir, $existing['id_nilai']);
        } else {
            // Insert new record
            $stmt = $conn->prepare("INSERT INTO nilai_siswa (id_siswa, id_kelas, id_mapel, id_guru, id_tahun, nilai_uh, nilai_uts, nilai_uas, nilai_akhir) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiiidddd", $id_siswa, $id_kelas, $id_mapel, $id_guru, $id_tahun, $uh, $uts, $uas, $akhir);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Nilai berhasil disimpan!';
        } else {
            $_SESSION['error'] = 'Gagal menyimpan nilai: ' . $stmt->error;
        }
        $stmt->close();
    }
    echo "<script>window.location.href = '/index.php?page=nilai&action=lihat&id=" . urlencode($id_kelas) . "';</script>";
    exit();
}

// Query to get class details
$stmt = $conn->prepare("SELECT k.nama_kelas, p.nama AS nama_walikelas FROM kelas k LEFT JOIN pengguna p ON k.id_walikelas = p.id_pengguna WHERE k.id_kelas = ?");
$stmt->bind_param("i", $id_kelas);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$class) {
    $_SESSION['error'] = 'Kelas tidak ditemukan!';
    echo "<script>window.location.href = '/index.php?page=nilai';</script>";
    exit();
}

// Query to get students and their grades
$stmt = $conn->prepare("SELECT s.id_siswa, s.nama_siswa, s.nis, s.jenis_kelamin, s.tanggal_lahir, s.alamat, n.nilai_uh, n.nilai_uts, n.nilai_uas, n.nilai_akhir FROM siswa s LEFT JOIN nilai_siswa n ON s.id_siswa = n.id_siswa AND n.id_kelas = ? AND n.id_mapel = ? AND n.id_guru = ? AND n.id_tahun = ? WHERE s.id_kelas = ?");
$stmt->bind_param("iiiii", $id_kelas, $id_mapel, $id_guru, $id_tahun, $id_kelas);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$stmt->close();

$page = 'nilai';
$columns = [
    'nama_siswa' => 'Nama Siswa',
    'nis' => 'NIS',
    'nilai_uh' => 'Nilai UH',
    'nilai_uts' => 'Nilai UTS',
    'nilai_uas' => 'Nilai UAS',
    'nilai_akhir' => 'Nilai Akhir',
];

$actions = [
    'view' => false,
    'edit' => false,
    'delete' => false,
];

require_once dirname(__DIR__, 3) . '/templates/alert.php';
?>

<div class="content-box dark:bg-gray-800 dark:text-white">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-2">
        Daftar Siswa - <?php echo htmlspecialchars($class['nama_kelas']); ?>
    </h2>
    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
        Wali Kelas: <?php echo htmlspecialchars($class['nama_walikelas'] ?? 'Tidak ada'); ?>
    </p>
    <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
        Mata Pelajaran: <?php echo htmlspecialchars($nama_mapel); ?>
    </p>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 mb-6" role="alert">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 mb-6" role="alert">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <?php foreach ($columns as $key => $label): ?>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                <?php echo htmlspecialchars($label); ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada siswa di kelas ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $index => $row): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($row['nama_siswa']); ?>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($row['nis']); ?>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="nilai_uh[<?php echo $index; ?>]" 
                                           value="<?php echo htmlspecialchars($row['nilai_uh'] ?? ''); ?>" 
                                           class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                           step="0.01" min="0" max="100" placeholder="-">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="nilai_uts[<?php echo $index; ?>]" 
                                           value="<?php echo htmlspecialchars($row['nilai_uts'] ?? ''); ?>" 
                                           class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                           step="0.01" min="0" max="100" placeholder="-">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="nilai_uas[<?php echo $index; ?>]" 
                                           value="<?php echo htmlspecialchars($row['nilai_uas'] ?? ''); ?>" 
                                           class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                           step="0.01" min="0" max="100" placeholder="-">
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($row['nilai_akhir'] ? number_format($row['nilai_akhir'], 2) : '-'); ?>
                                </td>
                                <input type="hidden" name="id_siswa[<?php echo $index; ?>]" 
                                       value="<?php echo htmlspecialchars($row['id_siswa']); ?>">
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex space-x-2">
            <button type="submit" name="save_grades" 
                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Simpan
            </button>
            <a href="/index.php?page=nilai" 
               class="px-4 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Kembali
            </a>
        </div>
    </form>
</div>