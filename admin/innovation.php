<?php
$page_title = 'Inovasi Pembelajaran';
require_once 'includes/functions.php';
require_once 'models/Innovation.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();
$innovation = new Innovation($db);

// Handle actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $errors = $innovation->validate($_POST);
                if (empty($errors)) {
                    // Handle image upload
                    $image_filename = '';
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = 'uploads/innovations/';
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        if (in_array($_FILES['image']['type'], $allowedTypes) && $_FILES['image']['size'] <= 5000000) {
                            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                            $filename = 'innovation_' . time() . '_' . uniqid() . '.' . $extension;
                            $uploadPath = $uploadDir . $filename;
                            
                            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                                $image_filename = $filename;
                            } else {
                                $errors[] = 'Gagal mengupload file gambar';
                            }
                        } else {
                            $errors[] = 'Format gambar tidak valid atau ukuran terlalu besar (max 5MB)';
                        }
                    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                        // Handle upload errors
                        $uploadErrors = [
                            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize)',
                            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE)',
                            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
                            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ada',
                            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
                            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP'
                        ];
                        $errors[] = 'Error upload: ' . ($uploadErrors[$_FILES['image']['error']] ?? 'Error tidak dikenal');
                    }
                    
                    $innovation->title = $_POST['title'];
                    $innovation->description = $_POST['description'];
                    $innovation->category = $_POST['category'];
                    $innovation->implementation_year = (int)$_POST['implementation_year'];
                    $innovation->benefits = !empty($_POST['benefits']) ? json_encode(array_filter(explode("\n", trim($_POST['benefits'])))) : null;
                    $innovation->features = !empty($_POST['features']) ? json_encode(array_filter(explode("\n", trim($_POST['features'])))) : null;
                    $innovation->image = $image_filename;
                    $innovation->video_url = $_POST['video_url'] ?? null;
                    $innovation->is_featured = isset($_POST['is_featured']) ? 1 : 0;
                    $innovation->is_active = isset($_POST['is_active']) ? 1 : 0;
                    
                    if ($innovation->create()) {
                        setAlert('Inovasi berhasil ditambahkan!', 'success');
                        header('Location: innovation.php');
                        exit;
                    } else {
                        setAlert('Gagal menambahkan inovasi!', 'error');
                    }
                } else {
                    setAlert(implode('<br>', $errors), 'error');
                }
                break;
                
            case 'update':
                $errors = $innovation->validate($_POST);
                if (empty($errors)) {
                    // Get existing data
                    $existingData = $innovation->getById($_POST['id']);
                    $image_filename = $existingData['image'];
                    
                    // Handle image upload
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = 'uploads/innovations/';
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        if (in_array($_FILES['image']['type'], $allowedTypes) && $_FILES['image']['size'] <= 5000000) {
                            // Delete old image
                            if ($image_filename && file_exists($uploadDir . $image_filename)) {
                                unlink($uploadDir . $image_filename);
                            }
                            
                            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                            $filename = 'innovation_' . time() . '_' . uniqid() . '.' . $extension;
                            $uploadPath = $uploadDir . $filename;
                            
                            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                                $image_filename = $filename;
                            } else {
                                $errors[] = 'Gagal mengupload file gambar';
                            }
                        } else {
                            $errors[] = 'Format gambar tidak valid atau ukuran terlalu besar (max 5MB)';
                        }
                    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                        // Handle upload errors
                        $uploadErrors = [
                            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize)',
                            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE)',
                            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
                            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ada',
                            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
                            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP'
                        ];
                        $errors[] = 'Error upload: ' . ($uploadErrors[$_FILES['image']['error']] ?? 'Error tidak dikenal');
                    }
                    
                    $innovation->id = $_POST['id'];
                    $innovation->title = $_POST['title'];
                    $innovation->description = $_POST['description'];
                    $innovation->category = $_POST['category'];
                    $innovation->implementation_year = (int)$_POST['implementation_year'];
                    $innovation->benefits = !empty($_POST['benefits']) ? json_encode(array_filter(explode("\n", trim($_POST['benefits'])))) : null;
                    $innovation->features = !empty($_POST['features']) ? json_encode(array_filter(explode("\n", trim($_POST['features'])))) : null;
                    $innovation->image = $image_filename;
                    $innovation->video_url = $_POST['video_url'] ?? null;
                    $innovation->is_featured = isset($_POST['is_featured']) ? 1 : 0;
                    $innovation->is_active = isset($_POST['is_active']) ? 1 : 0;
                    
                    if ($innovation->update()) {
                        setAlert('Inovasi berhasil diperbarui!', 'success');
                        header('Location: innovation.php');
                        exit;
                    } else {
                        setAlert('Gagal memperbarui inovasi!', 'error');
                    }
                } else {
                    setAlert(implode('<br>', $errors), 'error');
                }
                break;
        }
    }
}

