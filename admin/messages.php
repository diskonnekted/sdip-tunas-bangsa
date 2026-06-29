<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'models/ContactMessage.php';
require_once 'includes/functions.php';

// Require login and check permissions
Auth::requireLogin();
if (!Auth::canManageMessages()) {
    header('HTTP/1.0 403 Forbidden');
    die('Access denied. You do not have permission to manage messages.');
}

// Initialize database and models
$database = new Database();
$db = $database->getConnection();
$contactMessage = new ContactMessage($db);

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'update_status':
            $result = $contactMessage->updateStatus(
                $_POST['id'], 
                $_POST['status'], 
                $_SESSION['user_id'],
                $_POST['admin_notes'] ?? null
            );
            echo json_encode($result);
            exit;
            
        case 'delete_message':
            $result = $contactMessage->delete($_POST['id']);
            echo json_encode($result);
            exit;
            
        case 'mark_as_read':
            $result = $contactMessage->markAsRead($_POST['id']);
            echo json_encode($result);
            exit;
            
        case 'get_message':
            $message = $contactMessage->getById($_POST['id'] ?? $_GET['id'] ?? 0);
            if ($message) {
                // Auto mark as read when viewing details
                if ($message['status'] === 'unread') {
                    $contactMessage->markAsRead($message['id']);
                    $message['status'] = 'read';
                }
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Pesan tidak ditemukan']);
            }
            exit;
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$type_filter = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Get messages and stats
$messages = $contactMessage->getAll($status_filter, $per_page, $offset, $search, $type_filter);
$stats = $contactMessage->getStats();

// Get total count for pagination
$total_query = "SELECT COUNT(*) as total FROM contact_messages";
$conditions = [];
$params = [];

if (!empty($status_filter)) {
    $conditions[] = "status = ?";
    $params[] = $status_filter;
}

if (!empty($type_filter)) {
    $conditions[] = "recipient_type = ?";
    $params[] = $type_filter;
}

if (!empty($search)) {
    $conditions[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $searchParam = "%$search%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
}

if (!empty($conditions)) {
    $total_query .= " WHERE " . implode(" AND ", $conditions);
}

$total_stmt = $db->prepare($total_query);
$total_stmt->execute($params);
$total_records = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_records / $per_page);

