<?php
require_once __DIR__ . '/../../includes/db.php';

if (!$conn || $conn->connect_error) {
    error_log("Database connection failed: " . ($conn ? $conn->connect_error : "No connection"));
    die("Database connection failed. Please check your configuration.");
}

// Handle adding new academic year
if (isset($_POST['tambah_tahun'])) {
    $tahun = $_POST['tahun'] ?? '';
    $semester = $_POST['semester'] ?? '';

    if ($tahun && $semester && preg_match('/^\d{4}\/\d{4}$/', $tahun)) {
        list($tahun1, $tahun2) = explode('/', $tahun);
        if ((int)$tahun2 !== (int)$tahun1 + 1) {
            $message = '<p class="text-red-600 dark:text-red-400 font-bold">Format tahun ajaran tidak valid! Tahun kedua harus satu tahun setelah tahun pertama.</p>';
        } else {
            $check_query = $conn->prepare("SELECT COUNT(*) AS total FROM tahun_ajaran WHERE tahun = ? AND semester = ?");
            $check_query->bind_param("ss", $tahun, $semester);
            $check_query->execute();
            $result = $check_query->get_result();
            $count = $result->fetch_assoc()['total'];
            $check_query->close();

            if ($count > 0) {
                $message = '<p class="text-red-600 dark:text-red-400 font-bold">Tahun ajaran dan semester sudah ada!</p>';
            } else {
                $stmt = $conn->prepare("INSERT INTO tahun_ajaran (tahun, semester) VALUES (?, ?)");
                $stmt->bind_param("ss", $tahun, $semester);
                if ($stmt->execute()) {
                    $message = '<p class="text-green-600 dark:text-green-400 font-bold">Tahun ajaran berhasil ditambahkan!</p>';
                } else {
                    $message = '<p class="text-red-600 dark:text-red-400 font-bold">Gagal menambahkan tahun ajaran: ' . $stmt->error . '</p>';
                }
                $stmt->close();
            }
        }
    } else {
        $message = '<p class="text-red-600 dark:text-red-400 font-bold">Tahun ajaran harus dalam format YYYY/YYYY dan semester wajib diisi!</p>';
    }
}

$message = '';
$id_kelas = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$edit_mode = isset($_GET['edit']) && is_numeric($_GET['edit']);
$edit_id = $edit_mode ? (int)$_GET['edit'] : 0;

// Fetch class details
$kelas_query = $conn->prepare("SELECT nama_kelas FROM kelas WHERE id_kelas = ?");
$kelas_query->bind_param("i", $id_kelas);
$kelas_query->execute();
$kelas_result = $kelas_query->get_result();
$kelas = $kelas_result->fetch_assoc();
$kelas_query->close();

if (!$kelas) {
    $_SESSION['error'] = 'Kelas tidak ditemukan!';
    echo "<script>window.location.href = 'index.php?page=jadwal';</script>";
    exit;
}

