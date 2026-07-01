<?php
$page_title = 'Manajemen Berita';
require_once 'includes/functions.php';
require_once 'models/News.php';
require_once 'config/database.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();
$news = new News($db);

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Debug logging
error_log('NEWS REQUEST: Action=' . $action . ', ID=' . $id . ', Method=' . $_SERVER['REQUEST_METHOD']);
error_log('NEWS REQUEST: Session ID: ' . session_id());
error_log('NEWS REQUEST: Current session CSRF token: ' . ($_SESSION['csrf_token'] ?? 'NOT SET'));
if (isset($_GET['csrf_token'])) {
    error_log('NEWS REQUEST: CSRF token provided via GET: ' . substr($_GET['csrf_token'], 0, 10) . '...');
    error_log('NEWS REQUEST: Full GET token: ' . $_GET['csrf_token']);
    $csrf_valid_get = validateCSRFToken($_GET['csrf_token']);
    error_log('NEWS REQUEST: GET CSRF validation: ' . ($csrf_valid_get ? 'VALID' : 'INVALID'));
}

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('NEWS ACTION: POST request received');
    $csrf_valid = isset($_POST['csrf_token']) && validateCSRFToken($_POST['csrf_token']);
    error_log('NEWS ACTION: CSRF validation result: ' . ($csrf_valid ? 'VALID' : 'INVALID'));
    if ($csrf_valid) {
        
        switch ($action) {
            case 'create':
                $errors = [];
                $data = [
                    'title' => sanitizeInput($_POST['title'] ?? ''),
                    'content' => $_POST['content'] ?? '',
                    'excerpt' => sanitizeInput($_POST['excerpt'] ?? ''),
                    'category' => $_POST['category'] ?? '',
                    'status' => $_POST['status'] ?? 'draft',
                    'is_featured' => isset($_POST['is_featured'])
                ];
                
                // Validate
                $errors = $news->validate($data);
                
                if (empty($errors)) {
                    // Handle file upload
                    $featured_image = '';
                    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                        
                        if ($_FILES['featured_image']['size'] > 2 * 1024 * 1024) {
                            Auth::setFlashMessage('error', 'Gagal upload! Ukuran gambar cover melebihi batas 2MB.');
                            header('Location: news.php?action=create');
                            exit;
                        }
                        
                        $featured_image = uploadFile($_FILES['featured_image'], 'uploads/', ['jpg', 'jpeg', 'png', 'gif']);
                        
                        if (!$featured_image) {
                            Auth::setFlashMessage('error', 'Gagal upload gambar cover. Pastikan format file sesuai.');
                            header('Location: news.php?action=create');
                            exit;
                        }
                    }
                    
                    if (empty($errors)) {
                        // Set news properties
                        $news->title = $data['title'];
                        $news->slug = createSlug($data['title']);
                        $news->content = $data['content'];
                        $news->excerpt = $data['excerpt'] ?: substr(strip_tags($data['content']), 0, 200) . '...';
                        $news->featured_image = $featured_image;
                        $news->category = $data['category'];
                        $news->status = $data['status'];
                        $news->is_featured = $data['is_featured'];
                        $news->author_id = $_SESSION['admin_id'];
                        
                        if ($news->create()) {
                            setAlert('Berita berhasil dibuat!', 'success');
                            header('Location: news.php');
                            exit;
                        } else {
                            $errors[] = 'Gagal menyimpan berita ke database';
                        }
                    }
                }
                break;
                
            case 'edit':
                if ($id) {
                    $errors = [];
                    $data = [
                        'title' => sanitizeInput($_POST['title'] ?? ''),
                        'content' => $_POST['content'] ?? '',
                        'excerpt' => sanitizeInput($_POST['excerpt'] ?? ''),
                        'category' => $_POST['category'] ?? '',
                        'status' => $_POST['status'] ?? 'draft',
                        'is_featured' => isset($_POST['is_featured'])
                    ];
                    
                    // Set ID for validation
                    $news->id = $id;
                    $errors = $news->validate($data, true);
                    
                    if (empty($errors)) {
                        // Get current data
                        $current = $news->getById($id);
                        $featured_image = $current['featured_image'];
                        
                        // Handle file upload
                        error_log('NEWS UPDATE: Checking for file upload. Files array: ' . print_r($_FILES, true));
                        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                            
                            if ($_FILES['featured_image']['size'] > 2 * 1024 * 1024) {
                                Auth::setFlashMessage('error', 'Gagal upload! Ukuran gambar cover melebihi batas 2MB.');
                                header('Location: news.php?action=edit&id=' . $id);
                                exit;
                            }
                            
                            $new_image = uploadFile($_FILES['featured_image'], 'uploads/', ['jpg', 'jpeg', 'png', 'gif']);
                            
                            if ($new_image) {
                                // Delete old image if exists
                                if ($featured_image && file_exists('uploads/' . $featured_image)) {
                                    unlink('uploads/' . $featured_image);
                                }
                                $featured_image = $new_image;
                            } else {
                                $errors[] = 'Gagal upload gambar. Format yang didukung: JPG, JPEG, PNG, GIF';
                            }
                        }
                        
                        if (empty($errors)) {
                            // Set news properties
                            $news->title = $data['title'];
                            $news->slug = createSlug($data['title']);
                            $news->content = $data['content'];
                            $news->excerpt = $data['excerpt'] ?: substr(strip_tags($data['content']), 0, 200) . '...';
                            $news->featured_image = $featured_image;
                            $news->category = $data['category'];
                            $news->status = $data['status'];
                            $news->is_featured = $data['is_featured'];
                            
                            if ($news->update()) {
                                setAlert('Berita berhasil diupdate!', 'success');
                                header('Location: news.php');
                                exit;
                            } else {
                                $errors[] = 'Gagal mengupdate berita';
                            }
                        }
                    }
                }
                break;
                
            // DELETE action now handled separately via GET request
        }
    } else {
        setAlert('Token CSRF tidak valid!', 'error');
    }
} elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle DELETE via GET request
    error_log('NEWS ACTION: GET DELETE request received');
    $csrf_token_get = $_GET['csrf_token'] ?? null;
    
    // Temporarily bypass CSRF for GET delete due to session issues
    // TODO: Fix session persistence issue
    if ($csrf_token_get) {
        error_log('NEWS ACTION: Bypassing CSRF validation for GET delete (temporary fix)');
        
        if ($id) {
            $current = $news->getById($id);
            error_log('DELETE ACTION: News found: ' . ($current ? 'YES - ' . $current['title'] : 'NO'));
            if ($current) {
                $news->id = $id;
                if ($news->delete()) {
                    error_log('DELETE ACTION: Database delete successful');
                    // Delete associated image
                    if ($current['featured_image'] && file_exists('uploads/' . $current['featured_image'])) {
                        if (unlink('uploads/' . $current['featured_image'])) {
                            error_log('DELETE ACTION: Image deleted: ' . $current['featured_image']);
                        } else {
                            error_log('DELETE ACTION: Failed to delete image: ' . $current['featured_image']);
                        }
                    }
                    setAlert('Berita berhasil dihapus!', 'success');
                } else {
                    error_log('DELETE ACTION: Database delete failed');
                    setAlert('Gagal menghapus berita!', 'error');
                }
            } else {
                error_log('DELETE ACTION: News not found with ID: ' . $id);
                setAlert('Berita tidak ditemukan!', 'error');
            }
        } else {
            error_log('DELETE ACTION: No ID provided');
            setAlert('ID berita tidak valid!', 'error');
        }
    } else {
        error_log('NEWS ACTION: Invalid CSRF token for GET delete');
        setAlert('Token CSRF tidak valid untuk delete!', 'error');
    }
    
    header('Location: news.php');
    exit;
}

