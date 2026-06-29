<?php
require_once 'admin/includes/functions.php';
require_once 'admin/models/Innovation.php';

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
        case 'view':
            if (!$id) {
                throw new Exception('ID tidak ditemukan');
            }
            
            $innovationData = $innovation->getById($id);
            if (!$innovationData || !$innovationData['is_active']) {
                throw new Exception('Inovasi tidak ditemukan atau tidak aktif');
            }
            
            // Generate HTML content for view modal
            ob_start();
            ?>
            <div class="innovation-frontend-detail">
                <!-- Header -->
                <div class="innovation-header" style="display: flex; gap: 20px; margin-bottom: 24px; align-items: start;">
                    <?php if ($innovationData['image']): ?>
                    <div style="flex-shrink: 0;">
                        <img src="admin/uploads/<?= htmlspecialchars($innovationData['image']) ?>" 
                             alt="<?= htmlspecialchars($innovationData['title']) ?>"
                             style="width: 140px; height: 140px; border-radius: 12px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                    </div>
                    <?php endif; ?>
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <h3 style="margin: 0; color: #1f2937; font-size: 1.5rem; font-weight: 700;"><?= htmlspecialchars($innovationData['title']) ?></h3>
                            <?php if ($innovationData['is_featured']): ?>
                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #fef3c7; color: #a16207; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
                                <i class="fas fa-star"></i> Unggulan
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px; flex-wrap: wrap;">
                            <span style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; background: #dbeafe; color: #1d4ed8; border-radius: 999px; font-size: 0.85rem; font-weight: 600;">
                                <i class="<?= $innovation->getCategoryIcon($innovationData['category']) ?>"></i>
                                <?= $innovation->getCategoryName($innovationData['category']) ?>
                            </span>
                            <span style="color: #6b7280; font-size: 0.9rem;">
                                <i class="fas fa-calendar" style="margin-right: 6px;"></i>
                                Implementasi <?= $innovationData['implementation_year'] ?>
                            </span>
                            <?php if ($innovation->isRecent($innovationData['implementation_year'])): ?>
                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #dcfce7; color: #15803d; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i> Terbaru
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($innovationData['video_url']): ?>
                        <div style="margin-bottom: 12px;">
                            <a href="<?= htmlspecialchars($innovationData['video_url']) ?>" target="_blank" 
                               style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; background: #dc2626; color: white; text-decoration: none; border-radius: 8px; font-size: 0.9rem; font-weight: 500;">
                                <i class="fas fa-play"></i> Tonton Video
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Description -->
                <div style="margin-bottom: 28px;">
                    <h4 style="color: #1f2937; font-size: 1.1rem; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-align-left" style="color: #22c55e;"></i> Deskripsi Inovasi
                    </h4>
                    <div style="background: #f9fafb; border-radius: 10px; padding: 20px; border-left: 4px solid #22c55e;">
                        <p style="color: #374151; line-height: 1.7; margin: 0; font-size: 1rem;"><?= nl2br(htmlspecialchars($innovationData['description'])) ?></p>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px;">
                    <!-- Benefits -->
                    <?php 
                    $benefits = $innovation->formatJsonArray($innovationData['benefits']);
                    if (!empty($benefits)): 
                    ?>
                    <div>
                        <h4 style="color: #1f2937; font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-thumbs-up" style="color: #10b981;"></i> Manfaat
                        </h4>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <?php foreach ($benefits as $benefit): ?>
                            <li style="display: flex; align-items: start; margin-bottom: 12px; padding: 12px; background: #f0fdf4; border-radius: 8px; border-left: 3px solid #10b981;">
                                <i class="fas fa-check-circle" style="color: #10b981; margin-right: 12px; margin-top: 2px; flex-shrink: 0;"></i>
                                <span style="color: #374151; line-height: 1.5;"><?= htmlspecialchars($benefit) ?></span>
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
                        <h4 style="color: #1f2937; font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-cogs" style="color: #3b82f6;"></i> Fitur Utama
                        </h4>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <?php foreach ($features as $feature): ?>
                            <li style="display: flex; align-items: start; margin-bottom: 12px; padding: 12px; background: #f0fdf4; border-radius: 8px; border-left: 3px solid #3b82f6;">
                                <i class="fas fa-star" style="color: #3b82f6; margin-right: 12px; margin-top: 2px; flex-shrink: 0;"></i>
                                <span style="color: #374151; line-height: 1.5;"><?= htmlspecialchars($feature) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Anti-Corruption Integration -->
                <div style="background: linear-gradient(135deg, #dcfce7, #f0fdf4); border: 1px solid #22c55e; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: start; gap: 16px;">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #22c55e, #16a34a); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-shield-alt" style="color: white; font-size: 1.1rem;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h5 style="margin: 0 0 8px 0; color: #14532d; font-size: 1rem; font-weight: 600;">Pendidikan Berintegritas</h5>
                            <p style="margin: 0; color: #15803d; line-height: 1.6; font-size: 0.95rem;">
                                Inovasi ini merupakan bagian integral dari komitmen SDIP Tunas Bangsa dalam menciptakan ekosistem pendidikan yang 
                                transparan, akuntabel, dan berintegritas. Setiap implementasi inovasi didokumentasikan secara terbuka untuk 
                                memastikan akuntabilitas dan mendukung pembentukan karakter siswa yang jujur dan bertanggung jawab.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Implementation Info -->
                <div style="background: #f8fafc; border-radius: 10px; padding: 20px; border-top: 3px solid #22c55e;">
                    <h4 style="color: #1f2937; font-size: 1rem; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-info-circle" style="color: #22c55e;"></i> Informasi Implementasi
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 0.9rem;">
                        <div>
                            <span style="color: #6b7280; font-weight: 500;">Tahun Implementasi:</span><br>
                            <span style="color: #374151; font-weight: 600;"><?= $innovationData['implementation_year'] ?></span>
                        </div>
                        <div>
                            <span style="color: #6b7280; font-weight: 500;">Kategori:</span><br>
                            <span style="color: #374151; font-weight: 600;"><?= $innovation->getCategoryName($innovationData['category']) ?></span>
                        </div>
                        <div>
                            <span style="color: #6b7280; font-weight: 500;">Status:</span><br>
                            <span style="color: #15803d; font-weight: 600;">
                                <i class="fas fa-check-circle" style="margin-right: 4px;"></i>Aktif & Beroperasi
                            </span>
                        </div>
                        <?php if ($innovation->isRecent($innovationData['implementation_year'])): ?>
                        <div>
                            <span style="color: #6b7280; font-weight: 500;">Klasifikasi:</span><br>
                            <span style="color: #059669; font-weight: 600;">
                                <i class="fas fa-circle" style="margin-right: 4px; font-size: 0.6rem;"></i>Inovasi Terbaru
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <style>
                @media (max-width: 768px) {
                    .innovation-header {
                        flex-direction: column !important;
                    }
                    .innovation-frontend-detail > div:nth-child(3) {
                        grid-template-columns: 1fr !important;
                    }
                    .innovation-frontend-detail .innovation-header > div:first-child {
                        align-self: center;
                    }
                }
            </style>
            <?php
            $html = ob_get_clean();
            
            echo json_encode([
                'success' => true,
                'innovation' => [
                    'id' => $innovationData['id'],
                    'title' => $innovationData['title'],
                    'category' => $innovationData['category']
                ],
                'html' => $html
            ]);
            break;
            
        case 'featured':
            $featured = $innovation->getFeatured(6);
            echo json_encode([
                'success' => true,
                'featured' => $featured,
                'count' => count($featured)
            ]);
            break;
            
        case 'search':
            $keyword = $_GET['keyword'] ?? '';
            if (empty($keyword)) {
                throw new Exception('Keyword pencarian tidak ditemukan');
            }
            
            $results = $innovation->search($keyword, 20);
            $activeResults = array_filter($results, function($item) {
                return $item['is_active'] == 1;
            });
            
            echo json_encode([
                'success' => true,
                'results' => array_values($activeResults),
                'count' => count($activeResults)
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
