<?php
require_once 'includes/header.php';
require_once '../admin/config/database.php';
require_once '../admin/models/ParentModel.php';

$db = new Database();
$conn = $db->getConnection();
$parentModel = new ParentModel($conn);
$parentData = $parentModel->getByUserId($_SESSION['parent_user_id']);

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<div class="mb-4">
    <h2 class="text-xl font-bold text-gray-900">Profil Anda</h2>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="p-6 text-center border-b border-gray-100">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 text-gray-400 rounded-full mb-4">
            <i class="fas fa-user text-3xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($_SESSION['parent_full_name']) ?></h3>
        <p class="text-sm text-gray-500">Wali Murid</p>
    </div>
    
    <div class="p-2">
        <?php if($parentData): ?>
        <div class="flex items-center p-4 border-b border-gray-50">
            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-600 mr-4">
                <i class="fas fa-id-card"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">NIK</p>
                <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($parentData['nik'] ?: '-') ?></p>
            </div>
        </div>
        <div class="flex items-center p-4 border-b border-gray-50">
            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-600 mr-4">
                <i class="fas fa-phone"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">No. HP Ayah</p>
                <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($parentData['no_hp_ayah'] ?: '-') ?></p>
            </div>
        </div>
        <div class="flex items-center p-4 border-b border-gray-50">
            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-600 mr-4">
                <i class="fas fa-phone"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">No. HP Ibu</p>
                <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($parentData['no_hp_ibu'] ?: '-') ?></p>
            </div>
        </div>
        <div class="flex items-center p-4">
            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-600 mr-4">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Alamat</p>
                <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($parentData['alamat_lengkap'] ?: '-') ?></p>
            </div>
        </div>
        <?php else: ?>
            <p class="text-center text-gray-500 p-4">Data lengkap belum diisi.</p>
        <?php endif; ?>
    </div>
</div>

<a href="?logout=true" onclick="return confirm('Apakah Anda yakin ingin keluar?')" class="w-full flex items-center justify-center py-4 bg-white border border-gray-200 text-red-600 font-bold rounded-xl shadow-sm hover:bg-gray-50 transition-colors">
    <i class="fas fa-sign-out-alt mr-2"></i> Keluar (Logout)
</a>

<?php require_once 'includes/footer.php'; ?>
