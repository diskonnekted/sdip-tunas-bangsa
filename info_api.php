<?php
require_once 'admin/includes/functions.php';
require_once 'admin/models/GeneralInfo.php';

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
        case 'view':
            if (!$id) {
                throw new Exception('ID tidak ditemukan');
            }
            
            $info = $generalInfo->getById($id);
            if (!$info || !$info['is_active']) {
                throw new Exception('Informasi tidak ditemukan atau tidak aktif');
            }
            
            // Check if expired
            if ($info['expiry_date'] && $generalInfo->isExpired($info['expiry_date'])) {
                throw new Exception('Informasi telah kedaluwarsa');
            }
            
            // Generate HTML content for view modal
            ob_start();
            ?>
            <div class="modal-info-content">
                <!-- Header Info -->
                <div class="modal-info-header" style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                            <i class="<?= $generalInfo->getTypeIcon($info['type']) ?>"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="color: var(--primary-color); margin: 0 0 5px 0; font-size: 1.3rem;"><?= htmlspecialchars($info['title']) ?></h3>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <span style="display: inline-flex; align-items: center; padding: 4px 12px; background: #dcfce7; color: #0277bd; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                    <?= $generalInfo->getTypeName($info['type']) ?>
                                </span>
                                <span style="display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 500; <?= $generalInfo->getPriorityBadgeClass($info['priority']) ?>">
                                    <?= $generalInfo->getPriorityName($info['priority']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="modal-info-body" style="margin-bottom: 25px;">
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid var(--primary-color);">
                        <div style="color: #374151; line-height: 1.7; font-size: 1rem; white-space: pre-wrap; text-align: justify;">
                            <?= nl2br(htmlspecialchars($info['content'])) ?>
                        </div>
                    </div>
                </div>

                <!-- Meta Information -->
                <div class="modal-info-meta">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <h4 style="margin: 0 0 10px 0; color: var(--primary-color); font-size: 0.9rem; font-weight: 600;">
                                <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i>Informasi Tanggal
                            </h4>
                            <div style="font-size: 0.85rem; color: #6b7280; line-height: 1.5;">
                                <div style="margin-bottom: 5px;">
                                    <strong>Dibuat:</strong> <?= formatTanggal($info['created_at']) ?>
                                </div>
                                <?php if ($info['updated_at'] && $info['updated_at'] !== $info['created_at']): ?>
                                <div style="margin-bottom: 5px;">
                                    <strong>Diperbarui:</strong> <?= formatTanggal($info['updated_at']) ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($info['expiry_date']): ?>
                                <div style="margin-bottom: 5px;">
                                    <strong>Berlaku sampai:</strong> 
                                    <span style="<?= $generalInfo->isExpired($info['expiry_date']) ? 'color: #dc2626; font-weight: 600;' : '' ?>">
                                        <?= formatTanggal($info['expiry_date']) ?>
                                    </span>
                                </div>
                                <?php else: ?>
                                <div style="margin-bottom: 5px;">
                                    <strong>Berlaku:</strong> Tidak terbatas
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($info['attachment']): ?>
                        <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <h4 style="margin: 0 0 10px 0; color: var(--primary-color); font-size: 0.9rem; font-weight: 600;">
                                <i class="fas fa-paperclip" style="margin-right: 8px;"></i>Lampiran
                            </h4>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 40px; height: 40px; background: #3b82f6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-file-alt" style="color: white; font-size: 1.1rem;"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-size: 0.85rem; color: #374151; font-weight: 500; margin-bottom: 3px;">
                                        <?= htmlspecialchars($info['attachment']) ?>
                                    </div>
                                    <a href="admin/uploads/attachments/<?= htmlspecialchars($info['attachment']) ?>" 
                                       target="_blank" 
                                       style="display: inline-flex; align-items: center; gap: 5px; font-size: 0.8rem; color: #3b82f6; text-decoration: none; font-weight: 500;">
                                        <i class="fas fa-download"></i>
                                        Download Lampiran
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <h4 style="margin: 0 0 10px 0; color: var(--primary-color); font-size: 0.9rem; font-weight: 600;">
                                <i class="fas fa-info-circle" style="margin-right: 8px;"></i>Keterangan
                            </h4>
                            <div style="font-size: 0.85rem; color: #6b7280;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                    Informasi ini tersedia untuk umum
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                                    <i class="fas fa-shield-alt" style="color: #f59e0b;"></i>
                                    Bagian dari komitmen transparansi sekolah
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Anti-Corruption Message -->
                <div style="background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%); padding: 15px; border-radius: 10px; border: 1px solid #22c55e; margin-top: 20px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 35px; height: 35px; background: linear-gradient(135deg, #22c55e, #16a34a); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-shield-alt" style="color: white; font-size: 1rem;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h5 style="margin: 0 0 5px 0; color: #14532d; font-size: 0.9rem; font-weight: 600;">Komitmen Transparansi</h5>
                            <p style="margin: 0; font-size: 0.8rem; color: #15803d; line-height: 1.4;">
                                Informasi ini dipublikasikan sebagai bagian dari komitmen SDIP Tunas Bangsa dalam mewujudkan transparansi, akuntabilitas, dan nilai-nilai integritas dalam pendidikan.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                    <div style="font-size: 0.8rem; color: #6b7280;">
                        <i class="fas fa-eye" style="margin-right: 5px;"></i>
                        Informasi ini dapat dibagikan kepada publik
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <?php if ($info['attachment']): ?>
                        <a href="admin/uploads/attachments/<?= htmlspecialchars($info['attachment']) ?>" 
                           target="_blank" 
                           style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-size: 0.85rem; font-weight: 500;">
                            <i class="fas fa-download"></i>
                            Download
                        </a>
                        <?php endif; ?>
                        <button onclick="closeModal()" 
                                style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 500; cursor: pointer;">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

            <style>
                @media (max-width: 768px) {
                    .modal-info-meta > div {
                        grid-template-columns: 1fr !important;
                    }
                    
                    .modal-info-header > div {
                        flex-direction: column !important;
                        align-items: flex-start !important;
                        gap: 10px !important;
                    }
                    
                    .modal-info-header > div > div:last-child {
                        width: 100%;
                    }
                    
                    .modal-info-header > div > div:last-child > div {
                        flex-wrap: wrap;
                        gap: 10px !important;
                    }
                }
            </style>
            <?php
            $html = ob_get_clean();
            
            echo json_encode([
                'success' => true,
                'info' => [
                    'id' => $info['id'],
                    'title' => $info['title'],
                    'type' => $info['type'],
                    'priority' => $info['priority']
                ],
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
