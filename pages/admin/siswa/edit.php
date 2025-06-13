<?php
global $conn;
require_once dirname(__DIR__, 3) . '/includes/db.php';

$message = '';
$student = null;

// Get student ID from URL (changed from 'id_siswa' to 'id' to match URL)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'ID siswa tidak valid!'
    ];
    echo "<script>window.location.href = 'index.php?page=siswa';</script>";
    exit;
}

$id_siswa = (int)$_GET['id'];

// Fetch student data
$stmt = $conn->prepare("SELECT * FROM siswa WHERE id_siswa = ?");
$stmt->bind_param("i", $id_siswa);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Data siswa tidak ditemukan!'
    ];
    echo "<script>window.location.href = 'index.php?page=siswa';</script>";
    exit;
}
$student = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_siswa = $_POST['nama_siswa'];
    $nis = $_POST['nis'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'] ?: null;
    $alamat = $_POST['alamat'];
    $id_kelas = $_POST['id_kelas'] ?: null;
    $foto = $student['foto']; // Keep existing photo by default

    // Validate file photo if uploaded
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_name = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_ext = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png'];

        if (in_array($foto_ext, $allowed_ext)) {
            $foto = uniqid() . '.' . $foto_ext;
            $foto_path = dirname(__DIR__, 3) . '/uploads/' . $foto;
            if (move_uploaded_file($foto_tmp, $foto_path)) {
                // Delete old photo if exists
                if ($student['foto'] && file_exists(dirname(__DIR__, 3) . '/uploads/' . $student['foto'])) {
                    unlink(dirname(__DIR__, 3) . '/uploads/' . $student['foto']);
                }
            } else {
                $message = '<p class="text-red-600 dark:text-red-400 font-bold">Gagal mengunggah foto!</p>';
            }
        } else {
            $message = '<p class="text-red-600 dark:text-red-400 font-bold">Ekstensi file tidak diizinkan! Gunakan jpg, jpeg, atau png.</p>';
        }
    }

    // Check for duplicate NIS (excluding current student)
    if (!$message) {
        $check = $conn->prepare("SELECT * FROM siswa WHERE nis = ? AND id_siswa != ?");
        $check->bind_param("si", $nis, $id_siswa);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows > 0) {
            $message = '<p class="text-red-600 dark:text-red-400 font-bold">NIS sudah terdaftar!</p>';
        } else {
            // Update student data
            $stmt = $conn->prepare("UPDATE siswa SET nama_siswa = ?, nis = ?, jenis_kelamin = ?, tanggal_lahir = ?, alamat = ?, foto = ?, id_kelas = ? WHERE id_siswa = ?");
            $stmt->bind_param("ssssssii", $nama_siswa, $nis, $jenis_kelamin, $tanggal_lahir, $alamat, $foto, $id_kelas, $id_siswa);

            if ($stmt->execute()) {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Data siswa berhasil diperbarui!'
                ];
                echo "<script>window.location.href = 'index.php?page=siswa';</script>";
                exit;
            } else {
                $message = '<p class="text-red-600 dark:text-red-400 font-bold">Gagal memperbarui data: ' . $stmt->error . '</p>';
            }
            $stmt->close();
        }
        $check->close();
    }
}

