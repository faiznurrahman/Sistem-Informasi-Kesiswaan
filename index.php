<?php
session_start();
include __DIR__ . '/includes/db.php';
$page = $_GET['page'] ?? '';

if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}


$id_login = $_SESSION['id_pengguna'] ?? null;
$dataLogin = [];

if ($id_login) {
    $query = mysqli_query($conn, "
        SELECT p.nama, p.role, g.foto 
        FROM pengguna p
        LEFT JOIN guru g ON p.id_pengguna = g.id_guru
        WHERE p.id_pengguna = '$id_login'
    ");
    $dataLogin = mysqli_fetch_assoc($query);
}

$nama = $dataLogin['nama'] ?? 'Pengguna';
$foto = !empty($dataLogin['foto']) ? 'uploads/' . $dataLogin['foto'] : 'dist/img/blankprofil.jpg';

$page = $_GET['page'] ?? 'dashboard';
$masterDataPages = ['jadwal','guru','kelas','siswa','mapel','user'];
$selected = in_array($page, $masterDataPages) ? 'selected' : '';
$nilaiSelected = $page === 'nilai' ? 'selected' : '';
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="../dist/css/style.css" />
    <title>Dashboard</title>
    <style>
        body {
            background-color: #fff;
            color: #1f2937;
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        html.dark body {
            background-color: #111827;
            color: #fff;
        }
        .content-box {
            background-color: #fff;
            color: #1f2937;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1rem;
            min-height: 400px;
            overflow-y: auto;
            transition: background-color 0.3s, color 0.3s;
        }
        html.dark .content-box {
            background-color: #1f2937;
            color: #fff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="fixed left-0 top-0 w-64 h-full bg-gray-900 p-4 z-50 sidebar-menu transition-transform">
    <a href="#" class="flex items-center pb-4 border-b border-b-gray-800">
        <img src="../dist/img/logo.png" alt="" class="w-10 h-10 rounded object-cover" />
        <span class="text-lg font-bold text-white ml-3">SISWA</span>
    </a>

    <?php if ($_SESSION['role'] === 'admin') : ?>
        <!-- Sidebar untuk ADMIN -->
        <ul class="mt-4">
            <li class="mb-1 group <?= $page == 'dashboard' ? 'active' : '' ?>">
                <a href="?page=dashboard" class="flex items-center py-2 px-4 text-gray-300 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white">
                    <i class="ri-home-2-line mr-3 text-lg"></i>
                    <span class="text-sm">Dashboard</span>
                </a>
            </li>

            <li class="mb-1 group <?= $selected ?>">
                <a href="#" class="flex items-center py-2 px-4 text-gray-300 hover:bg-gray-950 hover:text-gray-100 rounded-md sidebar-dropdown-toggle group-[.selected]:bg-gray-950 group-[.selected]:text-gray-100">
                    <i class="ri-health-book-fill mr-3 text-lg"></i>
                    <span class="text-sm">Master Data</span>
                    <i class="ri-arrow-right-s-line ml-auto group-[.selected]:rotate-90"></i>
                </a>
                <ul class="pl-7 mt-2 <?= $selected ? 'block' : 'hidden' ?> group-[.selected]:block list-none">
                    <li class="mb-4 flex">
                        <i class="ri-calendar-2-line mr-3"></i>
                        <a href="?page=jadwal" class="text-sm flex items-center hover:text-gray-100 <?= $page == 'jadwal' ? 'text-gray-100 font-semibold bg-gray-800' : 'text-gray-300' ?>">Data Jadwal</a>
                    </li> 
                    <li class="mb-4 flex">
                        <i class="ri-group-fill mr-3"></i>
                        <a href="?page=guru" class="text-sm flex items-center hover:text-gray-100 <?= $page == 'guru' ? 'text-gray-100 font-semibold bg-gray-800' : 'text-gray-300' ?>">Data Guru</a>
                    </li> 
                    <li class="mb-4 flex">
                        <i class="ri-home-8-line mr-3"></i>
                        <a href="?page=kelas" class="text-sm flex items-center hover:text-gray-100 <?= $page == 'kelas' ? 'text-gray-100 font-semibold bg-gray-800' : 'text-gray-300' ?>">Data Kelas</a>
                    </li> 
                    <li class="mb-4 flex">
                        <i class="ri-group-fill mr-3"></i>
                        <a href="?page=siswa" class="text-sm flex items-center hover:text-gray-100 <?= $page == 'siswa' ? 'text-gray-100 font-semibold bg-gray-800' : 'text-gray-300' ?>">Data Siswa</a>
                    </li> 
                    <li class="mb-4 flex">
                        <i class="ri-book-read-fill mr-3"></i>
                        <a href="?page=mapel" class="text-sm flex items-center hover:text-gray-100 <?= $page == 'mapel' ? 'text-gray-100 font-semibold bg-gray-800' : 'text-gray-300' ?>">Data Mapel</a>
                    </li> 
                    <li class="mb-4 flex">
                        <i class="ri-user-add-fill mr-3"></i>
                        <a href="?page=user" class="text-sm flex items-center hover:text-gray-100 <?= $page == 'user' ? 'text-gray-100 font-semibold bg-gray-800' : 'text-gray-300' ?>">Data User</a>
                    </li> 
                </ul>
            </li>

        </ul>
    
    <?php elseif ($_SESSION['role'] === 'guru') : ?>
        <!-- Sidebar untuk GURU -->
        <ul class="mt-4">
            <li class="mb-1 group <?= $page == 'dashboard' ? 'active' : '' ?>">
                <a href="?page=dashboard" class="flex items-center py-2 px-4 text-gray-300 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white">
                    <i class="ri-home-2-line mr-3 text-lg"></i>
                    <span class="text-sm">Dashboard</span>
                </a>
            </li>

            <li class="mb-1 group <?= $page == 'jadwal' ? 'active' : '' ?>">
                <a href="?page=jadwal" class="flex items-center py-2 px-4 text-gray-300 hover:bg-gray-950 hover:text-white rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white">
                    <i class="ri-calendar-2-line mr-3 text-lg"></i>
                    <span class="text-sm">Jadwal Mengajar</span>
                </a>
            </li>

            <li class="mb-1 group <?= $page == 'nilai' ? 'active' : '' ?>">
                <a href="?page=nilai" class="flex items-center py-2 px-4 text-gray-300 hover:bg-gray-950 hover:text-white rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white">
                    <i class="ri-bar-chart-fill mr-3 text-lg"></i>
                    <span class="text-sm">Input Nilai</span>
                </a>
            </li>

            <li class="mb-1 group <?= $page == 'siswa' ? 'active' : '' ?>">
                <a href="?page=siswa" class="flex items-center py-2 px-4 text-gray-300 hover:bg-gray-950 hover:text-white rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white">
                    <i class="ri-group-line mr-3 text-lg"></i>
                    <span class="text-sm">Data Siswa</span>
                </a>
            </li>

        </ul>
    <?php endif; ?>

    <div class="dark:bg-gray-900 dark:text-white fixed top-0 left-0 w-full h-full bg-black/50 z-40 md:hidden sidebar-overlay"></div>
</div>


<main class="w-full md:w-[calc(100%-256px)] md:ml-64 bg-gray-50 dark:bg-gray-900 min-h-screen transition-all main">
    <!-- Navbar -->
    <div class="py-2 px-6 bg-white dark:bg-gray-900 flex items-center shadow-md sticky top-0 z-30">
        <button type="button" class="text-lg text-gray-600 sidebar-toggle">
            <i class="ri-menu-line"></i>
        </button>
        <ul class="flex items-center text-sm ml-4">
            <li class="mr-2">
                <a href="?page=dashboard" class="text-gray-400 hover:text-gray-600 font-medium">Dashboard</a>
            </li>
            <?php if ($page !== 'dashboard'): ?>
                <?php if (in_array($page, $masterDataPages)): ?>
                    <li class="mr-2 text-gray-600">/ Master Data</li>
                <?php endif; ?>
                <li class="text-gray-600">/ <?= ucfirst($page) ?></li>
            <?php endif; ?>
        </ul>

        <ul class="ml-auto flex items-center">
    <button id="toggleDarkMode" class="flex items-center gap-2 px-4 py-2 text-gray-900 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-green-500 dark:hover:text-green-400 transition-all duration-300">
        <i id="themeIcon" class="ri-sun-line text-lg"></i>
    </button>
    <h3 class="ml-4"><?= $nama ?></h3>
    <li class="dropdown ml-3">
        <button class="dropdown-toggle rounded-full flex items-center">
            <img src="<?= $foto ?>" alt="Foto Profil" class="w-8 h-8 rounded-full object-cover" />
        </button>
        <ul class="dropdown-menu shadow-md hidden py-1.5 rounded-md bg-white border border-gray-100 w-full max-w-[140px]">
            <li><a href="#" class="text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Profile</a></li>
            <li><a href="#" class="text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Settings</a></li>
            <li><a href="logout.php" class="text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Logout</a></li>
        </ul>
    </li>
</ul>

    </div>

    <!-- Konten Dinamis -->
    <div class="mt-4 max-w-full px-6">
        <div class="content-box">
            <?php
            $role = $_SESSION['role'] ?? '';
            $pageFile = __DIR__ . "/pages/{$role}/{$page}/index.php";

            if (file_exists($pageFile)) {
                include $pageFile;
            } else {
                echo "<p class='text-red-500'>Halaman tidak ditemukan.</p>";
            }

            ?>
        </div>
    </div>
</main>

<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../dist/js/script.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
</body>
</html>
