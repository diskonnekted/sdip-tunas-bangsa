<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Cek autentikasi dan peran
Auth::requireLogin();
if (!Auth::canEditContent()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Akses ditolak']);
    exit;
}

// Pastikan request adalah POST dan ada file yang diunggah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // Cek batas 2MB
    if ($file['size'] > 2 * 1024 * 1024) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Ukuran file melebihi batas 2MB.']);
        exit;
    }

    // Gunakan fungsi uploadFile bawaan
    // Kita arahkan target directory ke folder uploads/ (atau buat subfolder uploads/content/)
    $uploaded_filename = uploadFile($file, 'uploads/', ['jpg', 'jpeg', 'png', 'gif']);
    
    if ($uploaded_filename) {
        // Kembalikan format JSON yang diminta oleh TinyMCE
        echo json_encode([
            'location' => 'uploads/' . $uploaded_filename
        ]);
        exit;
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Gagal mengunggah file. Pastikan format valid (JPG/PNG/GIF).']);
        exit;
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Tidak ada file yang dikirim']);
    exit;
}