// Handle delete action
if ($action === 'delete' && $id) {
    $innovationData = $innovation->getById($id);
    if ($innovationData) {
        $innovation->id = $id;
        $innovation->image = $innovationData['image'];
        
        if ($innovation->delete()) {
            setAlert('Inovasi berhasil dihapus!', 'success');
        } else {
            setAlert('Gagal menghapus inovasi!', 'error');
        }
    }
    header('Location: innovation.php');
    exit;
}

// Handle toggle actions
if ($action === 'toggle_featured' && $id) {
    if ($innovation->toggleFeatured($id)) {
        setAlert('Status unggulan berhasil diubah!', 'success');
    } else {
        setAlert('Gagal mengubah status unggulan!', 'error');
    }
    header('Location: innovation.php');
    exit;
}

if ($action === 'toggle_active' && $id) {
    if ($innovation->toggleActive($id)) {
        setAlert('Status aktif berhasil diubah!', 'success');
    } else {
        setAlert('Gagal mengubah status aktif!', 'error');
    }
    header('Location: innovation.php');
    exit;
}

// Get filters and pagination
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$year_filter = $_GET['year'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Get data
$innovations = $innovation->getAll($limit, $offset, $search, $category_filter, $year_filter);
$total_records = $innovation->count($search, $category_filter, $year_filter);
$total_pages = ceil($total_records / $limit);

// Get statistics and available years
$stats = $innovation->getStats();
$available_years = $innovation->getAvailableYears();

require_once 'includes/admin_header.php';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Kelola Inovasi Pembelajaran</h2>
                <p class="text-gray-600 mt-1">Kelola inovasi teknologi, metode, kurikulum, dan fasilitas pembelajaran yang mendukung pendidikan berkualitas</p>
            </div>
            <button onclick="openCreateModal()" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-plus mr-2"></i>Tambah Inovasi
            </button>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-laptop text-green-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-sm text-green-600 font-medium">Teknologi</p>
                        <p class="text-xl font-bold text-green-900"><?= $stats['by_category']['teknologi'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-chalkboard-teacher text-green-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-sm text-green-600 font-medium">Metode</p>
                        <p class="text-xl font-bold text-green-900"><?= $stats['by_category']['metode'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-book-open text-yellow-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-sm text-yellow-600 font-medium">Kurikulum</p>
                        <p class="text-xl font-bold text-yellow-900"><?= $stats['by_category']['kurikulum'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-building text-purple-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-sm text-purple-600 font-medium">Fasilitas</p>
                        <p class="text-xl font-bold text-purple-900"><?= $stats['by_category']['fasilitas'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
            <span>Total Inovasi: <strong><?= $stats['total'] ?></strong></span>
            <span>Unggulan: <strong><?= $stats['featured'] ?></strong></span>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Cari judul atau deskripsi...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Kategori</option>
                    <option value="teknologi" <?= $category_filter === 'teknologi' ? 'selected' : '' ?>>Teknologi Pembelajaran</option>
                    <option value="metode" <?= $category_filter === 'metode' ? 'selected' : '' ?>>Metode Pembelajaran</option>
                    <option value="kurikulum" <?= $category_filter === 'kurikulum' ? 'selected' : '' ?>>Inovasi Kurikulum</option>
                    <option value="fasilitas" <?= $category_filter === 'fasilitas' ? 'selected' : '' ?>>Fasilitas & Infrastruktur</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <select name="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Tahun</option>
                    <?php foreach ($available_years as $year): ?>
                    <option value="<?= $year ?>" <?= $year_filter == $year ? 'selected' : '' ?>><?= $year ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="innovation.php" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Innovations List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Daftar Inovasi</h3>
        </div>
        
        <?php if (empty($innovations)): ?>
        <div class="text-center py-12">
            <i class="fas fa-lightbulb text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500 text-lg">Belum ada inovasi</p>
            <p class="text-gray-400">Klik tombol "Tambah Inovasi" untuk menambahkan inovasi pembelajaran baru</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inovasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($innovations as $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <?php if ($item['image']): ?>
                                    <img class="w-16 h-16 rounded-lg object-cover" 
                                         src="uploads/innovations/<?= htmlspecialchars($item['image']) ?>" 
                                         alt="<?= htmlspecialchars($item['title']) ?>">
                                    <?php else: ?>
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="<?= $innovation->getCategoryIcon($item['category']) ?> text-gray-400 text-xl"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-4 min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($item['title']) ?>
                                        <?php if ($item['is_featured']): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 ml-2">
                                            <i class="fas fa-star mr-1"></i>Unggulan
                                        </span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-sm text-gray-500 truncate max-w-md">
                                        <?= htmlspecialchars(substr($item['description'], 0, 100)) ?>...
                                    </p>
                                    <div class="flex items-center mt-2 space-x-4 text-xs text-gray-400">
                                        <?php if ($item['video_url']): ?>
                                        <span><i class="fas fa-video mr-1"></i>Video</span>
                                        <?php endif; ?>
                                        <?php if ($innovation->isRecent($item['implementation_year'])): ?>
                                        <span class="text-green-600"><i class="fas fa-circle mr-1"></i>Terbaru</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="<?= $innovation->getCategoryIcon($item['category']) ?> mr-1"></i>
                                <?= $innovation->getCategoryName($item['category']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= $item['implementation_year'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $item['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $item['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <button onclick="viewInnovation(<?= $item['id'] ?>)" 
                                        class="text-green-600 hover:text-green-900" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editInnovation(<?= $item['id'] ?>)" 
                                        class="text-green-600 hover:text-green-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?action=toggle_featured&id=<?= $item['id'] ?>" 
                                   class="text-yellow-600 hover:text-yellow-900" title="Toggle Unggulan"
                                   onclick="return confirm('Ubah status unggulan?')">
                                    <i class="fas <?= $item['is_featured'] ? 'fa-star' : 'fa-star-o' ?>"></i>
                                </a>
                                <a href="?action=toggle_active&id=<?= $item['id'] ?>" 
                                   class="text-green-600 hover:text-green-900" title="Toggle Aktif"
                                   onclick="return confirm('Ubah status aktif?')">
                                    <i class="fas <?= $item['is_active'] ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                </a>
                                <button onclick="deleteInnovation(<?= $item['id'] ?>, '<?= htmlspecialchars($item['title']) ?>')" 
                                        class="text-red-600 hover:text-red-900" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan <?= $offset + 1 ?> - <?= min($offset + $limit, $total_records) ?> dari <?= $total_records ?> data
                </div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category_filter) ?>&year=<?= urlencode($year_filter) ?>" 
                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category_filter) ?>&year=<?= urlencode($year_filter) ?>" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium <?= $i === $page ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category_filter) ?>&year=<?= urlencode($year_filter) ?>" 
                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="innovationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-5xl bg-white rounded-md shadow-lg min-h-[80vh] max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Tambah Inovasi</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="innovationForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="innovationId">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Inovasi *</label>
                        <input type="text" name="title" id="title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                        <select name="category" id="category" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Pilih Kategori</option>
                            <option value="teknologi">Teknologi Pembelajaran</option>
                            <option value="metode">Metode Pembelajaran</option>
                            <option value="kurikulum">Inovasi Kurikulum</option>
                            <option value="fasilitas">Fasilitas & Infrastruktur</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi *</label>
                    <textarea name="description" id="description" rows="6" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 resize-y"
                              style="color: #374151 !important; background-color: #ffffff !important; opacity: 1 !important; visibility: visible !important; min-height: 120px;"
                              placeholder="Jelaskan inovasi pembelajaran ini..."></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Implementasi *</label>
                        <input type="number" name="implementation_year" id="implementation_year" 
                               min="2000" max="<?= date('Y') + 5 ?>" value="<?= date('Y') ?>" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL Video (opsional)</label>
                        <input type="url" name="video_url" id="video_url"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                               placeholder="https://youtube.com/...">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Manfaat (satu per baris)</label>
                        <textarea name="benefits" id="benefits" rows="6"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 resize-y"
                                  style="color: #374151 !important; background-color: #ffffff !important; opacity: 1 !important; visibility: visible !important; min-height: 120px;"
                                  placeholder="Meningkatkan kualitas pembelajaran&#10;Mempermudah proses belajar mengajar&#10;Meningkatkan partisipasi siswa"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fitur (satu per baris)</label>
                        <textarea name="features" id="features" rows="6"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 resize-y"
                                  style="color: #374151 !important; background-color: #ffffff !important; opacity: 1 !important; visibility: visible !important; min-height: 120px;"
                                  placeholder="Interface yang user-friendly&#10;Integrasi dengan sistem sekolah&#10;Real-time monitoring"></textarea>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Inovasi</label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF, WEBP (maksimal 5MB)</p>
                    <div id="currentImage" class="hidden mt-2">
                        <img id="currentImagePreview" class="w-20 h-20 object-cover rounded-lg" src="" alt="Current image">
                    </div>
                </div>
                
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1"
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="is_featured" class="ml-2 block text-sm text-gray-700">
                            Jadikan unggulan
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">
                            Aktifkan inovasi ini
                        </label>
                    </div>
                </div>
                
                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                        <span id="submitText">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl bg-white rounded-md shadow-lg">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Detail Inovasi</h3>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="viewContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Inovasi';
    document.getElementById('formAction').value = 'create';
    document.getElementById('submitText').textContent = 'Simpan';
    document.getElementById('innovationForm').reset();
    document.getElementById('innovationId').value = '';
    document.getElementById('currentImage').classList.add('hidden');
    document.getElementById('innovationModal').classList.remove('hidden');
}

function editInnovation(id) {
    // Fetch innovation data via AJAX
    fetch(`innovation_ajax.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const innovation = data.innovation;
                
                document.getElementById('modalTitle').textContent = 'Edit Inovasi';
                document.getElementById('formAction').value = 'update';
                document.getElementById('submitText').textContent = 'Perbarui';
                document.getElementById('innovationId').value = innovation.id;
                document.getElementById('title').value = innovation.title || '';
                document.getElementById('category').value = innovation.category || '';
                document.getElementById('description').value = innovation.description || '';
                document.getElementById('implementation_year').value = innovation.implementation_year || new Date().getFullYear();
                document.getElementById('video_url').value = innovation.video_url || '';
                
                // Debug logging
                console.log('Loading innovation data:', {
                    title: innovation.title,
                    description: innovation.description,
                    benefits: innovation.benefits,
                    features: innovation.features
                });
                
                // Force textarea visibility and styles
                setTimeout(() => {
                    const descTextarea = document.getElementById('description');
                    const benefitsTextarea = document.getElementById('benefits');
                    const featuresTextarea = document.getElementById('features');
                    
                    [descTextarea, benefitsTextarea, featuresTextarea].forEach(textarea => {
                        if (textarea) {
                            textarea.style.color = '#374151';
                            textarea.style.backgroundColor = '#ffffff';
                            textarea.style.opacity = '1';
                            textarea.style.visibility = 'visible';
                        }
                    });
                    
                    console.log('Textarea values after loading:');
                    console.log('Description:', descTextarea.value);
                    console.log('Benefits:', benefitsTextarea.value);
                    console.log('Features:', featuresTextarea.value);
                }, 100);
                
                // Parse JSON arrays back to text
                if (innovation.benefits && innovation.benefits !== 'null' && innovation.benefits !== '') {
                    try {
                        const benefits = JSON.parse(innovation.benefits);
                        if (Array.isArray(benefits)) {
                            // Clean carriage returns and trim whitespace
                            const cleanBenefits = benefits.map(item => item.replace(/\r/g, '').trim()).filter(item => item);
                            document.getElementById('benefits').value = cleanBenefits.join('\n');
                        } else {
                            document.getElementById('benefits').value = '';
                        }
                    } catch (e) {
                        // If it's not valid JSON, assume it's plain text
                        const cleanText = innovation.benefits.replace(/\r/g, '').trim();
                        document.getElementById('benefits').value = cleanText;
                    }
                } else {
                    document.getElementById('benefits').value = '';
                }
                
                if (innovation.features && innovation.features !== 'null' && innovation.features !== '') {
                    try {
                        const features = JSON.parse(innovation.features);
                        if (Array.isArray(features)) {
                            // Clean carriage returns and trim whitespace
                            const cleanFeatures = features.map(item => item.replace(/\r/g, '').trim()).filter(item => item);
                            document.getElementById('features').value = cleanFeatures.join('\n');
                        } else {
                            document.getElementById('features').value = '';
                        }
                    } catch (e) {
                        // If it's not valid JSON, assume it's plain text
                        const cleanText = innovation.features.replace(/\r/g, '').trim();
                        document.getElementById('features').value = cleanText;
                    }
                } else {
                    document.getElementById('features').value = '';
                }
                
                document.getElementById('is_featured').checked = innovation.is_featured == 1;
                document.getElementById('is_active').checked = innovation.is_active == 1;
                
                // Show current image if exists
                if (innovation.image) {
                    document.getElementById('currentImagePreview').src = `uploads/innovations/${innovation.image}`;
                    document.getElementById('currentImage').classList.remove('hidden');
                } else {
                    document.getElementById('currentImage').classList.add('hidden');
                }
                
                document.getElementById('innovationModal').classList.remove('hidden');
            } else {
                alert('Gagal memuat data inovasi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data');
        });
}

function viewInnovation(id) {
    // Fetch innovation data via AJAX
    fetch(`innovation_ajax.php?action=view&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('viewContent').innerHTML = data.html;
                document.getElementById('viewModal').classList.remove('hidden');
            } else {
                alert('Gagal memuat data inovasi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data');
        });
}

function deleteInnovation(id, title) {
    if (confirm(`Apakah Anda yakin ingin menghapus inovasi "${title}"?\n\nTindakan ini tidak dapat dibatalkan.`)) {
        window.location.href = `innovation.php?action=delete&id=${id}`;
    }
}

function closeModal() {
    document.getElementById('innovationModal').classList.add('hidden');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const innovationModal = document.getElementById('innovationModal');
    const viewModal = document.getElementById('viewModal');
    
    if (event.target === innovationModal) {
        closeModal();
    }
    if (event.target === viewModal) {
        closeViewModal();
    }
}
</script>

<?php require_once 'includes/admin_footer.php'; ?>