// Fetch edit data if in edit mode
$edit_data = null;
if ($edit_mode) {
    $edit_query = $conn->prepare("
        SELECT id_kelas, hari, jam_mulai, jam_selesai, id_mapel, id_guru, ruang, id_tahun
        FROM jadwal WHERE id_jadwal = ? AND id_kelas = ?
    ");
    $edit_query->bind_param("ii", $edit_id, $id_kelas);
    $edit_query->execute();
    $edit_data = $edit_query->get_result()->fetch_assoc();
    $edit_query->close();

    if (!$edit_data) {
        $_SESSION['error'] = 'Jadwal tidak ditemukan!';
        echo "<script>window.location.href = 'index.php?page=jadwal&action=lihat&id=$id_kelas';</script>";
        exit;
    }
}

// Fetch schedules for the class
$jadwal_query = $conn->prepare("
    SELECT j.id_jadwal, j.hari, j.jam_mulai, j.jam_selesai, m.nama_mapel, p.nama AS nama_guru, j.ruang, CONCAT(t.tahun, ' ', t.semester) AS tahun_ajaran
    FROM jadwal j
    LEFT JOIN mapel m ON j.id_mapel = m.id_mapel
    LEFT JOIN guru g ON j.id_guru = g.id_guru
    LEFT JOIN pengguna p ON g.id_guru = p.id_pengguna
    LEFT JOIN tahun_ajaran t ON j.id_tahun = t.id_tahun
    WHERE j.id_kelas = ?
    ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), j.jam_mulai
");
$jadwal_query->bind_param("i", $id_kelas);
$jadwal_query->execute();
$result = $jadwal_query->get_result();
$jadwal_list = [];
while ($row = $result->fetch_assoc()) {
    $jadwal_list[] = $row;
}
$jadwal_query->close();

// Fetch subjects for form
$mapel_query = $conn->prepare("SELECT id_mapel, nama_mapel FROM mapel ORDER BY nama_mapel");
$mapel_query->execute();
$result = $mapel_query->get_result();
$mapel_list = [];
while ($row = $result->fetch_assoc()) {
    $mapel_list[] = $row;
}
$mapel_query->close();

// Fetch academic years for form
$tahun_query = $conn->prepare("SELECT id_tahun, tahun, semester FROM tahun_ajaran ORDER BY tahun DESC, semester DESC");
$tahun_query->execute();
$result = $tahun_query->get_result();
$tahun_ajaran_list = [];
while ($row = $result->fetch_assoc()) {
    $tahun_ajaran_list[] = $row;
}
$tahun_query->close();

// Fetch teachers by subject
$guru_by_mapel = [];
foreach ($mapel_list as $mapel) {
    $id_mapel = $mapel['id_mapel'];
    $guru_query = $conn->prepare("
        SELECT g.id_guru, p.nama
        FROM guru g
        JOIN pengguna p ON g.id_guru = p.id_pengguna
        JOIN mapel_guru mg ON g.id_guru = mg.id_guru
        WHERE p.role = 'guru' AND mg.id_mapel = ?
        ORDER BY p.nama
    ");
    $guru_query->bind_param("i", $id_mapel);
    $guru_query->execute();
    $result = $guru_query->get_result();
    $guru_list = [];
    while ($row = $result->fetch_assoc()) {
        $guru_list[] = $row;
    }
    $guru_query->close();
    $guru_by_mapel[$id_mapel] = $guru_list;
}

// Handle schedule form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['tambah_tahun'])) {
    $hari = $_POST['hari'] ?? '';
    $jam_mulai = $_POST['jam_mulai'] ?? '';
    $jam_selesai = $_POST['jam_selesai'] ?? '';
    $id_mapel = $_POST['id_mapel'] ?? null;
    $id_guru = !empty($_POST['id_guru']) ? (int)$_POST['id_guru'] : 0; // Default to 0 if no guru selected
    $ruang = trim($_POST['ruang'] ?? '');
    $id_tahun = (int)$_POST['id_tahun'] ?? 0;
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;

    // Validate inputs
    if (empty($hari) || empty($jam_mulai) || empty($jam_selesai) || empty($id_mapel) || empty($id_tahun)) {
        $message = '<p class="text-red-600 dark:text-red-400 font-bold">Semua kolom wajib diisi kecuali guru!</p>';
    } elseif (empty($ruang) || !preg_match('/^[a-zA-Z\s\-]+$/', $ruang)) {
        $message = '<p class="text-red-600 dark:text-red-400 font-bold">Ruang harus diisi dan hanya boleh berisi huruf, spasi, atau tanda hubung (tanpa angka)!</p>';
    } elseif ($id_guru === 0) {
        $message = '<p class="text-red-600 dark:text-red-400 font-bold">Guru harus dipilih!</p>';
    } else {
        $conn->begin_transaction();
        try {
            // Log input values for debugging
            error_log("Input values: id_kelas=$id_kelas, hari=$hari, jam_mulai=$jam_mulai, jam_selesai=$jam_selesai, id_mapel=$id_mapel, id_guru=$id_guru, ruang='$ruang', id_tahun=$id_tahun, edit_id=$edit_id");

            // Validate id_guru
            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM mapel_guru WHERE id_mapel = ? AND id_guru = ?");
            $stmt->bind_param("ii", $id_mapel, $id_guru);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['total'];
            $stmt->close();
            if ($count == 0) {
                throw new Exception('Guru tidak valid untuk mata pelajaran ini.');
            }

            // Validate id_tahun
            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tahun_ajaran WHERE id_tahun = ?");
            $stmt->bind_param("i", $id_tahun);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['total'];
            $stmt->close();
            if ($count == 0) {
                throw new Exception('Tahun ajaran tidak valid.');
            }

            if ($edit_id) {
                // Update existing schedule
                $query = "UPDATE jadwal SET hari = ?, jam_mulai = ?, jam_selesai = ?, id_mapel = ?, id_guru = ?, ruang = ?, id_tahun = ? WHERE id_jadwal = ? AND id_kelas = ?";
                error_log("Executing query: $query with values: hari=$hari, jam_mulai=$jam_mulai, jam_selesai=$jam_selesai, id_mapel=$id_mapel, id_guru=$id_guru, ruang='$ruang', id_tahun=$id_tahun, id_jadwal=$edit_id, id_kelas=$id_kelas");
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssisiiii", $hari, $jam_mulai, $jam_selesai, $id_mapel, $id_guru, $ruang, $id_tahun, $edit_id, $id_kelas);
                if (!$stmt->execute()) {
                    throw new Exception('Gagal memperbarui jadwal: ' . $stmt->error);
                }
                $stmt->close();
                $conn->commit();
                $_SESSION['success'] = 'Jadwal berhasil diperbarui!';
            } else {
                // Insert new schedule
                $query = "INSERT INTO jadwal (id_kelas, hari, jam_mulai, jam_selesai, id_mapel, id_guru, ruang, id_tahun) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                error_log("Executing query: $query with values: id_kelas=$id_kelas, hari=$hari, jam_mulai=$jam_mulai, jam_selesai=$jam_selesai, id_mapel=$id_mapel, id_guru=$id_guru, ruang='$ruang', id_tahun=$id_tahun");
                $stmt = $conn->prepare($query);
                $stmt->bind_param("issssisi", $id_kelas, $hari, $jam_mulai, $jam_selesai, $id_mapel, $id_guru, $ruang, $id_tahun);
                if (!$stmt->execute()) {
                    throw new Exception('Gagal menambahkan jadwal: ' . $stmt->error);
                }
                $stmt->close();
                $conn->commit();
                $_SESSION['success'] = 'Jadwal berhasil ditambahkan!';
            }

            echo "<script>window.location.href = 'index.php?page=jadwal&action=lihat&id=$id_kelas';</script>";
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Error: " . $e->getMessage());
            $message = '<p class="text-red-600 dark:text-red-400 font-bold">Gagal ' . ($edit_id ? 'memperbarui' : 'menambahkan') . ' jadwal: ' . $e->getMessage() . '</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kelas - <?= htmlspecialchars($kelas['nama_kelas']) ?></title>
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
        function toggleForm(id) {
            const form = document.getElementById(id);
            form.classList.toggle('hidden');
        }
        const guruByMapel = <?php echo json_encode($guru_by_mapel); ?>;
        function updateGuruList() {
            const mapelSelect = document.querySelector('select[name="id_mapel"]');
            const guruSelect = document.querySelector('select[name="id_guru"]');
            const idMapel = mapelSelect.value;

            guruSelect.innerHTML = '<option value="">Pilih Guru</option>';

            if (idMapel && guruByMapel[idMapel]) {
                if (guruByMapel[idMapel].length === 0) {
                    guruSelect.innerHTML = '<option value="">Belum ada guru untuk mata pelajaran ini</option>';
                } else {
                    guruByMapel[idMapel].forEach(teacher => {
                        const option = document.createElement('option');
                        option.value = teacher.id_guru;
                        option.textContent = teacher.nama;
                        option.selected = <?php echo $edit_mode ? 'teacher.id_guru == ' . json_encode($edit_data['id_guru']) : 'false'; ?>;
                        guruSelect.appendChild(option);
                    });
                }
            }
        }
        window.onload = function() {
            <?php if ($edit_mode): ?>
                updateGuruList();
                document.getElementById('jadwalForm').classList.remove('hidden');
            <?php endif; ?>
        };
    </script>
</head>
<body class="dark:bg-gray-900 dark:text-white min-h-screen transition-all duration-500">
    <div class="container mx-auto px-4 py-8 animate-fade-in">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-8 animate-slide-up">
                <h1 class="text-4xl font-bold mb-2">Jadwal Kelas <?= htmlspecialchars($kelas['nama_kelas']) ?></h1>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto mt-4 rounded-full"></div>
            </div>

            <?php if ($message): ?>
                <div class="text-center mb-4">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <!-- Add Academic Year Form -->
            <div id="tahunAjaranForm" class="hidden bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 mb-8 animate-slide-up border border-white/20">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold">Tambah Tahun Ajaran</h2>
                    <button onclick="toggleForm('tahunAjaranForm')" class="px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700">Tutup</button>
                </div>
                <form method="POST" action="" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 font-semibold">Tahun Ajaran *</label>
                            <input name="tahun" required placeholder="Contoh: 2025/2026" class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600" pattern="\d{4}/\d{4}" title="Format: YYYY/YYYY, misal 2025/2026" />
                        </div>
                        <div>
                            <label class="block mb-2 font-semibold">Semester *</label>
                            <select name="semester" required class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600">
                                <option value="">Pilih Semester</option>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-center pt-4 gap-4">
                        <button type="submit" name="tambah_tahun" class="px-8 py-3 bg-blue-700 text-white rounded-xl hover:bg-blue-800">Tambah Tahun Ajaran</button>
                    </div>
                </form>
            </div>

            <!-- Schedule Table -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 mb-8 animate-slide-up border border-white/20">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold">Daftar Jadwal</h2>
                    <div class="space-x-2">
                        <button onclick="toggleForm('jadwalForm')" class="px-4 py-2 bg-blue-700 text-white rounded-xl hover:bg-blue-800">Tambah Jadwal</button>
                        <button onclick="toggleForm('tahunAjaranForm')" class="px-4 py-2 bg-green-700 text-white rounded-xl hover:bg-green-800">Tambah Tahun Ajaran</button>
                    </div>
                </div>
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700">
                            <th class="px-4 py-2">Hari</th>
                            <th class="px-4 py-2">Jam</th>
                            <th class="px-4 py-2">Mata Pelajaran</th>
                            <th class="px-4 py-2">Guru</th>
                            <th class="px-4 py-2">Ruang</th>
                            <th class="px-4 py-2">Tahun Ajaran</th>
                            <th class="px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jadwal_list)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">Belum ada jadwal untuk kelas ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($jadwal_list as $jadwal): ?>
                                <tr class="border-b dark:border-gray-600">
                                    <td class="px-4 py-2"><?= htmlspecialchars($jadwal['hari']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($jadwal['jam_mulai'] . ' - ' . $jadwal['jam_selesai']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($jadwal['nama_mapel']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($jadwal['nama_guru'] ?? '-') ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($jadwal['ruang'] ?? '-') ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($jadwal['tahun_ajaran'] ?? '-') ?></td>
                                    <td class="px-4 py-2">
                                        <a href="index.php?page=jadwal&action=lihat&id=<?= $id_kelas ?>&edit=<?= $jadwal['id_jadwal'] ?>" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Add/Edit Schedule Form -->
            <div id="jadwalForm" class="hidden bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 animate-slide-up border border-white/20">
                <h2 class="text-2xl font-semibold mb-4"><?= $edit_mode ? 'Edit Jadwal' : 'Tambah Jadwal' ?></h2>
                <form method="POST" action="" class="space-y-8">
                    <input type="hidden" name="edit_id" value="<?= $edit_mode ? $edit_id : '' ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 font-semibold">Hari *</label>
                            <select name="hari" required class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600">
                                <option value="">Pilih Hari</option>
                                <?php foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari): ?>
                                    <option value="<?= $hari ?>" <?= $edit_mode && $edit_data['hari'] === $hari ? 'selected' : '' ?>><?= $hari ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 font-semibold">Jam Mulai *</label>
                            <input type="time" name="jam_mulai" required class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600" value="<?= $edit_mode ? htmlspecialchars($edit_data['jam_mulai']) : '' ?>" />
                        </div>
                        <div>
                            <label class="block mb-2 font-semibold">Jam Selesai *</label>
                            <input type="time" name="jam_selesai" required class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600" value="<?= $edit_mode ? htmlspecialchars($edit_data['jam_selesai']) : '' ?>" />
                        </div>
                        <div>
                            <label class="block mb-2 font-semibold">Mata Pelajaran *</label>
                            <select name="id_mapel" required class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600" onchange="updateGuruList()">
                                <option value="">Pilih Mata Pelajaran</option>
                                <?php foreach ($mapel_list as $mapel): ?>
                                    <option value="<?= htmlspecialchars($mapel['id_mapel']) ?>" <?= $edit_mode && $edit_data['id_mapel'] == $mapel['id_mapel'] ? 'selected' : '' ?>><?= htmlspecialchars($mapel['nama_mapel']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 font-semibold">Guru Pengajar *</label>
                            <select name="id_guru" required class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600">
                                <option value="">Pilih Guru</option>
                            </select>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pilih mata pelajaran terlebih dahulu untuk melihat daftar guru.</p>
                        </div>
                        <div>
                            <label class="block mb-2 font-semibold">Ruang *</label>
                            <input name="ruang" required placeholder="Masukkan nama ruang" class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600" value="<?= $edit_mode ? htmlspecialchars($edit_data['ruang']) : '' ?>" pattern="[a-zA-Z\s\-]+" title="Hanya boleh berisi huruf, spasi, atau tanda hubung (tanpa angka)" />
                        </div>
                        <div>
                            <label class="block mb-2 font-semibold">Tahun Ajaran *</label>
                            <select name="id_tahun" required class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600">
                                <option value="">Pilih Tahun Ajaran</option>
                                <?php if (empty($tahun_ajaran_list)): ?>
                                    <option value="" disabled>Tidak ada tahun ajaran tersedia</option>
                                <?php else: ?>
                                    <?php foreach ($tahun_ajaran_list as $tahun_ajaran): ?>
                                        <option value="<?= htmlspecialchars($tahun_ajaran['id_tahun']) ?>" <?= $edit_mode && $edit_data['id_tahun'] == $tahun_ajaran['id_tahun'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tahun_ajaran['tahun'] . ' ' . $tahun_ajaran['semester']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (empty($tahun_ajaran_list)): ?>
                                <p class="text-sm text-red-500 dark:text-red-400 mt-1">Tambahkan tahun ajaran terlebih dahulu melalui tombol "Tambah Tahun Ajaran".</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex justify-center pt-6 gap-4">
                        <button type="button" onclick="toggleForm('jadwalForm')" class="px-8 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700">Batal</button>
                        <button type="submit" class="px-8 py-3 bg-blue-700 text-white rounded-xl hover:bg-blue-800" <?php echo empty($tahun_ajaran_list) ? 'disabled' : ''; ?>><?= $edit_mode ? 'Simpan Perubahan' : 'Tambah Jadwal' ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>