$page_title = 'Pesan Kontak';
include 'includes/admin_header.php';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pesan Kontak</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola pesan dari form kontak website</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-inbox text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Total Pesan</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['total']; ?></p>
                </div>
            </div>
        </div>

        <!-- Unread -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Belum Dibaca</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['unread']; ?></p>
                </div>
            </div>
        </div>

        <!-- Read -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope-open text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Sudah Dibaca</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['read']; ?></p>
                </div>
            </div>
        </div>

        <!-- Replied -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-reply text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Sudah Dibalas</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['replied']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pesan</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Cari nama, email, subjek..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            
            <div class="w-full sm:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Semua Tipe</option>
                    <option value="general" <?php echo $type_filter === 'general' ? 'selected' : ''; ?>>Umum</option>
                    <option value="teacher" <?php echo $type_filter === 'teacher' ? 'selected' : ''; ?>>Guru</option>
                    <option value="principal" <?php echo $type_filter === 'principal' ? 'selected' : ''; ?>>Kepala Sekolah</option>
                    <option value="staff" <?php echo $type_filter === 'staff' ? 'selected' : ''; ?>>Staf</option>
                </select>
            </div>

            <div class="w-full sm:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="unread" <?php echo $status_filter === 'unread' ? 'selected' : ''; ?>>Belum Dibaca</option>
                    <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Sudah Dibaca</option>
                    <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Sudah Dibalas</option>
                    <option value="archived" <?php echo $status_filter === 'archived' ? 'selected' : ''; ?>>Diarsipkan</option>
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                
                <?php if (!empty($search) || !empty($status_filter) || !empty($type_filter)): ?>
                    <a href="messages.php" class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg border border-gray-300 transition-colors bg-white">
                        <i class="fas fa-times mr-2"></i>Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Messages List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Daftar Pesan
            </h3>
        </div>
        
        <?php if (empty($messages)): ?>
            <div class="text-center py-16">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Tidak ada pesan</h3>
                <p class="text-gray-500">
                    <?php if (!empty($search) || !empty($status_filter) || !empty($type_filter)): ?>
                        Tidak ada pesan yang sesuai dengan filter yang dipilih.
                    <?php else: ?>
                        Belum ada pesan masuk dari form kontak.
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subjek</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($messages as $message): ?>
                            <tr class="hover:bg-gray-50 <?php echo $message['status'] === 'unread' ? 'bg-green-50' : ''; ?>">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                <?php echo strtoupper(substr($message['name'], 0, 2)); ?>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($message['name']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($message['email']); ?></div>
                                            <?php if (!empty($message['phone'])): ?>
                                                <div class="text-xs text-gray-400"><?php echo htmlspecialchars($message['phone']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $typeClass = 'bg-gray-100 text-gray-800';
                                    $typeLabel = 'Umum';
                                    switch($message['recipient_type'] ?? 'general') {
                                        case 'teacher':
                                            $typeClass = 'bg-green-100 text-green-800';
                                            $typeLabel = 'Guru';
                                            break;
                                        case 'principal':
                                            $typeClass = 'bg-purple-100 text-purple-800';
                                            $typeLabel = 'Kepsek';
                                            break;
                                        case 'staff':
                                            $typeClass = 'bg-green-100 text-green-800';
                                            $typeLabel = 'Staf';
                                            break;
                                    }
                                    ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $typeClass; ?>">
                                        <?php echo $typeLabel; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($message['subject']); ?></div>
                                    <div class="text-sm text-gray-500 line-clamp-2">
                                        <?php echo htmlspecialchars(substr($message['message'], 0, 100)) . (strlen($message['message']) > 100 ? '...' : ''); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo $contactMessage->getStatusBadge($message['status']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div><?php echo date('d M Y', strtotime($message['created_at'])); ?></div>
                                    <div class="text-xs"><?php echo date('H:i', strtotime($message['created_at'])); ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium space-x-2">
                                    <button onclick="viewMessage(<?php echo $message['id']; ?>)" 
                                            class="text-green-600 hover:text-green-900 transition-colors" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <div class="relative inline-block text-left">
                                        <button onclick="toggleDropdown(<?php echo $message['id']; ?>)" 
                                                class="text-gray-600 hover:text-gray-900 transition-colors" title="Menu">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        
                                        <div id="dropdown-<?php echo $message['id']; ?>" 
                                             class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10 text-left">
                                            <div class="py-1">
                                                <?php if ($message['status'] === 'unread'): ?>
                                                    <a href="#" onclick="updateStatus(<?php echo $message['id']; ?>, 'read')" 
                                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-eye mr-2"></i>Tandai Sudah Dibaca
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($message['status'] !== 'replied'): ?>
                                                    <a href="#" onclick="updateStatus(<?php echo $message['id']; ?>, 'replied')" 
                                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-reply mr-2"></i>Tandai Sudah Dibalas
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="#" onclick="updateStatus(<?php echo $message['id']; ?>, 'archived')" 
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-archive mr-2"></i>Arsipkan
                                                </a>
                                                
                                                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>?subject=Re: <?php echo htmlspecialchars($message['subject']); ?>" 
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-envelope mr-2"></i>Balas via Email
                                                </a>
                                                
                                                <div class="border-t border-gray-100"></div>
                                                <a href="#" onclick="deleteMessage(<?php echo $message['id']; ?>)" 
                                                   class="block px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                    <i class="fas fa-trash mr-2"></i>Hapus
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&type=<?php echo $type_filter; ?>&search=<?php echo urlencode($search); ?>" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</a>
                        <?php endif; ?>
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&type=<?php echo $type_filter; ?>&search=<?php echo urlencode($search); ?>" 
                               class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</a>
                        <?php endif; ?>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to <span class="font-medium"><?php echo min($offset + $per_page, $total_records); ?></span> of <span class="font-medium"><?php echo $total_records; ?></span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&type=<?php echo $type_filter; ?>&search=<?php echo urlencode($search); ?>" 
                                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&type=<?php echo $type_filter; ?>&search=<?php echo urlencode($search); ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $page ? 'text-primary-600 bg-primary-50' : 'text-gray-500 hover:bg-gray-50'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&type=<?php echo $type_filter; ?>&search=<?php echo urlencode($search); ?>" 
                                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>

<!-- Message Detail Modal -->
<div id="messageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-4 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 max-w-4xl shadow-lg rounded-lg bg-white my-8 modal-content">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Detail Pesan</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="modalContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Toggle dropdown menu
function toggleDropdown(id) {
    const dropdown = document.getElementById('dropdown-' + id);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== 'dropdown-' + id) {
            d.classList.add('hidden');
        }
    });
    
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleDropdown"]') && !event.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    }
});

// Helper functions for modal
function showModal(title, content) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalContent').innerHTML = content;
    document.getElementById('messageModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('messageModal').classList.add('hidden');
}

function showAlert(message, type = 'success') {
    // You might want to implement a better alert system here
    alert(message);
}

