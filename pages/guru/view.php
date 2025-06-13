<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Data Guru - Profil</title>
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
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

<div class="container mx-auto px-4 py-8 animate-fade-in">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8 animate-slide-up">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                Profil Guru
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">
                Data lengkap informasi guru
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
        // Pastikan koneksi sudah include sebelumnya dan $conn tersedia
        // global $conn;

        $id = $_GET['id'] ?? null;
        $action = $_GET['action'] ?? '';

        $id = filter_var($id, FILTER_VALIDATE_INT);

        if ($action === 'lihat' && $id !== false && $id > 0) {
            // Query menggunakan LEFT JOIN seperti pada kode kedua
            $query = "SELECT p.nip, p.nama, g.jenis_kelamin, g.tempat_tanggal_lahir, g.alamat, 
                             g.no_hp, g.email, g.pendidikan_terakhir, g.program_studi, g.foto
                      FROM pengguna p
                      LEFT JOIN guru g ON p.id_pengguna = g.id_guru
                      WHERE p.id_pengguna = ?";

            $stmt = $conn->prepare($query);
            if (!$stmt) {
                echo "<div class='text-red-500 p-4 rounded-lg bg-red-100'>Kesalahan query: " . htmlspecialchars($conn->error) . "</div>";
                exit;
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                echo "<div class='text-center text-red-500 text-lg font-semibold p-4 rounded-lg bg-red-100'>Data tidak ditemukan.</div>";
                exit;
            }

            $guru = $result->fetch_assoc();

            // Pisah tempat dan tanggal lahir dari satu kolom
            $ttl = $guru['tempat_tanggal_lahir'] ?? '-';
            $parts = explode(',', $ttl);
            $tempat = trim($parts[0] ?? '-');
            $tanggal_raw = trim($parts[1] ?? null);
            $tanggal = '-';
            if ($tanggal_raw) {
                $timestamp = strtotime($tanggal_raw);
                if ($timestamp !== false) {
                    $tanggal = date('d F Y', $timestamp);
                }
            }
        ?>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-slide-up">
            <!-- Photo Section -->
<div class="bg-blue-600 dark:bg-blue-700 px-8 py-12">
    <div class="flex flex-col items-center">
        <div class="w-32 h-32 rounded-full bg-white p-1 shadow-lg mb-6">
            <?php
            $foto = htmlspecialchars($guru['foto'] ?? '');
            $fotoPath = "/uploads/" . $foto;
            $fotoExists = !empty($foto) && file_exists($_SERVER['DOCUMENT_ROOT'] . $fotoPath);
            ?>
            <img
                src="<?= $fotoExists ? $fotoPath : 'https://via.placeholder.com/200x200/3B82F6/FFFFFF?text=Foto+Guru' ?>"
                alt="Foto Guru"
                class="w-full h-full rounded-full object-cover"
            >
        </div>
        <h2 class="text-3xl font-bold text-white mb-2"><?= htmlspecialchars($guru['nama']) ?></h2>
        <p class="text-blue-100 text-lg">NIP: <?= htmlspecialchars($guru['nip']) ?></p>
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
                            <p class="text-gray-900 dark:text-white text-lg"><?= htmlspecialchars($guru['nama']) ?></p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">NIP</label>
                            <p class="text-gray-900 dark:text-white text-lg"><?= htmlspecialchars($guru['nip']) ?></p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">Jenis Kelamin</label>
                            <p class="text-gray-900 dark:text-white text-lg">
                                <?= $guru['jenis_kelamin'] === 'L' ? 'Laki-laki' : ($guru['jenis_kelamin'] === 'P' ? 'Perempuan' : '-') ?>
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">Tempat, Tanggal Lahir</label>
                            <p class="text-gray-900 dark:text-white text-lg"><?= htmlspecialchars($tempat) ?>, <?= $tanggal ?></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">Alamat Lengkap</label>
                        <p class="text-gray-900 dark:text-white text-lg leading-relaxed"><?= nl2br(htmlspecialchars($guru['alamat'] ?? '-')) ?></p>
                    </div>
                </div>

                <!-- Contact & Education Section -->
                <div class="space-y-6">
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
                        Kontak & Pendidikan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">No Telepon</label>
                            <p class="text-gray-900 dark:text-white text-lg"><?= htmlspecialchars($guru['no_hp'] ?? '-') ?></p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">Email</label>
                            <p class="text-gray-900 dark:text-white text-lg"><?= htmlspecialchars($guru['email'] ?? '-') ?></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">Pendidikan Terakhir</label>
                        <p class="text-gray-900 dark:text-white text-lg"><?= htmlspecialchars($guru['pendidikan_terakhir'] ?? '-') ?></p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">Program Studi</label>
                        <p class="text-gray-900 dark:text-white text-lg"><?= htmlspecialchars($guru['program_studi'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php
            $stmt->close();
        } else {
            echo "<div class='text-center text-red-500 text-lg font-semibold p-4 rounded-lg bg-red-100'>ID tidak valid atau data tidak ditemukan.</div>";
        }
        ?>
    </div>
</div>

</body>
</html>