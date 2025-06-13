<?php
global $conn;

$message = '';
$id_mapel = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch existing mapel data
$mapel_query = $conn->prepare("SELECT nama_mapel FROM mapel WHERE id_mapel = ?");
$mapel_query->bind_param("i", $id_mapel);
$mapel_query->execute();
$mapel_result = $mapel_query->get_result();
$mapel = $mapel_result->fetch_assoc();
$mapel_query->close();

if (!$mapel) {
    $_SESSION['error'] = 'Mata pelajaran tidak ditemukan!';
    echo "<script>window.location.href = 'index.php?page=mapel';</script>";
    exit;
}

// Fetch current guru associated with mapel
$current_guru_id = null;
$guru_query = $conn->prepare("SELECT id_guru FROM mapel_guru WHERE id_mapel = ?");
$guru_query->bind_param("i", $id_mapel);
$guru_query->execute();
$guru_result = $guru_query->get_result();
if ($guru_row = $guru_result->fetch_assoc()) {
    $current_guru_id = $guru_row['id_guru'];
}
$guru_query->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_mapel = $_POST['nama_mapel'] ?? '';
    $id_guru = $_POST['id_guru'] ?? null;

    if ($nama_mapel) {
        $conn->begin_transaction();
        try {
            // Update nama_mapel
            $stmt = $conn->prepare("UPDATE mapel SET nama_mapel = ? WHERE id_mapel = ?");
            $stmt->bind_param("si", $nama_mapel, $id_mapel);
            if (!$stmt->execute()) {
                throw new Exception('Gagal memperbarui mata pelajaran: ' . $stmt->error);
            }
            $stmt->close();

            // Handle mapel_guru relationship
            // Delete existing mapel_guru record
            $stmt = $conn->prepare("DELETE FROM mapel_guru WHERE id_mapel = ?");
            $stmt->bind_param("i", $id_mapel);
            $stmt->execute();
            $stmt->close();

            // Insert new mapel_guru record if id_guru is provided
            if ($id_guru) {
                $stmt = $conn->prepare("INSERT INTO mapel_guru (id_mapel, id_guru) VALUES (?, ?)");
                $stmt->bind_param("ii", $id_mapel, $id_guru);
                if (!$stmt->execute()) {
                    throw new Exception('Gagal menghubungkan guru: ' . $stmt->error);
                }
                $stmt->close();
            }

            // Commit transaction
            $conn->commit();
            $_SESSION['success'] = 'Mata pelajaran berhasil diperbarui!';
            echo "<script>window.location.href = 'index.php?page=mapel';</script>";
            exit;
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            $message = '<p class="text-red-600 dark:text-red-400 font-bold">Gagal memperbarui data: ' . $e->getMessage() . '</p>';
        }
    } else {
        $message = '<p class="text-red-600 dark:text-red-400 font-bold">Nama mata pelajaran harus diisi!</p>';
    }
}

// Fetch list of gurus for dropdown
$guru_query = mysqli_query($conn, "
    SELECT g.id_guru, p.nama
    FROM guru g
    JOIN pengguna p ON g.id_guru = p.id_pengguna
    WHERE p.role = 'guru'
");
$gurus = [];
while ($row = mysqli_fetch_assoc($guru_query)) {
    $gurus[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mata Pelajaran</title>
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
                <h1 class="text-4xl font-bold mb-2">Edit Mata Pelajaran</h1>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 animate-slide-up border border-white/20">
                <form id="mapelForm" method="POST" action="" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 font-semibold">Nama Mata Pelajaran *</label>
                            <input name="nama_mapel" required placeholder="Masukkan nama mata pelajaran" value="<?= htmlspecialchars($mapel['nama_mapel']) ?>" class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600" />
                        </div>
                        <div>
                            <label class="block mb-2 font-semibold">Guru Pengajar</label>
                            <select name="id_guru" class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600">
                                <option value="">Tidak ada guru</option>
                                <?php foreach ($gurus as $guru): ?>
                                    <option value="<?= htmlspecialchars($guru['id_guru']) ?>" <?= $guru['id_guru'] == $current_guru_id ? 'selected' : '' ?>><?= htmlspecialchars($guru['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-center pt-6 gap-4">
                        <button type="button" onclick="history.back()" class="px-8 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700">Kembali</button>
                        <button type="submit" class="px-8 py-3 bg-blue-700 text-white rounded-xl hover:bg-blue-800">Simpan Perubahan</button>
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