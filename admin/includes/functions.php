<?php
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database config with correct path
if (file_exists(__DIR__ . '/../config/database.php')) {
    require_once __DIR__ . '/../config/database.php';
} else {
    require_once 'config/database.php';
}

// Fungsi untuk membuat slug dari string
function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', ' ', $string);
    $string = preg_replace('/\s/', '-', $string);
    return trim($string, '-');
}

// Fungsi untuk format tanggal Indonesia
function formatTanggal($date) {
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $hari = date('d', $timestamp);
    $bulan_idx = date('n', $timestamp);
    $tahun = date('Y', $timestamp);
    
    return $hari . ' ' . $bulan[$bulan_idx] . ' ' . $tahun;
}

// Fungsi untuk upload file dengan debugging yang lebih baik
function uploadFile($file, $target_dir = 'uploads/', $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']) {
    // Check if file was uploaded
    if (!isset($file) || !is_array($file)) {
        error_log('Upload error: No file array provided');
        return false;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log('Upload error: ' . $file['error']);
        return false;
    }
    
    // Check file size (max 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        error_log('Upload error: File exceeds 2MB limit. Size: ' . $file['size']);
        return false;
    }
    
    // Check file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_types)) {
        error_log('Upload error: Invalid file extension: ' . $file_extension);
        return false;
    }
    
    // Create unique filename
    $new_filename = 'news_' . uniqid() . '_' . time() . '.' . $file_extension;
    $target_path = $target_dir . $new_filename;
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0755, true)) {
            error_log('Upload error: Cannot create directory: ' . $target_dir);
            return false;
        }
    }
    
    // Check if directory is writable
    if (!is_writable($target_dir)) {
        error_log('Upload error: Directory not writable: ' . $target_dir);
        return false;
    }
    
    // Check if it's a valid uploaded file
    if (!is_uploaded_file($file['tmp_name'])) {
        error_log('Upload error: File is not a valid uploaded file: ' . $file['tmp_name']);
        return false;
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        error_log('Upload success: File uploaded to ' . $target_path);
        return $new_filename;
    } else {
        error_log('Upload error: move_uploaded_file failed from ' . $file['tmp_name'] . ' to ' . $target_path);
        return false;
    }
}

// Fungsi untuk validasi email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Fungsi untuk generate password hash
function generatePasswordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Fungsi untuk verifikasi password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Fungsi untuk cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Fungsi untuk redirect jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Fungsi untuk get current user info
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, username, email, full_name, role, last_login FROM admin_users WHERE id = ? AND is_active = 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fungsi untuk sanitize input
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }
}

// Fungsi untuk generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fungsi untuk validate CSRF token
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Fungsi untuk pagination
function paginate($total_records, $records_per_page, $current_page) {
    $total_pages = ceil($total_records / $records_per_page);
    $offset = ($current_page - 1) * $records_per_page;
    
    return [
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'offset' => $offset,
        'limit' => $records_per_page,
        'total_records' => $total_records
    ];
}

// Fungsi untuk format file size
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Fungsi untuk log activity
function logActivity($action, $description, $user_id = null) {
    if (!$user_id) {
        $user_id = $_SESSION['user_id'] ?? null;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent, created_at) 
              VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $user_id,
        $action,
        $description,
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
}

// Fungsi untuk alert messages
function setAlert($message, $type = 'success') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

function getAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        return $alert;
    }
    return null;
}

// Fungsi untuk time ago
if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'baru saja';
        if ($time < 3600) return floor($time/60) . ' menit lalu';
        if ($time < 86400) return floor($time/3600) . ' jam lalu';
        if ($time < 2592000) return floor($time/86400) . ' hari lalu';
        if ($time < 31536000) return floor($time/2592000) . ' bulan lalu';
        
        return floor($time/31536000) . ' tahun lalu';
    }
}
?>
