<?php
require_once 'includes/functions.php';
require_once 'models/GeneralInfo.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Initialize database
$database = new Database();
$db = $database->getConnection();
$generalInfo = new GeneralInfo($db);

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// Set content type to JSON
header('Content-Type: application/json');

try {
    switch ($action) {
        case 'get':
            if (!$id) {
                throw new Exception('ID tidak ditemukan');
            }
            
            $info = $generalInfo->getById($id);
            if (!$info) {
                throw new Exception('Informasi tidak ditemukan');
            }
            
            echo json_encode([
                'success' => true,
                'info' => $info
            ]);
            break;
            
        case 'view':
            if (!$id) {
                throw new Exception('ID tidak ditemukan');
            }
            
            $info = $generalInfo->getById($id);
            if (!$info) {
                throw new Exception('Informasi tidak ditemukan');
            }
            
            // Generate HTML content for view modal
            ob_start();
            ?>
            <div class="space-y-4">
                <!-- Header -->
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <i class="<?= $generalInfo->getTypeIcon($info['type']) ?> text-gray-600 text-lg"></i>
                            <h4 class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($info['title']) ?></h4>
                        </div>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <?= $generalInfo->getTypeName($info['type']) ?>
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $generalInfo->getPriorityBadgeClass($info['priority']) ?>">
                                <?= $generalInfo->getPriorityName($info['priority']) ?>
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $info['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $info['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h5 class="font-medium text-gray-900 mb-2">Konten:</h5>
                    <div class="text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($info['content']) ?></div>
                </div>

                <!-- Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h5 class="font-medium text-gray-900 mb-2">Informasi Tanggal:</h5>
                        <div class="space-y-1 text-sm text-gray-600">
                            <div>Dibuat: <?= formatTanggal($info['created_at']) ?></div>
                            <?php if ($info['updated_at'] && $info['updated_at'] !== $info['created_at']): ?>
                            <div>Diperbarui: <?= formatTanggal($info['updated_at']) ?></div>
                            <?php endif; ?>
                            <?php if ($info['expiry_date']): ?>
                            <div class="<?= $generalInfo->isExpired($info['expiry_date']) ? 'text-red-600 font-medium' : '' ?>">
                                Berlaku sampai: <?= formatTanggal($info['expiry_date']) ?>
                                <?php if ($generalInfo->isExpired($info['expiry_date'])): ?>
                                <span class="text-red-600">(Kedaluwarsa)</span>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div>Berlaku: Tidak terbatas</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($info['attachment']): ?>
                    <div>
                        <h5 class="font-medium text-gray-900 mb-2">Lampiran:</h5>
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-paperclip text-green-600"></i>
                                <span class="text-sm text-gray-700"><?= htmlspecialchars($info['attachment']) ?></span>
                            </div>
                            <div>
                                <a href="uploads/attachments/<?= htmlspecialchars($info['attachment']) ?>" 
                                   target="_blank" 
                                   class="inline-flex items-center text-sm text-green-600 hover:text-green-800">
                                    <i class="fas fa-download mr-1"></i>
                                    Download Lampiran
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Status Warnings -->
                <?php if (!$info['is_active']): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                        <p class="text-yellow-800 text-sm">
                            <strong>Perhatian:</strong> Informasi ini dalam status nonaktif dan tidak akan ditampilkan di website.
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($info['expiry_date'] && $generalInfo->isExpired($info['expiry_date'])): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                        <p class="text-red-800 text-sm">
                            <strong>Informasi Kedaluwarsa:</strong> Informasi ini telah melewati tanggal kedaluwarsa dan tidak akan ditampilkan di website.
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php
            $html = ob_get_clean();
            
            echo json_encode([
                'success' => true,
                'html' => $html
            ]);
            break;
            
        default:
            throw new Exception('Aksi tidak valid');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
