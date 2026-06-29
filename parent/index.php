<?php
require_once 'includes/header.php';
require_once '../admin/config/database.php';
require_once '../admin/models/ParentModel.php';

$db = new Database();
$conn = $db->getConnection();
$parentModel = new ParentModel($conn);

$parentData = $parentModel->getByUserId($_SESSION['parent_user_id']);
$children = [];
if ($parentData) {
    $children = $parentModel->getChildren($parentData['id']);
}
?>

<div class="mb-4">
    <h2 class="text-xl font-bold text-gray-900">Daftar Anak</h2>
    <p class="text-sm text-gray-500">Pilih anak untuk mengisi jurnal 7KAIH hari ini.</p>
</div>

<?php if (empty($children)): ?>
<div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100 mt-6">
    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 text-gray-400 rounded-full mb-4">
        <i class="fas fa-child text-2xl"></i>
    </div>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Data Anak</h3>
    <p class="text-sm text-gray-500">Hubungi pihak sekolah (Admin) untuk menyambungkan akun Anda dengan data anak Anda.</p>
</div>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($children as $child): ?>
        <a href="jurnal.php?siswa_id=<?= $child['id'] ?>" class="block bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative overflow-hidden transition-all active:scale-[0.98]">
            <div class="absolute top-0 right-0 w-16 h-16 bg-red-50 rounded-bl-full flex items-start justify-end p-3">
                <i class="fas fa-chevron-right text-red-400 text-sm"></i>
            </div>
            
            <div class="flex items-center mb-3">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 mr-4 shrink-0">
                    <i class="fas <?= $child['jenis_kelamin'] == 'L' ? 'fa-male' : 'fa-female' ?> text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 leading-tight"><?= htmlspecialchars($child['nama_lengkap']) ?></h3>
                    <p class="text-xs text-gray-500">NISN: <?= htmlspecialchars($child['nisn']) ?></p>
                </div>
            </div>
            
            <div class="flex justify-between items-center border-t border-gray-50 pt-3 mt-1">
                <span class="inline-block px-3 py-1 bg-red-100 text-red-800 text-[10px] font-bold rounded-full">
                    Kelas <?= htmlspecialchars($child['kelas'] ?: '-') ?>
                </span>
                <span class="text-xs text-gray-500 font-medium">
                    Status: <?= htmlspecialchars($child['status_hubungan']) ?>
                </span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
