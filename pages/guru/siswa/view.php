<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Data Siswa - Profil</title>
    <link rel="stylesheet" href="../../dist/css/style.css" />
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
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .table-container {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        html.dark .table-container {
            background: linear-gradient(135deg, rgba(31, 41, 55, 0.95) 0%, rgba(17, 24, 39, 0.95) 100%);
            border-color: rgba(255, 255, 255, 0.1);
        }
        .table-header th {
            font-weight: 600;
            color: #374151;
            padding: 1rem 0.75rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        html.dark .table-header th {
            color: #d1d5db;
        }
        .table-row td {
            padding: 0.875rem 0.75rem;
            color: #4b5563;
            font-size: 0.875rem;
        }
        html.dark .table-row td {
            color: #d1d5db;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

<div class="container mx-auto px-4 py-8 animate-fade-in">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8 animate-slide-up">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                Profil Siswa
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">
                Data lengkap informasi siswa
            </p>
            <div class="w-24 h-1 bg-blue-500 mx-auto mt-4 rounded-full"></div>
        </div>

        <!-- Navigation Button -->
        <div class="flex justify-center mb-8">
            <button onclick="window.history.back()" class="px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 shadow-md flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </button>
        </div>

        <!-- Profile Card with PHP Integration -->
        <?php
        // Pastikan koneksi database sudah di-include
        // global $conn;

        $id = $_GET['id'] ?? null;
        $action = $_GET['action'] ?? '';

        $id = filter_var($id, FILTER_VALIDATE_INT);

        if ($action === 'lihat' && $id !== false && $id > 0) {
            // Query untuk data siswa
            $query = "SELECT s.nama_siswa, s.nis, s.jenis_kelamin, s.tanggal_lahir, s.alamat, s.foto, k.nama_kelas
                      FROM siswa s
                      LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
                      WHERE s.id_siswa = ?";

            $stmt = $conn->prepare($query);
            if (!$stmt) {
                echo "<div class='text-red-500 p-4 rounded-lg bg-red-100'>Kesalahan query: " . htmlspecialchars($conn->error) . "</div>";
                exit;
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                echo "<div class='text-center text-red-500 text-lg font-semibold p-4 rounded-lg bg-red-100'>Data siswa tidak ditemukan.</div>";
                exit;
            }

            $siswa = $result->fetch_assoc();
            $stmt->close();

            // Format tanggal lahir
            $tanggal_lahir = $siswa['tanggal_lahir'] ? date('d F Y', strtotime($siswa['tanggal_lahir'])) : '-';
        ?>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-slide-up">
            <!-- Photo Section -->
            <div class="bg-blue-600 dark:bg-blue-700 px-8 py-12">
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 rounded-full bg-white p-1 shadow-lg mb-6">
                        <?php
                        $foto = htmlspecialchars($siswa['foto'] ?? '');
                        $fotoPath = "/uploads/" . $foto;
                        $fotoExists = !empty($foto) && file_exists($_SERVER['DOCUMENT_ROOT'] . $fotoPath);
                        ?>
                        <img
                            src="<?= $fotoExists ? $fotoPath : 'https://via.placeholder.com/200x200/3B82F6/FFFFFF?text=Foto+Siswa' ?>"
                            alt="Foto Siswa"
                            class="w-full h-full rounded-full object-cover"
                        >
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-2"><?= htmlspecialchars($siswa['nama_siswa']) ?></h2>
                    <p class="text-blue-100 text-lg">NIS: <?= htmlspecialchars($siswa['nis']) ?></p>
                </div>
            </div>

            <!-- Data Section -->
            <div class="p-8 space-y-8">
                <!-- Personal Info Section -->
                <div class="space-y-6">
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
                        Data Pribadi
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">Nama Lengkap</label>
                            <p class="text-gray-900 dark:text-white text-lg"><?= htmlspecialchars($siswa['nama_siswa']) ?></p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">NIS</label>
                            <p class="text-gray-900 dark:text-white text-lg"><?= htmlspecialchars($siswa['nis']) ?></p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">Jenis Kelamin</label>
                            <p class="text-gray-900 dark:text-white text-lg">
                                <?= $siswa['jenis_kelamin'] === 'L' ? 'Laki-laki' : ($siswa['jenis_kelamin'] === 'P' ? 'Perempuan' : '-') ?>
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">Tanggal Lahir</label>
                            <p class="text-gray-900 dark:text-white text-lg"><?= $tanggal_lahir ?></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">Alamat Lengkap</label>
                        <p class="text-gray-900 dark:text-white text-lg leading-relaxed"><?= nl2br(htmlspecialchars($siswa['alamat'] ?? '-')) ?></p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">Kelas</label>
                        <p class="text-gray-900 dark:text-white text-lg"><?= htmlspecialchars($siswa['nama_kelas'] ?? '-') ?></p>
                    </div>
                </div>

                <!-- Nilai Siswa Section -->
                <div class="space-y-6">
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
                        Nilai Siswa
                    </h3>

                    <?php
                    // Query untuk nilai siswa
                    $query_nilai = "SELECT m.nama_mapel, n.nilai_uh, n.nilai_uts, n.nilai_uas, n.nilai_akhir, t.tahun, t.semester
                                    FROM nilai_siswa n
                                    LEFT JOIN mapel m ON n.id_mapel = m.id_mapel
                                    LEFT JOIN tahun_ajaran t ON n.id_tahun = t.id_tahun
                                    WHERE n.id_siswa = ?";
                    
                    $stmt_nilai = $conn->prepare($query_nilai);
                    if (!$stmt_nilai) {
                        echo "<div class='text-red-500 p-4 rounded-lg bg-red-100'>Kesalahan query nilai: " . htmlspecialchars($conn->error) . "</div>";
                    } else {
                        $stmt_nilai->bind_param("i", $id);
                        $stmt_nilai->execute();
                        $result_nilai = $stmt_nilai->get_result();

                        if ($result_nilai->num_rows === 0) {
                            echo "<div class='text-center text-gray-600 dark:text-gray-400 text-lg p-4'>Belum ada data nilai untuk siswa ini.</div>";
                        } else {
                    ?>
                    <div class="table-container shadow-md rounded-xl">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="table-header">
                                    <tr>
                                        <th class="text-center w-16">No</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Nilai UH</th>
                                        <th>Nilai UTS</th>
                                        <th>Nilai UAS</th>
                                        <th>Nilai Akhir</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Semester</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($nilai = $result_nilai->fetch_assoc()) {
                                    ?>
                                    <tr class="table-row">
                                        <td class="text-center font-medium"><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($nilai['nama_mapel'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($nilai['nilai_uh'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($nilai['nilai_uts'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($nilai['nilai_uas'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($nilai['nilai_akhir'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($nilai['tahun'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($nilai['semester'] ?? '-') ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                        }
                        $stmt_nilai->close();
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        } else {
            echo "<div class='text-center text-red-500 text-lg font-semibold p-4 rounded-lg bg-red-100'>ID tidak valid atau data siswa tidak ditemukan.</div>";
        }
        ?>
    </div>
</div>

</body>
</html>