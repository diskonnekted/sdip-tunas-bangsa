<?php
session_start();
require_once '../admin/config/database.php';
require_once '../admin/models/User.php';

// If already logged in
if (isset($_SESSION['parent_user_id']) && isset($_SESSION['parent_role']) && $_SESSION['parent_role'] === 'orang_tua') {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    $username = $_POST['no_hp'] ?? '';
    $password = $_POST['nisn'] ?? '';

    if (!empty($username) && !empty($password)) {
        // Authenticate as normal user but only allow 'orang_tua' role
        $query = "SELECT id, username, password, full_name, role, is_active FROM admin_users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['role'] === 'orang_tua') {
                if ($user['is_active'] == 1) {
                    $_SESSION['parent_user_id'] = $user['id'];
                    $_SESSION['parent_username'] = $user['username'];
                    $_SESSION['parent_full_name'] = $user['full_name'];
                    $_SESSION['parent_role'] = $user['role'];
                    
                    // Update last login
                    $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
                    
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Akun Anda dinonaktifkan. Hubungi admin.';
                }
            } else {
                $error = 'Akun ini bukan akun Wali Murid.';
            }
        } else {
            $error = 'No. HP atau NISN salah.';
        }
    } else {
        $error = 'Silakan masukkan No. HP dan NISN.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login Wali - 7KAIH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="theme-color" content="#dc2626">
    <style>
        /* Mobile app styling */
        body { background-color: #f3f4f6; overscroll-behavior-y: none; }
        .app-container { max-width: 480px; margin: 0 auto; min-height: 100vh; background-color: white; box-shadow: 0 0 20px rgba(0,0,0,0.05); position: relative; }
    </style>
</head>
<body class="antialiased">
    <div class="app-container flex flex-col justify-center px-6 py-12">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-red-600 text-white rounded-full mb-4 shadow-lg">
                <i class="fas fa-hands-holding-child text-3xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Portal 7KAIH</h1>
            <p class="text-sm text-gray-500 mt-2">7 Kebiasaan Anak Indonesia Hebat</p>
        </div>

        <?php if ($error): ?>
        <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm mb-6 flex items-center border border-red-100">
            <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">No. Handphone (WhatsApp)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-phone text-gray-400"></i>
                    </div>
                    <input type="tel" name="no_hp" required placeholder="Contoh: 08123456789"
                           class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">NISN Siswa (Password)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-id-card text-gray-400"></i>
                    </div>
                    <input type="password" name="nisn" required placeholder="Masukkan NISN Siswa"
                           class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                </div>
                <p class="text-xs text-gray-400 mt-2 text-center">Gunakan NISN anak Anda sebagai password awal.</p>
            </div>

            <button type="submit" class="w-full py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-200 transition-all active:scale-95 flex justify-center items-center">
                <i class="fas fa-sign-in-alt mr-2"></i> Masuk Dasbor
            </button>
        </form>
        
        <div class="mt-10 text-center text-xs text-gray-400">
            &copy; <?= date('Y') ?> SDIP Tunas Bangsa
        </div>
    </div>
</body>
</html>
