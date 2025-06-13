<?php
$id = $id ?? 0;
$page = $page ?? '';
$actions = $actions ?? [];

$url_view = "?page=$page&action=lihat&id=$id";
$url_edit = "?page=$page&action=edit&id=$id";
$url_delete = "?page=$page&action=delete&id=$id";  // ganti 'hapus' jadi 'delete'


$modal_id = "modalConfirmDelete_$id";
?>

<div class="flex gap-2">
  <?php if ($actions['view'] ?? false): ?>
    <!-- Tombol Lihat -->
    <a href="<?= $url_view ?>" class="flex items-center gap-1 text-blue-600 hover:text-blue-800 px-2 py-1 rounded hover:bg-blue-100" title="Lihat">
      <i class="ri-eye-line text-lg"></i><span class="hidden sm:inline">Lihat</span>
    </a>
  <?php endif; ?>

  <?php if ($actions['edit'] ?? false): ?>
    <!-- Tombol Edit -->
    <a href="<?= $url_edit ?>" class="flex items-center gap-1 text-yellow-600 hover:text-yellow-800 px-2 py-1 rounded hover:bg-yellow-100" title="Edit">
      <i class="ri-edit-line text-lg"></i><span class="hidden sm:inline">Edit</span>
    </a>
  <?php endif; ?>

  <?php if ($actions['delete'] ?? false): ?>
    <!-- Tombol Hapus -->
    <button onclick="document.getElementById('<?= $modal_id ?>').classList.remove('hidden')" 
            class="flex items-center gap-1 text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-100" 
            title="Hapus" type="button">
      <i class="ri-delete-bin-6-line text-lg"></i><span class="hidden sm:inline">Hapus</span>
    </button>
  <?php endif; ?>
</div>

<?php if ($actions['delete'] ?? false): ?>
  <!-- Modal Konfirmasi -->
  <div id="<?= $modal_id ?>" 
       class="fixed inset-0 z-50 flex items-center justify-center bg-transparent bg-opacity-40 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-sm">
      <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Konfirmasi Hapus</h2>
      <p class="text-gray-600 dark:text-gray-300 mb-4">Yakin ingin menghapus data ini?</p>
      <div class="flex justify-end gap-3">
        <button onclick="document.getElementById('<?= $modal_id ?>').classList.add('hidden')" 
                class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded">
          Tidak
        </button>
        <a href="<?= $url_delete ?>" 
           class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded">
          Ya
        </a>
      </div>
    </div>
  </div>
<?php endif; ?>