// Fetch class options for dropdown
$kelas_query = mysqli_query($conn, "SELECT id_kelas, nama_kelas FROM kelas");
$kelas_options = [];
while ($kelas = mysqli_fetch_assoc($kelas_query)) {
    $kelas_options[] = $kelas;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa</title>
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
                    Edit Siswa
                </h1>
                <p class="text-gray-600 dark:text-gray-300 text-lg">
                    Perbarui data siswa dengan teliti
                </p>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto mt-4 rounded-full"></div>
            </div>

            <!-- Form Container -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 animate-slide-up border border-white/20">
                <form id="siswaForm" method="POST" action="" enctype="multipart/form-data" class="space-y-8">
                    <!-- Personal Info Section -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-bold">1</span>
                            </div>
                            Data Pribadi
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Siswa -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Nama Siswa *
                                </label>
                                <input 
                                    type="text" 
                                    name="nama_siswa"
                                    required
                                    value="<?= htmlspecialchars($student['nama_siswa']) ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500" 
                                    placeholder="Masukkan nama siswa"
                                >
                            </div>
                            
                            <!-- NIS -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    NIS *
                                </label>
                                <input 
                                    type="text" 
                                    name="nis"
                                    required
                                    value="<?= htmlspecialchars($student['nis']) ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500" 
                                    placeholder="Masukkan NIS"
                                >
                            </div>
                            
                            <!-- Jenis Kelamin -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Jenis Kelamin *
                                </label>
                                <select 
                                    name="jenis_kelamin"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500 cursor-pointer"
                                >
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="L" <?= $student['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= $student['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            
                            <!-- Tanggal Lahir -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Tanggal Lahir
                                </label>
                                <input 
                                    name="tanggal_lahir"
                                    type="date" 
                                    value="<?= htmlspecialchars($student['tanggal_lahir']) ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500" 
                                >
                            </div>
                        </div>
                        
                        <!-- Alamat -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Alamat
                            </label>
                            <textarea 
                                name="alamat"
                                rows="3"
                                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500 resize-none" 
                                placeholder="Masukkan alamat"
                            ><?= htmlspecialchars($student['alamat']) ?></textarea>
                        </div>
                    </div>

                    <!-- Class & Photo Section -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-bold">2</span>
                            </div>
                            Kelas & Foto
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kelas -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Kelas
                                </label>
                                <select 
                                    name="id_kelas"
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500 cursor-pointer"
                                >
                                    <option value="">Pilih kelas</option>
                                    <?php foreach ($kelas_options as $kelas): ?>
                                        <option value="<?= $kelas['id_kelas'] ?>" <?= $student['id_kelas'] == $kelas['id_kelas'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($kelas['nama_kelas']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Photo Upload -->
                        <div class="flex justify-center">
                            <div class="w-full max-w-md">
                                <div
                                    id="photo-dropzone"
                                    class="relative border-[3px] border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-8 text-center cursor-pointer group hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300 hover:bg-blue-50 dark:hover:bg-blue-900"
                                >
                                    <input id="file-upload" name="foto" type="file" accept="image/*" class="sr-only" />

                                    <div id="upload-content" class="space-y-4" <?= $student['foto'] ? 'style="display: none;"' : '' ?>>
                                        <div
                                            class="mx-auto w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300"
                                        >
                                            <svg
                                                class="w-8 h-8 text-white"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                ></path>
                                            </svg>
                                        </div>

                                        <div>
                                            <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Upload Foto Siswa</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Klik untuk pilih file atau drag & drop</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">PNG, JPG maksimal 10MB</p>
                                        </div>
                                    </div>

                                    <div id="preview-content" class="space-y-4" <?= $student['foto'] ? '' : 'style="display: none;"' ?>>
                                        <img
                                            id="preview-image"
                                            class="mx-auto w-32 h-32 rounded-xl object-cover shadow-lg"
                                            src="<?= $student['foto'] ? '../../Uploads/' . htmlspecialchars($student['foto']) : '' ?>"
                                            alt="Preview"
                                        />
                                        <p class="mt-4 text-sm text-green-600 dark:text-green-400 font-medium">✓ Foto tersedia</p>
                                        <button
                                            type="button"
                                            id="change-photo"
                                            class="mt-2 text-sm text-blue-600 dark:text-blue-400 hover:underline focus:outline-none"
                                        >
                                            Ganti foto
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-center pt-6 gap-4">
                        <button 
                            type="button" 
                            onclick="window.location.href='index.php?page=siswa'"
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
                                Simpan Perubahan
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

    <script>
        // File upload handling
        const fileInput = document.getElementById('file-upload');
        const dropzone = document.getElementById('photo-dropzone');
        const uploadContent = document.getElementById('upload-content');
        const previewContent = document.getElementById('preview-content');
        const previewImage = document.getElementById('preview-image');
        const changePhotoBtn = document.getElementById('change-photo');

        // Click to upload
        dropzone.addEventListener('click', () => {
            if (!previewContent.classList.contains('hidden')) return;
            fileInput.click();
        });

        // Change photo button
        changePhotoBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.click();
        });

        // File input change
        fileInput.addEventListener('change', handleFileSelect);

        // Drag and drop
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-blue-400', 'bg-blue-50/50');
        });

        dropzone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-400', 'bg-blue-50/50');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-400', 'bg-blue-50/50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        });

        function handleFileSelect() {
            const file = fileInput.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                    uploadContent.classList.add('hidden');
                    previewContent.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>