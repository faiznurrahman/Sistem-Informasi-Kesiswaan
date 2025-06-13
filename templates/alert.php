<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Function untuk generate alert HTML
 */
function showAlert($message, $type = 'info', $dismissible = true) {
    $alertId = 'alert-' . uniqid();
    
    // Konfigurasi styling berdasarkan tipe
    $configs = [
        'success' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-200',
            'text' => 'text-green-800',
            'icon' => '✓',
            'icon_bg' => 'bg-green-100',
            'icon_text' => 'text-green-600'
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'text' => 'text-red-800',
            'icon' => '✕',
            'icon_bg' => 'bg-red-100',
            'icon_text' => 'text-red-600'
        ],
        'warning' => [
            'bg' => 'bg-yellow-50',
            'border' => 'border-yellow-200',
            'text' => 'text-yellow-800',
            'icon' => '⚠',
            'icon_bg' => 'bg-yellow-100',
            'icon_text' => 'text-yellow-600'
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'text' => 'text-blue-800',
            'icon' => 'ℹ',
            'icon_bg' => 'bg-blue-100',
            'icon_text' => 'text-blue-600'
        ]
    ];
    
    $config = $configs[$type] ?? $configs['info'];
    
    $dismissButton = $dismissible ? 
        '<button onclick="closeAlert(\''.$alertId.'\')" class="ml-auto -mx-1.5 -my-1.5 '.$config['bg'].' '.$config['text'].' rounded-lg focus:ring-2 focus:ring-'.$config['icon_text'].' p-1.5 hover:bg-opacity-75 inline-flex h-8 w-8 items-center justify-center transition-colors duration-200" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>' : '';
    
    echo '
    <div id="'.$alertId.'" class="flex items-center p-4 mb-4 text-sm '.$config['text'].' border '.$config['border'].' rounded-lg '.$config['bg'].' shadow-sm animate-fade-in" role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 '.$config['icon_text'].' '.$config['icon_bg'].' rounded-lg mr-3">
            <span class="text-lg font-bold">'.$config['icon'].'</span>
        </div>
        <div class="flex-1 font-medium">
            '.htmlspecialchars($message, ENT_QUOTES, 'UTF-8').'
        </div>
        '.$dismissButton.'
    </div>';
}

// Cek dan tampilkan alert berdasarkan session
$alertShown = false;

// Alert Success
if (isset($_SESSION['success'])) {
    showAlert($_SESSION['success'], 'success');
    unset($_SESSION['success']);
    $alertShown = true;
}

// Alert Error
if (isset($_SESSION['error'])) {
    showAlert($_SESSION['error'], 'error');
    unset($_SESSION['error']);
    $alertShown = true;
}

// Alert Warning
if (isset($_SESSION['warning'])) {
    showAlert($_SESSION['warning'], 'warning');
    unset($_SESSION['warning']);
    $alertShown = true;
}

// Alert Info
if (isset($_SESSION['info'])) {
    showAlert($_SESSION['info'], 'info');
    unset($_SESSION['info']);
    $alertShown = true;
}

// Tampilkan JavaScript hanya jika ada alert yang ditampilkan
if ($alertShown) {
?>
<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>

<script>
function closeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.transition = "all 0.3s ease-out";
        alert.style.opacity = "0";
        alert.style.transform = "translateY(-10px)";
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 300);
    }
}

// Auto close alerts setelah 5 detik
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll("[id^='alert-']");
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert && alert.parentNode) {
                closeAlert(alert.id);
            }
        }, 3000);
    });
});
</script>
<?php
}
?>