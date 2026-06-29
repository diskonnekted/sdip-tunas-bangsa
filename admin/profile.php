<?php
$page_title = 'Profil Saya';
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'includes/functions.php';

// Require login
Auth::requireLogin();

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

$current_user_id = $_SESSION['user_id'];
$user = $userModel->getById($current_user_id);

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $username = sanitizeInput($_POST['username']);
        $email = sanitizeInput($_POST['email']);
        $full_name = sanitizeInput($_POST['full_name']);
        
        // Basic validation
        if (empty($username) || empty($email) || empty($full_name)) {
            $errors[] = 'Semua kolom wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }
        
        if (empty($errors)) {
            // Keep role and status the same for current user
            $result = $userModel->update($current_user_id, $username, $email, $full_name, $user['role'], $user['is_active']);
            if ($result['success']) {
                $success_message = 'Profil berhasil diperbarui.';
                // Refresh data
                $user = $userModel->getById($current_user_id);
                // Update session
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
            } else {
                $errors[] = $result['message'];
            }
        }
    } elseif (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $errors[] = 'Semua kolom password wajib diisi.';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        } elseif (strlen($new_password) < 6) {
            $errors[] = 'Password baru minimal 6 karakter.';
        } else {
            // Verify current password first (User model doesn't have verifyPasswordById, we can authenticate)
            $authCheck = $userModel->authenticate($user['username'], $current_password);
            if (!$authCheck['success']) {
                $errors[] = 'Password saat ini salah.';
            } else {
                $result = $userModel->updatePassword($current_user_id, $new_password);
                if ($result['success']) {
                    $success_message = 'Password berhasil diperbarui.';
                } else {
                    $errors[] = $result['message'];
                }
            }
        }
    }
}
?>
<?php include 'includes/admin_header.php'; ?>

<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Profil Saya</h2>
        <p class="text-gray-600 mt-1">Kelola informasi profil dan keamanan akun Anda</p>
    </div>

    <!-- Alerts -->
    <?php if (!empty($errors)): ?>
    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
    <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg">
        <i class="fas fa-check-circle mr-2"></i> <?= htmlspecialchars($success_message) ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Informasi Profil -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Informasi Dasar</h3>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 p-2 border">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 p-2 border">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 p-2 border">
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ubah Password -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Ubah Password</h3>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="update_password" value="1">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                        <input type="password" name="current_password" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 p-2 border">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password Baru</label>
                        <input type="password" name="new_password" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 p-2 border">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 p-2 border">
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded transition">
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Status Akun</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Peran:</span>
                        <span class="font-medium text-gray-900 px-2 py-1 bg-primary-50 text-primary-700 rounded text-xs uppercase">
                            <?= htmlspecialchars($user['role']) ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status:</span>
                        <span class="font-medium <?= $user['is_active'] ? 'text-green-600' : 'text-red-600' ?>">
                            <?= $user['is_active'] ? 'Aktif' : 'Non-Aktif' ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Terakhir Login:</span>
                        <span class="font-medium text-gray-900">
                            <?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Belum pernah' ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Terdaftar Sejak:</span>
                        <span class="font-medium text-gray-900">
                            <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
