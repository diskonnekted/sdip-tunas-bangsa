<?php
$page_title = 'Informasi Umum';
require_once 'includes/functions.php';
require_once 'models/GeneralInfo.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();
$generalInfo = new GeneralInfo($db);

// Handle actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $errors = $generalInfo->validate($_POST);
                if (empty($errors)) {
                    // Handle file upload
                    $attachment = '';
                    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = 'uploads/attachments/';
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                        $uploadPath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadPath)) {
                            $attachment = $fileName;
                        }
                    }
                    
                    $generalInfo->title = $_POST['title'];
                    $generalInfo->content = $_POST['content'];
                    $generalInfo->type = $_POST['type'];
                    $generalInfo->priority = $_POST['priority'];
                    $generalInfo->expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
                    $generalInfo->attachment = $attachment;
                    $generalInfo->is_active = isset($_POST['is_active']) ? 1 : 0;
                    
                    if ($generalInfo->create()) {
                        setAlert('success', 'Informasi berhasil ditambahkan!');
                        header('Location: info.php');
                        exit;
                    } else {
                        setAlert('error', 'Gagal menambahkan informasi!');
                    }
                } else {
                    setAlert('error', implode('<br>', $errors));
                }
                break;
                
            case 'update':
                $errors = $generalInfo->validate($_POST);
                if (empty($errors)) {
                    // Get existing data
                    $existingInfo = $generalInfo->getById($_POST['id']);
                    $attachment = $existingInfo['attachment'];
                    
                    // Handle file upload
                    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = 'uploads/attachments/';
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        // Delete old file
                        if ($attachment && file_exists($uploadDir . $attachment)) {
                            unlink($uploadDir . $attachment);
                        }
                        
                        $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                        $uploadPath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadPath)) {
                            $attachment = $fileName;
                        }
                    }
                    
                    $generalInfo->id = $_POST['id'];
                    $generalInfo->title = $_POST['title'];
                    $generalInfo->content = $_POST['content'];
                    $generalInfo->type = $_POST['type'];
                    $generalInfo->priority = $_POST['priority'];
                    $generalInfo->expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
                    $generalInfo->attachment = $attachment;
                    $generalInfo->is_active = isset($_POST['is_active']) ? 1 : 0;
                    
                    if ($generalInfo->update()) {
                        setAlert('success', 'Informasi berhasil diperbarui!');
                        header('Location: info.php');
                        exit;
                    } else {
                        setAlert('error', 'Gagal memperbarui informasi!');
                    }
                } else {
                    setAlert('error', implode('<br>', $errors));
                }
                break;
        }
    }
}

// Handle delete action
if ($action === 'delete' && $id) {
    $info = $generalInfo->getById($id);
    if ($info) {
        $generalInfo->id = $id;
        $generalInfo->attachment = $info['attachment'];
        
        if ($generalInfo->delete()) {
            setAlert('success', 'Informasi berhasil dihapus!');
        } else {
            setAlert('error', 'Gagal menghapus informasi!');
        }
    }
    header('Location: info.php');
    exit;
}

