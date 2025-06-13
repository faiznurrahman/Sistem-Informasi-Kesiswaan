<?php
global $conn;

$message = '';
$id_kelas = $_GET['id'] ?? 0;

// Fetch existing class data
if ($id_kelas) {
    $stmt = $conn->prepare("SELECT nama_kelas, id_walikelas FROM kelas WHERE id_kelas = ?");
    $stmt->bind_param("i", $id_kelas);
    $stmt->execute();
    $result = $stmt->get_result();
    $kelas = $result->fetch_assoc();
    $stmt->close();

    if (!$kelas) {
        $_SESSION['error'] = 'Kelas tidak ditemukan!';
        echo "<script>window.location.href = 'index.php?page=kelas';</script>";
        exit;
    }
} else {
    $_SESSION['error'] = 'ID kelas tidak valid!';
    echo "<script>window.location.href = 'index.php?page=kelas';</script>";
    exit;
}

// Fetch pengguna for walikelas dropdown
$pengguna_query = mysqli_query($conn, "SELECT id_pengguna, nama FROM pengguna ORDER BY nama");
$pengguna_list = [];
while ($row = mysqli_fetch_assoc($pengguna_query)) {
    $pengguna_list[] = $row;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_kelas = $_POST['nama_kelas'] ?? '';
    $id_walikelas = $_POST['id_walikelas'] ?? '';

    if ($nama_kelas && $id_walikelas) {
        $stmt = $conn->prepare("UPDATE kelas SET nama_kelas = ?, id_walikelas = ? WHERE id_kelas = ?");
        $stmt->bind_param("sii", $nama_kelas, $id_walikelas, $id_kelas);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Kelas berhasil diperbarui!';
            echo "<script>window.location.href = 'index.php?page=kelas';</script>";
            exit;
        } else {
            $message = '<p class="text-red-600 dark:text-red-400 font-bold">Gagal memperbarui data: ' . $stmt->error . '</p>';
        }
        $stmt->close();
    } else {
        $message = '<p class="text-red-600 dark:text-red-400 font-bold">Semua field harus diisi!</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kelas</title>
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
                <h1 class="text-4xl font-bold mb-2">Edit Kelas</h1>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 animate-slide-up border border-white/20">
                <form id="kelasForm" method="POST" action="" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 font-semibold">Nama Kelas *</label>
                            <input name="nama_kelas" required placeholder="Masukkan nama kelas" value="<?php echo htmlspecialchars($kelas['nama_kelas']); ?>" class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600" />
                        </div>
                        <div>
                            <label class="block mb-2 font-semibold">Wali Kelas *</label>
                            <select name="id_walikelas" required class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600">
                                <option value="">Pilih wali kelas</option>
                                <?php foreach ($pengguna_list as $pengguna): ?>
                                    <option value="<?php echo $pengguna['id_pengguna']; ?>" <?php echo $pengguna['id_pengguna'] == $kelas['id_walikelas'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($pengguna['nama']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-center pt-6 gap-4">
                        <button type="button" onclick="history.back()" class="px-8 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700">Kembali</button>
                        <button type="submit" class="px-8 py-3 bg-blue-700 text-white rounded-xl hover:bg-blue-800">Simpan Data</button>
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