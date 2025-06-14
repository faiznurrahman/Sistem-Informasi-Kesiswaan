<?php
// Required variables:
// $columns = ['Nama Kolom', 'Kolom Lain', ...];
// $rows = array of associative arrays for table data
// $actions = ['edit' => true, 'delete' => true, 'view' => true]

if (!isset($columns) || !isset($rows)) {
    echo "<p class='text-red-500'>Template membutuhkan \$columns dan \$rows.</p>";
    return;
}

// Kolom yang boleh mengandung HTML langsung (tanpa htmlspecialchars)
$rawHtmlColumns = ['foto']; // tambah 'deskripsi' atau kolom lain jika perlu
?>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contoh Tabel</title>
  <link rel="stylesheet" href="../dist/css/style.css" />
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
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }
    
    .table-header {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        border-bottom: 2px solid #e2e8f0;
    }
    
    html.dark .table-header {
        background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
        border-bottom-color: #374151;
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
    
    .table-row {
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.2s ease;
    }
    
    html.dark .table-row {
        border-bottom-color: #374151;
    }
    
    .table-row:hover {
        background-color: #fafbfc;
    }
    
    html.dark .table-row:hover {
        background-color: #374151;
    }
    
    .table-row td {
        padding: 0.875rem 0.75rem;
        color: #4b5563;
        font-size: 0.875rem;
    }
    
    html.dark .table-row td {
        color: #d1d5db;
    }
    
    .table-row:nth-child(even) {
        background-color: rgba(248, 250, 252, 0.5);
    }
    
    html.dark .table-row:nth-child(even) {
        background-color: rgba(31, 41, 55, 0.3);
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    
    /* Styling untuk kolom foto */
    .table-row td img {
        max-width: 80px;
        max-height: 80px;
        object-fit: cover;
        border-radius: 8px;
        display: block;
        margin: 0 auto;
    }
    
    /* DataTables styling enhancement */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0.5rem;
        background: white;
        color: #374151;
    }
    
    html.dark .dataTables_wrapper .dataTables_length select,
    html.dark .dataTables_wrapper .dataTables_filter input {
        border-color: #374151;
        background: #1f2937;
        color: #d1d5db;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        margin: 0 2px;
        padding: 0.5rem 0.75rem;
        background: white;
        color: #374151;
    }
    
    html.dark .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-color: #374151;
        background: #1f2937;
        color: #d1d5db;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        color: white;
        border-color: #4f46e5;
    }
    
    .dataTables_wrapper .dataTables_info {
        color: #6b7280;
    }
    
    html.dark .dataTables_wrapper .dataTables_info {
        color: #9ca3af;
    }
  </style>
</head>

<div class="table-container shadow-md rounded-xl p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Data Tabel</h2>
        <?php if ($showAddButton ?? true): ?>
            <a href="?page=<?= htmlspecialchars($page ?? basename($_SERVER['PHP_SELF'], '.php')) ?>&action=tambah" class="btn-primary hover:bg-green-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium">
                <i class="ri-add-line mr-2"></i>Tambah Data
            </a>
        <?php endif; ?>
    </div>
    <div class="overflow-x-auto">
        <table id="datatable" class="min-w-full text-sm">
            <thead class="table-header">
                <tr>
                    <th class="text-center w-16">No</th>
                    <?php foreach ($columns as $col): ?>
                        <th><?= htmlspecialchars($col) ?></th>
                    <?php endforeach; ?>
                    <?php if (array_filter($actions ?? [])): ?>
                        <th class="text-center w-32">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $index => $row): ?>
                    <tr class="table-row">
                        <td class="text-center font-medium"><?= $index + 1 ?></td>
                        <?php foreach ($columns as $key => $label): ?>
                            <td>
                                <?= in_array($key, $rawHtmlColumns) 
                                    ? ($row[$key] ?? '') 
                                    : htmlspecialchars($row[$key] ?? '') ?>
                            </td>
                        <?php endforeach; ?>
                        <?php if (array_filter($actions ?? [])): ?>
                            <td class="action-buttons justify-center">
                                <?php 
                                $id = htmlspecialchars($row['id'] ?? ''); 
                                include __DIR__ . '/actions.php'; 
                                ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    if (window.jQuery && $.fn.DataTable) {
        $('#datatable').DataTable({
            language: {
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                search: "Cari:",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            pageLength: 10,
            responsive: true,
            dom: '<"flex flex-col md:flex-row md:justify-between md:items-center mb-4"<"mb-2 md:mb-0"l><"mb-2 md:mb-0"f>>rt<"flex flex-col md:flex-row md:justify-between md:items-center mt-4"<"mb-2 md:mb-0"i><"mb-2 md:mb-0"p>>'
        });
    } else {
        console.warn("DataTables belum dimuat!");
    }
});
</script>