// Get filters and pagination
$search = $_GET['search'] ?? '';
$type_filter = $_GET['type'] ?? '';
$priority_filter = $_GET['priority'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Get data
$infos = $generalInfo->getAll($limit, $offset, $search, $type_filter, $priority_filter);
$total_records = $generalInfo->count($search, $type_filter, $priority_filter);
$total_pages = ceil($total_records / $limit);

// Get statistics
$type_counts = $generalInfo->countByType();
$priority_counts = $generalInfo->countByPriority();
$expired_count = count($generalInfo->getExpired());

require_once 'includes/admin_header.php';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Kelola Informasi Umum</h2>
                <p class="text-gray-600 mt-1">Kelola pengumuman, kalender akademik, prosedur, dan dokumen penting sekolah</p>
            </div>
            <button onclick="openCreateModal()" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-plus mr-2"></i>Tambah Informasi
            </button>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-bullhorn text-green-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-sm text-green-600 font-medium">Pengumuman</p>
                        <p class="text-xl font-bold text-green-900"><?= $type_counts['pengumuman'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-calendar-alt text-green-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-sm text-green-600 font-medium">Kalender</p>
                        <p class="text-xl font-bold text-green-900"><?= $type_counts['kalender'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-list-ol text-purple-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-sm text-purple-600 font-medium">Prosedur</p>
                        <p class="text-xl font-bold text-purple-900"><?= $type_counts['prosedur'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-orange-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-file-alt text-orange-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-sm text-orange-600 font-medium">Dokumen</p>
                        <p class="text-xl font-bold text-orange-900"><?= $type_counts['dokumen'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($expired_count > 0): ?>
        <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                <p class="text-red-800">
                    <strong><?= $expired_count ?></strong> informasi telah kedaluwarsa. 
                    <a href="?expired=1" class="underline">Lihat daftar</a>
                </p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Cari judul atau konten...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Tipe</option>
                    <option value="pengumuman" <?= $type_filter === 'pengumuman' ? 'selected' : '' ?>>Pengumuman</option>
                    <option value="kalender" <?= $type_filter === 'kalender' ? 'selected' : '' ?>>Kalender Akademik</option>
                    <option value="prosedur" <?= $type_filter === 'prosedur' ? 'selected' : '' ?>>Prosedur & SOP</option>
                    <option value="dokumen" <?= $type_filter === 'dokumen' ? 'selected' : '' ?>>Dokumen Penting</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Prioritas</label>
                <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Prioritas</option>
                    <option value="tinggi" <?= $priority_filter === 'tinggi' ? 'selected' : '' ?>>Tinggi</option>
                    <option value="sedang" <?= $priority_filter === 'sedang' ? 'selected' : '' ?>>Sedang</option>
                    <option value="rendah" <?= $priority_filter === 'rendah' ? 'selected' : '' ?>>Rendah</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="info.php" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Info List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Daftar Informasi</h3>
        </div>
        
        <?php if (empty($infos)): ?>
        <div class="text-center py-12">
            <i class="fas fa-info-circle text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500 text-lg">Belum ada informasi</p>
            <p class="text-gray-400">Klik tombol "Tambah Informasi" untuk menambahkan informasi baru</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Informasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioritas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($infos as $info): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="<?= $generalInfo->getTypeIcon($info['type']) ?> text-gray-400"></i>
                                </div>
                                <div class="ml-3 min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars($info['title']) ?></p>
                                    <p class="text-sm text-gray-500 truncate"><?= htmlspecialchars(substr($info['content'], 0, 100)) ?>...</p>
                                    <?php if ($info['attachment']): ?>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center text-xs text-green-600">
                                            <i class="fas fa-paperclip mr-1"></i>
                                            Ada lampiran
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <?= $generalInfo->getTypeName($info['type']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $generalInfo->getPriorityBadgeClass($info['priority']) ?>">
                                <?= $generalInfo->getPriorityName($info['priority']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col space-y-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $info['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $info['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                                <?php if ($info['expiry_date'] && $generalInfo->isExpired($info['expiry_date'])): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Kedaluwarsa
                                </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div><?= formatTanggal($info['created_at']) ?></div>
                            <?php if ($info['expiry_date']): ?>
                            <div class="text-xs text-gray-400">
                                Berlaku sampai: <?= formatTanggal($info['expiry_date']) ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <button onclick="viewInfo(<?= $info['id'] ?>)" 
                                        class="text-green-600 hover:text-green-900" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editInfo(<?= $info['id'] ?>)" 
                                        class="text-green-600 hover:text-green-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteInfo(<?= $info['id'] ?>, '<?= htmlspecialchars($info['title']) ?>')" 
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
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type_filter) ?>&priority=<?= urlencode($priority_filter) ?>" 
                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type_filter) ?>&priority=<?= urlencode($priority_filter) ?>" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium <?= $i === $page ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type_filter) ?>&priority=<?= urlencode($priority_filter) ?>" 
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
<div id="infoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl bg-white rounded-md shadow-lg">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Tambah Informasi</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="infoForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="infoId">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul *</label>
                    <input type="text" name="title" id="title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe *</label>
                        <select name="type" id="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Pilih Tipe</option>
                            <option value="pengumuman">Pengumuman</option>
                            <option value="kalender">Kalender Akademik</option>
                            <option value="prosedur">Prosedur & SOP</option>
                            <option value="dokumen">Dokumen Penting</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prioritas *</label>
                        <select name="priority" id="priority" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            <option value="sedang">Sedang</option>
                            <option value="tinggi">Tinggi</option>
                            <option value="rendah">Rendah</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konten *</label>
                    <textarea name="content" id="content" rows="6" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Masukkan konten informasi..."></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kedaluwarsa</label>
                    <input type="date" name="expiry_date" id="expiry_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ada batas waktu</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran</label>
                    <input type="file" name="attachment" id="attachment"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <p class="text-xs text-gray-500 mt-1">Format yang didukung: PDF, DOC, DOCX, XLS, XLSX (maksimal 5MB)</p>
                    <div id="currentAttachment" class="hidden mt-2">
                        <p class="text-sm text-gray-600">Lampiran saat ini: <span id="attachmentName"></span></p>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Aktifkan informasi ini
                    </label>
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
    <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl bg-white rounded-md shadow-lg">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Detail Informasi</h3>
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
    document.getElementById('modalTitle').textContent = 'Tambah Informasi';
    document.getElementById('formAction').value = 'create';
    document.getElementById('submitText').textContent = 'Simpan';
    document.getElementById('infoForm').reset();
    document.getElementById('infoId').value = '';
    document.getElementById('currentAttachment').classList.add('hidden');
    document.getElementById('infoModal').classList.remove('hidden');
}

function editInfo(id) {
    // Fetch info data via AJAX
    fetch(`info_ajax.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const info = data.info;
                
                document.getElementById('modalTitle').textContent = 'Edit Informasi';
                document.getElementById('formAction').value = 'update';
                document.getElementById('submitText').textContent = 'Perbarui';
                document.getElementById('infoId').value = info.id;
                document.getElementById('title').value = info.title;
                document.getElementById('type').value = info.type;
                document.getElementById('priority').value = info.priority;
                document.getElementById('content').value = info.content;
                document.getElementById('expiry_date').value = info.expiry_date || '';
                document.getElementById('is_active').checked = info.is_active == 1;
                
                // Show current attachment if exists
                if (info.attachment) {
                    document.getElementById('attachmentName').textContent = info.attachment;
                    document.getElementById('currentAttachment').classList.remove('hidden');
                } else {
                    document.getElementById('currentAttachment').classList.add('hidden');
                }
                
                document.getElementById('infoModal').classList.remove('hidden');
            } else {
                alert('Gagal memuat data informasi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data');
        });
}

function viewInfo(id) {
    // Fetch info data via AJAX
    fetch(`info_ajax.php?action=view&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('viewContent').innerHTML = data.html;
                document.getElementById('viewModal').classList.remove('hidden');
            } else {
                alert('Gagal memuat data informasi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data');
        });
}

function deleteInfo(id, title) {
    if (confirm(`Apakah Anda yakin ingin menghapus informasi "${title}"?\n\nTindakan ini tidak dapat dibatalkan.`)) {
        window.location.href = `info.php?action=delete&id=${id}`;
    }
}

function closeModal() {
    document.getElementById('infoModal').classList.add('hidden');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const infoModal = document.getElementById('infoModal');
    const viewModal = document.getElementById('viewModal');
    
    if (event.target === infoModal) {
        closeModal();
    }
    if (event.target === viewModal) {
        closeViewModal();
    }
}
</script>

<?php require_once 'includes/admin_footer.php'; ?>
