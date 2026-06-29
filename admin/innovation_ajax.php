<?php
require_once 'includes/functions.php';
require_once 'models/Innovation.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Initialize database
$database = new Database();
$db = $database->getConnection();
$innovation = new Innovation($db);

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
            
            $innovationData = $innovation->getById($id);
            if (!$innovationData) {
                throw new Exception('Inovasi tidak ditemukan');
            }
            
            echo json_encode([
                'success' => true,
                'innovation' => $innovationData
            ]);
            break;
            
        case 'view':
            if (!$id) {
                throw new Exception('ID tidak ditemukan');
            }
            
            $innovationData = $innovation->getById($id);
            if (!$innovationData) {
                throw new Exception('Inovasi tidak ditemukan');
            }
            
            // Generate HTML content for view modal
            ob_start();
            ?>
            <div class="innovation-detail">
                <!-- Header -->
                <div class="flex items-start gap-6 mb-6">
                    <?php if ($innovationData['image']): ?>
                    <div class="flex-shrink-0">
                        <img src="uploads/innovations/<?= htmlspecialchars($innovationData['image']) ?>" 
                             alt="<?= htmlspecialchars($innovationData['title']) ?>"
                             class="w-32 h-32 rounded-lg object-cover">
                    </div>
                    <?php endif; ?>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($innovationData['title']) ?></h3>
                            <?php if ($innovationData['is_featured']): ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-star mr-1"></i>Unggulan
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center gap-4 mb-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="<?= $innovation->getCategoryIcon($innovationData['category']) ?> mr-2"></i>
                                <?= $innovation->getCategoryName($innovationData['category']) ?>
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-calendar mr-1"></i>
                                Implementasi <?= $innovationData['implementation_year'] ?>
                            </span>
                            <?php if ($innovation->isRecent($innovationData['implementation_year'])): ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-circle mr-1"></i>Terbaru
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center gap-4 text-sm">
                            <span class="inline-flex items-center text-<?= $innovationData['is_active'] ? 'green' : 'red' ?>-600">
                                <i class="fas fa-circle mr-1 text-xs"></i>
                                <?= $innovationData['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                            <?php if ($innovationData['video_url']): ?>
                            <a href="<?= htmlspecialchars($innovationData['video_url']) ?>" target="_blank" 
                               class="inline-flex items-center text-green-600 hover:text-green-800">
                                <i class="fas fa-video mr-1"></i>Tonton Video
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Deskripsi</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($innovationData['description'])) ?></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Benefits -->
                    <?php 
                    $benefits = $innovation->formatJsonArray($innovationData['benefits']);
                    if (!empty($benefits)): 
                    ?>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-3">
                            <i class="fas fa-thumbs-up text-green-600 mr-2"></i>Manfaat
                        </h4>
                        <ul class="space-y-2">
                            <?php foreach ($benefits as $benefit): ?>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-700"><?= htmlspecialchars($benefit) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Features -->
                    <?php 
                    $features = $innovation->formatJsonArray($innovationData['features']);
                    if (!empty($features)): 
                    ?>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-3">
                            <i class="fas fa-cogs text-green-600 mr-2"></i>Fitur Utama
                        </h4>
                        <ul class="space-y-2">
                            <?php foreach ($features as $feature): ?>
                            <li class="flex items-start">
                                <i class="fas fa-star text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-700"><?= htmlspecialchars($feature) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Anti-Corruption Integration Message -->
                <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-shield-alt text-green-600 mt-1 mr-3"></i>
                        <div>
                            <h5 class="font-semibold text-green-900 mb-1">Komitmen Pendidikan Berintegritas</h5>
                            <p class="text-green-800 text-sm">
                                Inovasi ini merupakan bagian dari upaya SDIP Tunas Bangsa dalam menciptakan lingkungan 
                                pembelajaran yang transparan, akuntabel, dan mendukung pembentukan karakter siswa yang berintegritas.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Metadata -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">Dibuat:</span><br>
                            <?= formatTanggal($innovationData['created_at']) ?>
                        </div>
                        <?php if ($innovationData['updated_at'] && $innovationData['updated_at'] !== $innovationData['created_at']): ?>
                        <div>
                            <span class="font-medium">Diperbarui:</span><br>
                            <?= formatTanggal($innovationData['updated_at']) ?>
                        </div>
                        <?php endif; ?>
                        <div>
                            <span class="font-medium">Status:</span><br>
                            <?= $innovationData['is_active'] ? 'Aktif' : 'Nonaktif' ?> â€¢ 
                            <?= $innovationData['is_featured'] ? 'Unggulan' : 'Biasa' ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <style>
                .innovation-detail h4 {
                    color: #1f2937;
                }
                .innovation-detail .bg-gray-50 {
                    background-color: #f9fafb;
                }
                .innovation-detail ul li {
                    padding: 8px 0;
                }
                @media (max-width: 768px) {
                    .innovation-detail .flex-shrink-0 {
                        width: 100%;
                        margin-bottom: 1rem;
                    }
                    .innovation-detail .grid.grid-cols-1.lg\\:grid-cols-2 {
                        grid-template-columns: 1fr !important;
                    }
                }
            </style>
            <?php
            $html = ob_get_clean();
            
            echo json_encode([
                'success' => true,
                'html' => $html
            ]);
            break;
            
        case 'toggle_status':
            if (!$id) {
                throw new Exception('ID tidak ditemukan');
            }
            
            $type = $_GET['type'] ?? '';
            if ($type === 'featured') {
                $result = $innovation->toggleFeatured($id);
                $message = 'Status unggulan berhasil diubah!';
            } elseif ($type === 'active') {
                $result = $innovation->toggleActive($id);
                $message = 'Status aktif berhasil diubah!';
            } else {
                throw new Exception('Tipe toggle tidak valid');
            }
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                throw new Exception('Gagal mengubah status');
            }
            break;
            
        case 'quick_stats':
            $stats = $innovation->getStats();
            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
            break;
            
        case 'search':
            $keyword = $_GET['keyword'] ?? '';
            if (empty($keyword)) {
                throw new Exception('Keyword pencarian tidak ditemukan');
            }
            
            $results = $innovation->search($keyword, 10);
            echo json_encode([
                'success' => true,
                'results' => $results,
                'count' => count($results)
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
