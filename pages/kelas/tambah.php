<?php
global $conn;
require_once __DIR__ . '/../../includes/db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_kelas = $_POST['nama_kelas'] ?? '';
    $id_walikelas = $_POST['id_walikelas'] ?? '';

    if ($nama_kelas && $id_walikelas) {
        $stmt = $conn->prepare("INSERT INTO kelas (nama_kelas, id_walikelas) VALUES (?, ?)");
        $stmt->bind_param("si", $nama_kelas, $id_walikelas);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Kelas berhasil ditambahkan!';
            echo "<script>window.location.href = 'index.php?page=kelas';</script>";
            exit;
        } else {
            $message = '<p class="text-red-600 dark:text-red-400 font-bold">Gagal menyimpan data: ' . $stmt->error . '</p>';
        }
        $stmt->close();
    } else {
        $message = '<p class="text-red-600 dark:text-red-400 font-bold">Semua field harus diisi!</p>';
    }
}

// Fetch teachers for walikelas dropdown from guru and pengguna tables
$guru_query = mysqli_query($conn, "
    SELECT g.id_guru, p.nama 
    FROM guru g
    JOIN pengguna p ON g.id_guru = p.id_pengguna
    WHERE p.role = 'guru'
    ORDER BY p.nama
");
$guru_list = [];
while ($row = mysqli_fetch_assoc($guru_query)) {
    $guru_list[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kelas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'bounce-subtle': 'bounceSubtle 2s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        bounceSubtle: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-5px)' }
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-blue-900 dark:to-indigo-900 transition-all duration-500">

    <div class="container mx-auto px-4 py-8 animate-fade-in">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8 animate-slide-up">
                <h1 class="text-4xl font-bold text-black dark:text-white mb-2">
                    Tambah Kelas
                </h1>
                <p class="text-gray-600 dark:text-gray-300 text-lg">
                    Lengkapi data kelas dengan teliti
                </p>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto mt-4 rounded-full"></div>
            </div>

            <!-- Form Container -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 animate-slide-up border border-white/20">
                <form id="kelasForm" method="POST" action="" class="space-y-8">
                    <!-- Class Info Section -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-bold">1</span>
                            </div>
                            Data Kelas
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Kelas -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Nama Kelas *
                                </label>
                                <input 
                                    type="text" 
                                    name="nama_kelas"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500" 
                                    placeholder="Masukkan nama kelas (e.g., X IPA 1)"
                                >
                            </div>
                            
                            <!-- Wali Kelas -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Wali Kelas *
                                </label>
                                <select 
                                    name="id_walikelas"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500 cursor-pointer"
                                >
                                    <option value="">Pilih wali kelas</option>
                                    <?php foreach ($guru_list as $guru): ?>
                                        <option value="<?= $guru['id_guru'] ?>">
                                            <?= htmlspecialchars($guru['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-center pt-6 gap-4">
                        <button 
                            type="button" 
                            onclick="window.location.href='index.php?page=kelas'"
                            class="
                                px-8 py-3 
                                bg-gray-600 
                                text-white 
                                font-bold rounded-xl 
                                shadow-lg 
                                hover:bg-gray-700 
                                hover:shadow-xl 
                                transform hover:scale-105 
                                transition-all duration-300 
                                focus:outline-none focus:ring-4 
                                focus:ring-gray-300/50
                            "
                        >
                            Kembali
                        </button>
                        <button 
                            type="submit" 
                            class="
                                px-8 py-3 
                                bg-blue-700 
                                text-white 
                                font-bold rounded-xl 
                                shadow-lg 
                                hover:bg-blue-800 
                                hover:shadow-xl 
                                transform hover:scale-105 
                                transition-all duration-300 
                                focus:outline-none focus:ring-4 
                                focus:ring-blue-300/50
                            "
                        >
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Data
                            </span>
                        </button>
                    </div>
                </form>

                <?php if ($message): ?>
                    <div class="text-center mt-4">
                        <?= $message ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>