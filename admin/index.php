<?php
if ($_SERVER['REQUEST_URI'] === '/admin') {
    header('Location: /admin/index.php');
    exit;
}
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/ContactMessage.php';
require_once 'includes/functions.php';

// Require login
Auth::requireLogin();

// Get current user
$current_user = Auth::getCurrentUser();
$page_title = 'Dashboard';

// Get statistics
$database = new Database();
$db = $database->getConnection();

// Count total news
$news_query = "SELECT COUNT(*) as total, 
               SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
               SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft
               FROM news";
$news_stmt = $db->prepare($news_query);
$news_stmt->execute();
$news_stats = $news_stmt->fetch(PDO::FETCH_ASSOC);

// Count academic programs
$academic_query = "SELECT COUNT(*) as total FROM academic_programs WHERE is_active = 1";
$academic_stmt = $db->prepare($academic_query);
$academic_stmt->execute();
$academic_stats = $academic_stmt->fetch(PDO::FETCH_ASSOC);

// Count innovations
$innovation_query = "SELECT COUNT(*) as total FROM innovations WHERE is_active = 1";
$innovation_stmt = $db->prepare($innovation_query);
$innovation_stmt->execute();
$innovation_stats = $innovation_stmt->fetch(PDO::FETCH_ASSOC);

// Count new contact messages
$contactMessage = new ContactMessage($db);
$messages_stats_full = $contactMessage->getStats();
$messages_stats = ['total' => $messages_stats_full['unread']];

// Get recent news
$recent_news_query = "SELECT id, title, category, status, created_at, author_id 
                      FROM news 
                      ORDER BY created_at DESC 
                      LIMIT 5";
$recent_news_stmt = $db->prepare($recent_news_query);
$recent_news_stmt->execute();
$recent_news = $recent_news_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent contact messages
$recent_messages = $contactMessage->getRecentMessages(5);

// Get user stats for admin
$userModel = new User($db);
$user_stats = $userModel->getStats();
?>
<?php include 'includes/admin_header.php'; ?>

<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-primary-500 to-purple-600 rounded-lg shadow p-6 text-white">
        <div class="flex items-center">
            <div class="flex-1">
                <h2 class="text-2xl font-bold mb-2">Selamat Datang, <?= htmlspecialchars($current_user['name']) ?>!</h2>
                <p class="text-white/80">Kelola konten website SDIP Tunas Bangsa dengan mudah melalui dashboard admin ini.</p>
            </div>
            <div class="hidden md:block">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Berita -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-newspaper text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Total Berita</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $news_stats['total'] ?></p>
                    <p class="text-xs text-gray-500 mt-1">
                        <?= $news_stats['published'] ?> published, <?= $news_stats['draft'] ?> draft
                    </p>
                </div>
            </div>
        </div>

        <!-- Program Akademik -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Program Akademik</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $academic_stats['total'] ?></p>
                    <p class="text-xs text-gray-500 mt-1">Program aktif</p>
                </div>
            </div>
        </div>

        <!-- Pesan Baru -->
        <?php if (Auth::canManageMessages()): ?>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Pesan Baru</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $messages_stats['total'] ?></p>
                    <p class="text-xs text-gray-500 mt-1">Belum dibaca</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- User Management (Admin only) -->
        <?php if (Auth::canManageUsers()): ?>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $user_stats['total'] ?></p>
                    <p class="text-xs text-gray-500 mt-1"><?= $user_stats['active'] ?> aktif</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php if (Auth::canEditContent() && !Auth::isReadOnly()): ?>
            <a href="news.php?action=create" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-colors">
                <i class="fas fa-plus-circle text-2xl text-primary-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Tambah Berita</span>
            </a>
            <?php elseif (Auth::canEditContent()): ?>
            <a href="news.php" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-colors">
                <i class="fas fa-newspaper text-2xl text-primary-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Lihat Berita</span>
            </a>
            <?php endif; ?>
            
            <?php if (Auth::canEditContent() && !Auth::isReadOnly()): ?>
            <a href="academic.php?action=create" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-colors">
                <i class="fas fa-book text-2xl text-primary-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Tambah Program</span>
            </a>
            <?php elseif (Auth::canEditContent()): ?>
            <a href="academic.php" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-colors">
                <i class="fas fa-book-open text-2xl text-primary-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Lihat Program</span>
            </a>
            <?php endif; ?>
            
            <?php if (Auth::canEditContent() && !Auth::isReadOnly()): ?>
            <a href="innovation.php?action=create" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-colors">
                <i class="fas fa-lightbulb text-2xl text-primary-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Tambah Inovasi</span>
            </a>
            <?php elseif (Auth::canEditContent()): ?>
            <a href="innovation.php" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-colors">
                <i class="fas fa-lightbulb text-2xl text-primary-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Lihat Inovasi</span>
            </a>
            <?php endif; ?>
            
            <?php if (Auth::canManageMessages()): ?>
            <a href="messages.php" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-colors">
                <i class="fas fa-envelope text-2xl text-primary-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Lihat Pesan</span>
            </a>
            <?php endif; ?>
            
            <?php if (Auth::canManageUsers()): ?>
            <a href="users.php" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-colors">
                <i class="fas fa-users text-2xl text-primary-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Kelola User</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent News -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Berita Terbaru</h3>
                    <a href="news.php" class="text-sm text-primary-600 hover:text-primary-700">Lihat Semua</a>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($recent_news)): ?>
                <p class="text-gray-500 text-center py-4">Belum ada berita</p>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recent_news as $news): ?>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                <?= $news['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                <?= ucfirst($news['status']) ?>
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                <?= htmlspecialchars($news['title']) ?>
                            </p>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <span class="capitalize"><?= $news['category'] ?></span>
                                <span class="mx-1">â€¢</span>
                                <span><?= timeAgo($news['created_at']) ?></span>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="news.php?action=edit&id=<?= $news['id'] ?>" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Messages -->
        <?php if (Auth::canManageMessages()): ?>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Pesan Terbaru</h3>
                    <a href="messages.php" class="text-sm text-primary-600 hover:text-primary-700">Lihat Semua</a>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($recent_messages)): ?>
                <p class="text-gray-500 text-center py-4">Belum ada pesan</p>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recent_messages as $message): ?>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-500 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                <?= htmlspecialchars($message['name']) ?>
                            </p>
                            <p class="text-sm text-gray-500 truncate">
                                <?= htmlspecialchars($message['subject']) ?>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                <?= date('d/m/Y H:i', strtotime($message['created_at'])) ?>
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <?php if ($message['status'] === 'unread'): ?>
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <!-- Placeholder for users without message permission -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Sistem Informasi</h3>
            </div>
            <div class="p-6">
                <div class="text-center py-8">
                    <i class="fas fa-info-circle text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 mb-2">Website SDIP Tunas Bangsa</p>
                    <p class="text-sm text-gray-400">Sistem Manajemen Konten</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- System Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Sistem</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-500">PHP Version</dt>
                <dd class="text-lg font-semibold text-gray-900"><?= PHP_VERSION ?></dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Server Software</dt>
                <dd class="text-lg font-semibold text-gray-900"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Login Terakhir</dt>
                <dd class="text-lg font-semibold text-gray-900">
                    <?= $current_user['last_login'] ? formatTanggal($current_user['last_login']) : 'Belum pernah' ?>
                </dd>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
