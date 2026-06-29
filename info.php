<?php
// Include necessary files
include_once 'includes/settings.php';
require_once 'admin/includes/functions.php';
require_once 'admin/models/GeneralInfo.php';

// Get school info
$school_info = getSchoolInfo();
$contact_info = getContactInfo();

// Initialize database
$database = new Database();
$db = $database->getConnection();
$generalInfo = new GeneralInfo($db);

// Get filter parameters
$type_filter = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';

// Get general information data
try {
    // Get active information (not expired and active)
    $pengumuman = $generalInfo->getByType('pengumuman');
    $kalender = $generalInfo->getByType('kalender'); 
    $prosedur = $generalInfo->getByType('prosedur');
    $dokumen = $generalInfo->getByType('dokumen');
    
    // Get all active info for search/filter
    $all_info = $generalInfo->getActive(50, 0, $type_filter);
    
    // Apply search filter if exists
    if (!empty($search)) {
        $all_info = array_filter($all_info, function($info) use ($search) {
            return stripos($info['title'], $search) !== false || 
                   stripos($info['content'], $search) !== false;
        });
    }
    
} catch (Exception $e) {
    $pengumuman = [];
    $kalender = [];
    $prosedur = [];
    $dokumen = [];
    $all_info = [];
}
?>
<?php $page_title = 'Informasi Umum'; ?>
<?php include 'includes/header.php'; ?>
<style>
        .info-section {
            padding: 80px 0;
        }
        
        .info-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .info-header h2 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .info-header p {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .info-filters {
            background: #f8f9fa;
            padding: 30px 0;
            margin-bottom: 60px;
        }
        
        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            align-items: center;
        }
        
        .search-box {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .search-box input {
            padding: 12px 20px;
            border: 2px solid #ddd;
            border-radius: 50px;
            width: 300px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }
        
        .filter-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .filter-tab {
            padding: 10px 20px;
            background: white;
            border: 2px solid #ddd;
            border-radius: 25px;
            text-decoration: none;
            color: #666;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .filter-tab:hover,
        .filter-tab.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .info-category {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .info-category:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .category-header {
            padding: 25px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            text-align: center;
        }
        
        .category-header i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .category-header h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .category-header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .category-content {
            padding: 25px;
        }
        
        .info-item {
            border-bottom: 1px solid #eee;
            padding: 20px 0;
            transition: all 0.3s ease;
        }
        
        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .info-item:hover {
            background: #f8f9fa;
            margin: 0 -25px;
            padding-left: 25px;
            padding-right: 25px;
            border-radius: 8px;
        }
        
        .info-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .info-title:hover {
            color: var(--primary-color);
        }
        
        .info-preview {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        
        .info-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: #888;
        }
        
        .priority-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .priority-tinggi {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .priority-sedang {
            background: #fef3c7;
            color: #d97706;
        }
        
        .priority-rendah {
            background: #d1fae5;
            color: #059669;
        }
        
        .attachment-icon {
            color: var(--primary-color);
            margin-left: 10px;
        }
        
        .no-info {
            text-align: center;
            padding: 40px;
            color: #888;
            font-style: italic;
        }
        
        .all-info-section {
            margin-top: 80px;
            padding-top: 80px;
            border-top: 1px solid #eee;
        }
        
        .info-search-results {
            margin-bottom: 30px;
        }
        
        .search-result-item {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .search-result-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .result-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .result-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.1rem;
            color: white;
        }
        
        .type-pengumuman { background: #3b82f6; }
        .type-kalender { background: #10b981; }
        .type-prosedur { background: #8b5cf6; }
        .type-dokumen { background: #f59e0b; }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        
        .modal-content {
            position: relative;
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #888;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .modal-close:hover {
            background: #f1f5f9;
            color: var(--primary-color);
        }
        
        .modal-body {
            line-height: 1.6;
        }
        
        .modal-body h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .modal-body p {
            margin-bottom: 15px;
            text-align: justify;
        }
        
        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box input {
                width: 100%;
            }
            
            .filter-tabs {
                justify-content: center;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .modal-content {
                margin: 10% 20px;
                padding: 20px;
            }
        }
</style>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <h1>Informasi Umum</h1>
                <p>Berbagai informasi penting seputar <?php echo htmlspecialchars($school_info['name']); ?> dengan nilai-nilai integritas dan transparansi</p>
                <nav class="breadcrumb">
                    <a href="index.php">Beranda</a>
                    <span>/</span>
                    <span>Info</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Filters -->
    <section class="info-filters">
        <div class="container">
            <div class="filter-container">
                <form method="GET" class="search-box">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($type_filter) ?>">
                    <input type="text" name="search" placeholder="Cari informasi..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <div class="filter-tabs">
                    <a href="info.php" class="filter-tab <?= empty($type_filter) ? 'active' : '' ?>">
                        <i class="fas fa-th-large"></i> Semua
                    </a>
                    <a href="info.php?type=pengumuman" class="filter-tab <?= $type_filter === 'pengumuman' ? 'active' : '' ?>">
                        <i class="fas fa-bullhorn"></i> Pengumuman
                    </a>
                    <a href="info.php?type=kalender" class="filter-tab <?= $type_filter === 'kalender' ? 'active' : '' ?>">
                        <i class="fas fa-calendar-alt"></i> Kalender
                    </a>
                    <a href="info.php?type=prosedur" class="filter-tab <?= $type_filter === 'prosedur' ? 'active' : '' ?>">
                        <i class="fas fa-list-ol"></i> Prosedur
                    </a>
                    <a href="info.php?type=dokumen" class="filter-tab <?= $type_filter === 'dokumen' ? 'active' : '' ?>">
                        <i class="fas fa-file-alt"></i> Dokumen
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="info-section">
        <div class="container">
            <?php if (!empty($search) || !empty($type_filter)): ?>
            <!-- Search/Filter Results -->
            <div class="info-search-results">
                <h2>
                    <?php if (!empty($search)): ?>
                    Hasil pencarian "<?= htmlspecialchars($search) ?>"
                    <?php elseif (!empty($type_filter)): ?>
                    <?= $generalInfo->getTypeName($type_filter) ?>
                    <?php endif ?>
                    <span style="color: #888; font-size: 0.8em;">(<?= count($all_info) ?> ditemukan)</span>
                </h2>
                
                <?php if (empty($all_info)): ?>
                <div class="no-info">
                    <i class="fas fa-info-circle" style="font-size: 3rem; margin-bottom: 20px; color: #ddd;"></i>
                    <p>Tidak ada informasi yang ditemukan.</p>
                    <a href="info.php" class="btn btn-primary" style="margin-top: 20px;">Lihat Semua Informasi</a>
                </div>
                <?php else: ?>
                <?php foreach ($all_info as $info): ?>
                <div class="search-result-item">
                    <div class="result-header">
                        <div class="result-icon type-<?= $info['type'] ?>">
                            <i class="<?= $generalInfo->getTypeIcon($info['type']) ?>"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 class="info-title" onclick="openModal(<?= $info['id'] ?>)">
                                <?= htmlspecialchars($info['title']) ?>
                                <span style="font-size: 0.8em; color: #666;">
                                    <?php if ($info['attachment']): ?>
                                    <i class="fas fa-paperclip attachment-icon"></i>
                                    <?php endif; ?>
                                    <i class="fas fa-eye" style="margin-left: 10px;"></i>
                                </span>
                            </h3>
                            <div class="info-meta">
                                <span><?= $generalInfo->getTypeName($info['type']) ?></span>
                                <span class="priority-badge priority-<?= $info['priority'] ?>">
                                    <?= $generalInfo->getPriorityName($info['priority']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="info-preview">
                        <?= htmlspecialchars(substr($info['content'], 0, 200)) ?>...
                    </div>
                    <div class="info-meta">
                        <span><i class="fas fa-calendar"></i> <?= formatTanggal($info['created_at']) ?></span>
                        <?php if ($info['expiry_date']): ?>
                        <span><i class="fas fa-clock"></i> Berlaku s/d <?= formatTanggal($info['expiry_date']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <!-- Category Grid -->
            <div class="info-header">
                <h2>Informasi Umum SDIP Tunas Bangsa</h2>
                <p>Temukan berbagai informasi penting tentang kegiatan, prosedur, dan dokumen sekolah yang mendukung pendidikan berintegritas</p>
            </div>
            
            <div class="info-grid">
                <!-- Pengumuman -->
                <div class="info-category">
                    <div class="category-header">
                        <i class="fas fa-bullhorn"></i>
                        <h3>Pengumuman</h3>
                        <p>Informasi terkini dan pengumuman penting sekolah</p>
                    </div>
                    <div class="category-content">
                        <?php if (empty($pengumuman)): ?>
                        <div class="no-info">
                            <p>Belum ada pengumuman terbaru</p>
                        </div>
                        <?php else: ?>
                        <?php foreach (array_slice($pengumuman, 0, 3) as $info): ?>
                        <div class="info-item">
                            <div class="info-title" onclick="openModal(<?= $info['id'] ?>)">
                                <?= htmlspecialchars($info['title']) ?>
                                <span>
                                    <?php if ($info['attachment']): ?>
                                    <i class="fas fa-paperclip attachment-icon"></i>
                                    <?php endif; ?>
                                    <i class="fas fa-eye" style="color: var(--primary-color);"></i>
                                </span>
                            </div>
                            <div class="info-preview">
                                <?= htmlspecialchars(substr($info['content'], 0, 150)) ?>...
                            </div>
                            <div class="info-meta">
                                <span><i class="fas fa-calendar"></i> <?= formatTanggal($info['created_at']) ?></span>
                                <span class="priority-badge priority-<?= $info['priority'] ?>">
                                    <?= $generalInfo->getPriorityName($info['priority']) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (count($pengumuman) > 3): ?>
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="info.php?type=pengumuman" class="btn btn-primary">Lihat Semua Pengumuman</a>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Kalender Akademik -->
                <div class="info-category">
                    <div class="category-header" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-calendar-alt"></i>
                        <h3>Kalender Akademik</h3>
                        <p>Jadwal kegiatan dan kalendar pendidikan</p>
                    </div>
                    <div class="category-content">
                        <?php if (empty($kalender)): ?>
                        <div class="no-info">
                            <p>Kalender akademik akan segera tersedia</p>
                        </div>
                        <?php else: ?>
                        <?php foreach (array_slice($kalender, 0, 3) as $info): ?>
                        <div class="info-item">
                            <div class="info-title" onclick="openModal(<?= $info['id'] ?>)">
                                <?= htmlspecialchars($info['title']) ?>
                                <span>
                                    <?php if ($info['attachment']): ?>
                                    <i class="fas fa-paperclip attachment-icon"></i>
                                    <?php endif; ?>
                                    <i class="fas fa-eye" style="color: #10b981;"></i>
                                </span>
                            </div>
                            <div class="info-preview">
                                <?= htmlspecialchars(substr($info['content'], 0, 150)) ?>...
                            </div>
                            <div class="info-meta">
                                <span><i class="fas fa-calendar"></i> <?= formatTanggal($info['created_at']) ?></span>
                                <span class="priority-badge priority-<?= $info['priority'] ?>">
                                    <?= $generalInfo->getPriorityName($info['priority']) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (count($kalender) > 3): ?>
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="info.php?type=kalender" class="btn btn-primary">Lihat Semua Kalender</a>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Prosedur & SOP -->
                <div class="info-category">
                    <div class="category-header" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <i class="fas fa-list-ol"></i>
                        <h3>Prosedur & SOP</h3>
                        <p>Panduan dan prosedur operasional sekolah</p>
                    </div>
                    <div class="category-content">
                        <?php if (empty($prosedur)): ?>
                        <div class="no-info">
                            <p>Dokumen prosedur sedang disiapkan</p>
                        </div>
                        <?php else: ?>
                        <?php foreach (array_slice($prosedur, 0, 3) as $info): ?>
                        <div class="info-item">
                            <div class="info-title" onclick="openModal(<?= $info['id'] ?>)">
                                <?= htmlspecialchars($info['title']) ?>
                                <span>
                                    <?php if ($info['attachment']): ?>
                                    <i class="fas fa-paperclip attachment-icon"></i>
                                    <?php endif; ?>
                                    <i class="fas fa-eye" style="color: #8b5cf6;"></i>
                                </span>
                            </div>
                            <div class="info-preview">
                                <?= htmlspecialchars(substr($info['content'], 0, 150)) ?>...
                            </div>
                            <div class="info-meta">
                                <span><i class="fas fa-calendar"></i> <?= formatTanggal($info['created_at']) ?></span>
                                <span class="priority-badge priority-<?= $info['priority'] ?>">
                                    <?= $generalInfo->getPriorityName($info['priority']) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (count($prosedur) > 3): ?>
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="info.php?type=prosedur" class="btn btn-primary">Lihat Semua Prosedur</a>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Dokumen Penting -->
                <div class="info-category">
                    <div class="category-header" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-file-alt"></i>
                        <h3>Dokumen Penting</h3>
                        <p>Dokumen resmi dan berkas penting sekolah</p>
                    </div>
                    <div class="category-content">
                        <?php if (empty($dokumen)): ?>
                        <div class="no-info">
                            <p>Dokumen akan segera dipublikasikan</p>
                        </div>
                        <?php else: ?>
                        <?php foreach (array_slice($dokumen, 0, 3) as $info): ?>
                        <div class="info-item">
                            <div class="info-title" onclick="openModal(<?= $info['id'] ?>)">
                                <?= htmlspecialchars($info['title']) ?>
                                <span>
                                    <?php if ($info['attachment']): ?>
                                    <i class="fas fa-paperclip attachment-icon"></i>
                                    <?php endif; ?>
                                    <i class="fas fa-eye" style="color: #f59e0b;"></i>
                                </span>
                            </div>
                            <div class="info-preview">
                                <?= htmlspecialchars(substr($info['content'], 0, 150)) ?>...
                            </div>
                            <div class="info-meta">
                                <span><i class="fas fa-calendar"></i> <?= formatTanggal($info['created_at']) ?></span>
                                <span class="priority-badge priority-<?= $info['priority'] ?>">
                                    <?= $generalInfo->getPriorityName($info['priority']) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (count($dokumen) > 3): ?>
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="info.php?type=dokumen" class="btn btn-primary">Lihat Semua Dokumen</a>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Modal -->
    <div id="infoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Detail Informasi</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script>
        // Modal functionality
        function openModal(id) {
            fetch(`info_api.php?action=view&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalTitle').innerHTML = data.info.title;
                        document.getElementById('modalBody').innerHTML = data.html;
                        document.getElementById('infoModal').style.display = 'block';
                        document.body.style.overflow = 'hidden';
                    } else {
                        alert('Gagal memuat informasi');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memuat informasi');
                });
        }

        function closeModal() {
            document.getElementById('infoModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('infoModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>