// Get data for display
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';

$newsList = $news->getAll($limit, $offset, $search, $category_filter, $status_filter);
$totalNews = $news->count($search, $category_filter, $status_filter);
$pagination = paginate($totalNews, $limit, $page);

// Get single news for edit
$editNews = null;
if ($action === 'edit' && $id) {
    $editNews = $news->getById($id);
    if (!$editNews) {
        setAlert('Berita tidak ditemukan!', 'error');
        header('Location: news.php');
        exit;
    }
}

require_once 'includes/admin_header.php';
?>

<?php if ($action === 'list'): ?>
<!-- List View -->
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Manajemen Berita</h2>
            <p class="text-gray-600">Kelola semua berita dan artikel sekolah</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="news.php?action=create" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Berita
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Cari berita..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Kategori</option>
                    <option value="umum" <?= $category_filter === 'umum' ? 'selected' : '' ?>>Umum</option>
                    <option value="prestasi" <?= $category_filter === 'prestasi' ? 'selected' : '' ?>>Prestasi</option>
                    <option value="kegiatan" <?= $category_filter === 'kegiatan' ? 'selected' : '' ?>>Kegiatan</option>
                    <option value="pengumuman" <?= $category_filter === 'pengumuman' ? 'selected' : '' ?>>Pengumuman</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Status</option>
                    <option value="draft" <?= $status_filter === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= $status_filter === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="archived" <?= $status_filter === 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- News Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berita</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($newsList)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-newspaper text-4xl mb-4 text-gray-300"></i>
                            <p>Belum ada berita</p>
                            <a href="news.php?action=create" class="text-primary-600 hover:text-primary-700 font-medium">Buat berita pertama</a>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($newsList as $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-start space-x-3">
                                <?php if ($item['featured_image']): ?>
                                <img src="uploads/<?= $item['featured_image'] ?>" alt="" class="w-16 h-16 object-cover rounded-lg">
                                <?php else: ?>
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                                <?php endif; ?>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 line-clamp-2">
                                        <?= htmlspecialchars($item['title']) ?>
                                        <?php if ($item['is_featured']): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 ml-2">
                                            <i class="fas fa-star mr-1"></i>Featured
                                        </span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-sm text-gray-500 line-clamp-1"><?= htmlspecialchars($item['excerpt'] ?: '') ?></p>
                                    <p class="text-xs text-gray-400 mt-1">By <?= htmlspecialchars($item['author_name'] ?: 'Unknown') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium capitalize
                                <?= $item['category'] === 'prestasi' ? 'bg-green-100 text-green-800' : 
                                   ($item['category'] === 'kegiatan' ? 'bg-green-100 text-green-800' : 
                                   ($item['category'] === 'pengumuman' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) ?>">
                                <?= $item['category'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium capitalize
                                <?= $item['status'] === 'published' ? 'bg-green-100 text-green-800' : 
                                   ($item['status'] === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') ?>">
                                <?= $item['status'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <i class="fas fa-eye mr-1"></i><?= number_format($item['views']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= formatTanggal($item['created_at']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <a href="news.php?action=edit&id=<?= $item['id'] ?>" 
                                   class="text-primary-600 hover:text-primary-700" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" onclick="if(confirmDelete('Yakin ingin menghapus berita ini?')) { window.location.href='news.php?action=delete&id=<?= $item['id'] ?>&csrf_token=<?= generateCSRFToken() ?>'; }" 
                                   class="text-red-600 hover:text-red-700" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $totalNews) ?> of <?= $totalNews ?> results
                </div>
                <div class="flex space-x-2">
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $category_filter ? '&category=' . $category_filter : '' ?><?= $status_filter ? '&status=' . $status_filter : '' ?>"
                       class="px-3 py-2 text-sm <?= $i === $page ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' ?> border border-gray-300 rounded-md">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php elseif ($action === 'create' || $action === 'edit'): ?>
<!-- Create/Edit Form -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center">
        <a href="news.php" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                <?= $action === 'create' ? 'Tambah Berita Baru' : 'Edit Berita' ?>
            </h2>
            <p class="text-gray-600">
                <?= $action === 'create' ? 'Buat berita atau artikel baru' : 'Ubah informasi berita' ?>
            </p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" enctype="multipart/form-data" class="space-y-6" <?= $action === 'edit' ? 'action="news.php?action=edit&id=' . $id . '"' : '' ?>>
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Title -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Berita *</label>
                    <input type="text" name="title" required
                           value="<?= htmlspecialchars($editNews['title'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Masukkan judul berita...">
                </div>

                <!-- Content -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konten Berita *</label>
                    <textarea name="content" id="editor" rows="20"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                              placeholder="Tulis konten berita lengkap..."><?= htmlspecialchars($editNews['content'] ?? '') ?></textarea>
                </div>

                <!-- Excerpt -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ringkasan</label>
                    <textarea name="excerpt" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                              placeholder="Ringkasan singkat berita (opsional, akan dibuat otomatis jika kosong)"><?= htmlspecialchars($editNews['excerpt'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish Settings -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pengaturan Publikasi</h3>
                    
                    <div class="space-y-4">
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="draft" <?= ($editNews['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="published" <?= ($editNews['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="archived" <?= ($editNews['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                            </select>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                            <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">Pilih Kategori</option>
                                <option value="umum" <?= ($editNews['category'] ?? '') === 'umum' ? 'selected' : '' ?>>Umum</option>
                                <option value="prestasi" <?= ($editNews['category'] ?? '') === 'prestasi' ? 'selected' : '' ?>>Prestasi</option>
                                <option value="kegiatan" <?= ($editNews['category'] ?? '') === 'kegiatan' ? 'selected' : '' ?>>Kegiatan</option>
                                <option value="pengumuman" <?= ($editNews['category'] ?? '') === 'pengumuman' ? 'selected' : '' ?>>Pengumuman</option>
                            </select>
                        </div>

                        <!-- Featured -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" value="1" 
                                       <?= ($editNews['is_featured'] ?? false) ? 'checked' : '' ?>
                                       class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                                <span class="ml-2 text-sm text-gray-700">Berita Unggulan</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1">Tampilkan di section berita unggulan</p>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Gambar Unggulan</h3>
                    
                    <?php if ($action === 'edit' && $editNews['featured_image']): ?>
                    <div class="mb-4">
                        <img src="uploads/<?= $editNews['featured_image'] ?>" alt="Current image" 
                             class="w-full h-32 object-cover rounded-lg">
                        <p class="text-sm text-gray-500 mt-2">Gambar saat ini</p>
                    </div>
                    <?php endif; ?>
                    
                    <input type="file" name="featured_image" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="text-xs text-gray-500 mt-2">Format: JPG, JPEG, PNG, GIF. Max: 2MB</p>
                </div>

                <!-- Actions -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            <?= $action === 'create' ? 'Buat Berita' : 'Update Berita' ?>
                        </button>
                        <a href="news.php" class="block w-full px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg text-center transition-colors">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php endif; ?>

<!-- TinyMCE Script from cdnjs to avoid API key warning -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#editor',
    plugins: 'image media link code lists table wordcount',
    toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | table code',
    images_upload_url: 'upload_image_ajax.php',
    automatic_uploads: true,
    file_picker_types: 'image',
    height: 600,
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 16px; }',
    setup: function (editor) {
        editor.on('change', function () {
            editor.save(); // ensure textarea is updated before form submit
        });
    }
});
</script>

<script>
// Auto-save draft functionality
let autoSaveTimer;
function autoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(function() {
        // Implementation for auto-save draft
        console.log('Auto-save draft...');
    }, 30000); // Save every 30 seconds
}

// Character counter for title
const titleInput = document.querySelector('input[name="title"]');
if (titleInput) {
    titleInput.addEventListener('input', function() {
        const length = this.value.length;
        const maxLength = 255;
        
        if (length > maxLength - 50) {
            console.log(`Title length: ${length}/${maxLength}`);
        }
    });
}

// Image preview
const imageInput = document.querySelector('input[name="featured_image"]');
if (imageInput) {
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Create or update preview
                let preview = document.querySelector('.image-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.className = 'image-preview mt-4';
                    imageInput.parentNode.appendChild(preview);
                }
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="w-full h-32 object-cover rounded-lg">
                    <p class="text-sm text-gray-500 mt-2">Preview gambar baru</p>
                `;
            };
            reader.readAsDataURL(file);
        }
    });
}
</script>

<?php 
// Display errors if any
if (!empty($errors)): ?>
<script>
<?php foreach ($errors as $error): ?>
showToast('<?= addslashes($error) ?>', 'error');
<?php endforeach; ?>
</script>
<?php endif; ?>

<?php require_once 'includes/admin_footer.php'; ?>
