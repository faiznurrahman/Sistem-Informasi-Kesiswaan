<?php
global $conn;

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Cek NIP duplikat
    $check = mysqli_query($conn, "SELECT * FROM pengguna WHERE nip='$nip'");
    if (mysqli_num_rows($check) > 0) {
        $message = '<p class="text-red-600 dark:text-red-400 font-bold">NIP sudah terdaftar!</p>';
    } else {
        $stmt = $conn->prepare("INSERT INTO pengguna (nip, nama, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nip, $nama, $password, $role);

        if ($stmt->execute()) {
            // Redirect pake JS setelah sukses simpan
            $_SESSION['success'] = 'User berhasil ditambahkan!';
            echo "<script>
                window.location.href = 'index.php?page=user';
            </script>";
            exit; // stop eksekusi PHP supaya redirect langsung jalan
        } else {
            $message = '<p class="text-red-600 dark:text-red-400 font-bold">Gagal menyimpan data: ' . $stmt->error . '</p>';
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Form User</title>
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
        <h1 class="text-4xl font-bold mb-2">Form User</h1>
        <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto mt-4 rounded-full"></div>
      </div>

      <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 animate-slide-up border border-white/20">
        <form id="teacherForm" method="POST" action="" class="space-y-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block mb-2 font-semibold">Nama Lengkap *</label>
              <input name="nama" required placeholder="Masukkan nama" class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600" />
            </div>

            <div>
              <label class="block mb-2 font-semibold">NIP *</label>
              <input name="nip" required placeholder="Masukkan NIP" class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600" />
            </div>

            <div>
              <label class="block mb-2 font-semibold">Password *</label>
              <input name="password" type="password" required placeholder="Masukkan password" class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600" />
            </div>

            <div>
              <label class="block mb-2 font-semibold">Role *</label>
              <select name="role" required class="w-full px-4 py-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600">
                <option value="">Pilih role</option>
                <option value="admin">Admin</option>
                <option value="guru">Guru</option>
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
