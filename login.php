<?php
session_start();
include_once 'includes/db.php'; // atau 'config/db.php' tergantung lokasimu
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="src/output.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <title>Masuk</title>
</head>
<body class="dark:bg-gray-900 dark:text-white text-gray-800 font-inter">
    <div class="flex flex-col items-center justify-center min-h-screen px-4">
        <div class="w-800 max-w-md p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="flex justify-center mb-2">
                <img src="dist/img/logo.png" alt="" width="50px" height="50px" class="rounded object-cover">
            </div>

            <h2 class="text-2xl font-bold text-center mb-6">Sistem Informasi <br> Siswa dan Akademik</h2>

            <!-- Alert box muncul di sini -->
            <?php if (isset($_SESSION['alert'])): ?>
                <div class="mb-4 px-4 py-3 rounded relative text-white text-sm
                    <?php
                        echo match ($_SESSION['alert']['type']) {
                            'success' => 'bg-green-600',
                            'danger'  => 'bg-red-600',
                            'warning' => 'bg-yellow-500',
                            default   => 'bg-blue-600'
                        };
                    ?>"
                    role="alert">
                    <?php echo htmlspecialchars($_SESSION['alert']['message']); ?>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>

            <form action="process/login_process.php" method="POST">
                <div class="mb-4">
                    <label for="nip" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIP</label>
                    <input
                        type="text"
                        id="nip"
                        name="nip"
                        required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm
                        focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm
                        focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                </div>
                <select
                    name="kategori"
                    id="kategori"
                    class="mt-1 mb-3 block w-full p-2 border border-gray-300 rounded-md shadow-sm
                    focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                >
                    <option value="admin">Admin</option>
                    <option value="guru">Guru</option>
                </select>
                <button
                    type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700
                    focus:outline-none focus:ring focus:ring-blue-200"
                >
                    Login
                </button>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="dist/js/script.js"></script>
</body>
</html>
