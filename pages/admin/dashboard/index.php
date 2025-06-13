<?php
// Contoh data statistik sederhana, biasanya dari DB
$totalSiswa = 120;
$totalGuru = 15;
$totalKelas = 8;
$totalMapel = 10;

// Data contoh untuk chart (bisa diganti pakai Chart.js nanti)
$bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
$nilaiRataRata = [75, 80, 78, 82, 85, 88];
?>

<h1 class="text-2xl font-semibold mb-6">Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-green-500 text-white p-5 rounded-lg shadow flex items-center">
        <i class="ri-group-fill text-4xl mr-4"></i>
        <div>
            <p class="text-sm uppercase font-semibold">Total Siswa</p>
            <p class="text-2xl font-bold"><?= $totalSiswa ?></p>
        </div>
    </div>
    <div class="bg-blue-500 text-white p-5 rounded-lg shadow flex items-center">
        <i class="ri-user-3-fill text-4xl mr-4"></i>
        <div>
            <p class="text-sm uppercase font-semibold">Total Guru</p>
            <p class="text-2xl font-bold"><?= $totalGuru ?></p>
        </div>
    </div>
    <div class="bg-yellow-400 text-white p-5 rounded-lg shadow flex items-center">
        <i class="ri-building-2-fill text-4xl mr-4"></i>
        <div>
            <p class="text-sm uppercase font-semibold">Total Kelas</p>
            <p class="text-2xl font-bold"><?= $totalKelas ?></p>
        </div>
    </div>
    <div class="bg-purple-600 text-white p-5 rounded-lg shadow flex items-center">
        <i class="ri-book-fill text-4xl mr-4"></i>
        <div>
            <p class="text-sm uppercase font-semibold">Total Mapel</p>
            <p class="text-2xl font-bold"><?= $totalMapel ?></p>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6">
    <h2 class="text-xl font-semibold mb-4">Rata-rata Nilai Siswa per Bulan</h2>
    <canvas id="nilaiChart" height="100"></canvas>
</div>

<!-- Daftar Siswa Terbaru (Contoh statis) -->
<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Siswa Terbaru</h2>
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-300 dark:border-gray-700">
                <th class="py-2 px-4">No</th>
                <th class="py-2 px-4">Nama</th>
                <th class="py-2 px-4">Kelas</th>
                <th class="py-2 px-4">Tanggal Daftar</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-gray-200 dark:border-gray-700">
                <td class="py-2 px-4">1</td>
                <td class="py-2 px-4">Rina Susanti</td>
                <td class="py-2 px-4">X IPA 1</td>
                <td class="py-2 px-4">2025-05-10</td>
            </tr>
            <tr class="border-b border-gray-200 dark:border-gray-700">
                <td class="py-2 px-4">2</td>
                <td class="py-2 px-4">Budi Santoso</td>
                <td class="py-2 px-4">XI IPS 2</td>
                <td class="py-2 px-4">2025-05-12</td>
            </tr>
            <tr class="border-b border-gray-200 dark:border-gray-700">
                <td class="py-2 px-4">3</td>
                <td class="py-2 px-4">Dewi Lestari</td>
                <td class="py-2 px-4">XII IPA 3</td>
                <td class="py-2 px-4">2025-05-15</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('nilaiChart').getContext('2d');
const nilaiChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($bulan) ?>,
        datasets: [{
            label: 'Nilai Rata-rata',
            data: <?= json_encode($nilaiRataRata) ?>,
            borderColor: 'rgba(59, 130, 246, 1)', // Tailwind blue-500
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            fill: true,
            tension: 0.3,
            pointRadius: 5,
            pointHoverRadius: 7,
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        },
        plugins: {
            legend: {
                labels: {
                    color: getComputedStyle(document.body).color
                }
            }
        }
    }
});
</script>