// View message details
async function viewMessage(id) {
    try {
        // Show loading state
        showModal('Detail Pesan', `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                <span class="ml-2 text-gray-600">Memuat detail pesan...</span>
            </div>
        `);
        
        const response = await fetch('messages.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_message&id=${id}`
        });
        
        const result = await response.json();
        
        if (result.success && result.message) {
            const msg = result.message;
            const createdDate = new Date(msg.created_at).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            const statusColors = {
                'unread': 'bg-red-100 text-red-800',
                'read': 'bg-yellow-100 text-yellow-800',
                'replied': 'bg-green-100 text-green-800',
                'archived': 'bg-gray-100 text-gray-800'
            };
            
            const statusLabels = {
                'unread': 'Belum Dibaca',
                'read': 'Sudah Dibaca', 
                'replied': 'Sudah Dibalas',
                'archived': 'Diarsipkan'
            };
            
            const content = `
                <div class="space-y-6">
                    <!-- Header Info -->
                    <div class="bg-gradient-to-r from-green-50 to-purple-50 p-6 rounded-lg">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        ${msg.name.substring(0, 2).toUpperCase()}
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">${escapeHtml(msg.name)}</h3>
                                        <p class="text-gray-600">${escapeHtml(msg.email)}</p>
                                        ${msg.phone ? `<p class="text-sm text-gray-500">${escapeHtml(msg.phone)}</p>` : ''}
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <span><i class="fas fa-calendar-alt mr-1"></i>${createdDate}</span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium ${statusColors[msg.status] || 'bg-gray-100 text-gray-800'}">
                                        ${statusLabels[msg.status] || 'Unknown'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Penerima</label>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="px-2 py-1 rounded-full text-xs font-medium ${
                                    msg.recipient_type === 'teacher' ? 'bg-green-100 text-green-800' :
                                    msg.recipient_type === 'principal' ? 'bg-purple-100 text-purple-800' :
                                    msg.recipient_type === 'staff' ? 'bg-green-100 text-green-800' :
                                    'bg-gray-100 text-gray-800'
                                }">
                                    ${
                                        msg.recipient_type === 'teacher' ? 'Guru' :
                                        msg.recipient_type === 'principal' ? 'Kepala Sekolah' :
                                        msg.recipient_type === 'staff' ? 'Staf' :
                                        'Umum'
                                    }
                                </span>
                            </div>
                        </div>
                        ${msg.student_name ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Siswa</label>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-900 text-sm font-medium">${escapeHtml(msg.student_name)}</p>
                            </div>
                        </div>
                        ` : ''}
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subjek</label>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="font-medium text-gray-900">${escapeHtml(msg.subject)}</p>
                        </div>
                    </div>
                    
                    <!-- Message Content -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Isi Pesan</label>
                        <div class="bg-white border border-gray-200 p-4 rounded-lg max-h-64 overflow-y-auto custom-scrollbar">
                            <p class="text-gray-800 whitespace-pre-wrap">${escapeHtml(msg.message)}</p>
                        </div>
                    </div>
                    
                    <!-- Admin Notes -->
                    ${msg.admin_notes ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin</label>
                            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                                <p class="text-gray-800">${escapeHtml(msg.admin_notes)}</p>
                            </div>
                        </div>
                    ` : ''}
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                        <a href="mailto:${escapeHtml(msg.email)}?subject=Re: ${encodeURIComponent(msg.subject)}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-reply mr-2"></i>Balas via Email
                        </a>
                        
                        ${msg.status !== 'replied' ? `
                            <button onclick="updateStatusFromModal(${msg.id}, 'replied')" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check mr-2"></i>Tandai Sudah Dibalas
                            </button>
                        ` : ''}
                        
                        <button onclick="updateStatusFromModal(${msg.id}, 'archived')" 
                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-archive mr-2"></i>Arsipkan
                        </button>
                        
                        <button onclick="deleteMessageFromModal(${msg.id})" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Hapus Pesan
                        </button>
                    </div>
                </div>
            `;
            
            showModal('Detail Pesan', content);
        } else {
            showModal('Error', `
                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-red-800">${result.message || 'Gagal memuat detail pesan'}</p>
                </div>
            `);
        }
    } catch (error) {
        console.error('Error:', error);
        showModal('Error', `
            <div class="bg-red-50 p-4 rounded-lg">
                <p class="text-red-800">Terjadi kesalahan saat memuat detail pesan</p>
            </div>
        `);
    }
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Update message status
async function updateStatus(id, status) {
    try {
        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('id', id);
        formData.append('status', status);
        
        const response = await fetch('messages.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Reload page to reflect changes
            window.location.reload();
        } else {
            alert('Gagal mengupdate status');
        }
        return result;
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan sistem');
        throw error;
    }
}

// Update message status from modal
async function updateStatusFromModal(id, status) {
    try {
        const result = await updateStatus(id, status);
        closeModal();
    } catch (error) {
        console.error('Error:', error);
    }
}

// Delete message
async function deleteMessage(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus pesan ini?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete_message');
        formData.append('id', id);
        
        const response = await fetch('messages.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.location.reload();
        } else {
            alert('Gagal menghapus pesan');
        }
        return result;
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan sistem');
        throw error;
    }
}

// Delete message from modal
async function deleteMessageFromModal(id) {
    try {
        const result = await deleteMessage(id);
        closeModal();
    } catch (error) {
        console.error('Error:', error);
    }
}
</script>