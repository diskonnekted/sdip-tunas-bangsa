<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/database.php';
// Ensure user is logged in
if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$current_user = Auth::getCurrentUser();
$page_title = $page_title ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Admin SDIP Tunas Bangsa</title>
    <?php include '../includes/favicon.php'; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-64 bg-white border-r border-gray-200">
                <!-- Logo -->
                <div class="flex items-center px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <?php
                        // Fetch logo from settings
                        $logo_db = new Database();
                        $logo_conn = $logo_db->getConnection();
                        $logo_stmt = $logo_conn->prepare("SELECT school_logo FROM school_settings LIMIT 1");
                        $logo_stmt->execute();
                        $admin_logo = $logo_stmt->fetchColumn();
                        ?>
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg overflow-hidden bg-white shadow-sm">
                            <?php if ($admin_logo): ?>
                                <img src="uploads/<?= htmlspecialchars($admin_logo) ?>" alt="Logo" class="w-full h-full object-contain p-1">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-r from-primary-500 to-purple-600 flex items-center justify-center">
                                    <i class="fas fa-graduation-cap text-white"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="ml-3">
                            <h2 class="text-lg font-semibold text-gray-900">SDIP Tunas Bangsa</h2>
                            <p class="text-sm text-gray-500">Admin Panel</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-4 overflow-y-auto sidebar-scroll">
                    <ul class="space-y-1">
                        <li>
                            <a href="index.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : '' ?>">
                                <i class="fas fa-chart-pie mr-3 text-gray-400"></i>
                                Dashboard
                            </a>
                        </li>

                        <?php if (Auth::canEditContent()): ?>
                        <li>
                            <a href="news.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 <?= basename($_SERVER['PHP_SELF']) == 'news.php' ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : '' ?>">
                                <i class="fas fa-newspaper mr-3 text-gray-400"></i>
                                Berita
                                <?php if (Auth::isReadOnly()): ?>
                                <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Read-only</span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (Auth::canEditContent()): ?>
                        <li>
                            <a href="academic.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 <?= basename($_SERVER['PHP_SELF']) == 'academic.php' ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : '' ?>">
                                <i class="fas fa-book-open mr-3 text-gray-400"></i>
                                Program Akademik
                                <?php if (Auth::isReadOnly()): ?>
                                <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Read-only</span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endif; ?>

                        <li>
                            <a href="ppdb.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 <?= basename($_SERVER['PHP_SELF']) == 'ppdb.php' ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : '' ?>">
                                <i class="fas fa-user-plus mr-3 text-gray-400"></i>
                                Data PPDB
                            </a>
                        </li>
                        
                        <?php if (Auth::canManageUsers()): ?>
                        <li>
                            <a href="students.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 <?= basename($_SERVER['PHP_SELF']) == 'students.php' ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : '' ?>">
                                <i class="fas fa-user-graduate mr-3 text-red-500"></i>
                                Data Siswa (7KAIH)
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (Auth::canEditContent()): ?>
                        <li>
                            <a href="info.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 <?= basename($_SERVER['PHP_SELF']) == 'info.php' ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : '' ?>">
                                <i class="fas fa-info-circle mr-3 text-gray-400"></i>
                                Informasi Umum
                                <?php if (Auth::isReadOnly()): ?>
                                <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Read-only</span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (Auth::canEditContent()): ?>
                        <li>
                            <a href="innovation.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 <?= basename($_SERVER['PHP_SELF']) == 'innovation.php' ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : '' ?>">
                                <i class="fas fa-lightbulb mr-3 text-gray-400"></i>
                                Inovasi
                                <?php if (Auth::isReadOnly()): ?>
                                <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Read-only</span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (false && Auth::canViewSettings()): // Sembunyikan menu transparansi sesuai permintaan ?>
                        <li>
                            <a href="transparansi.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 <?= basename($_SERVER['PHP_SELF']) == 'transparansi.php' ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : '' ?>">
                                <i class="fas fa-balance-scale mr-3 text-gray-400"></i>
                                Transparansi
                                <?php if (Auth::isReadOnly()): ?>
                                <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Read-only</span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (Auth::canManageMessages()): ?>
                        <li>
                            <a href="messages.php" class="flex items-center justify-between px-3 py-2 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 <?= basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : '' ?>">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope mr-3 text-gray-400"></i>
                                    Pesan Kontak
                                    <?php if (Auth::isReadOnly()): ?>
                                    <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Read-only</span>
                                    <?php endif; ?>
                                </div>
                                <?php 
                                try {
                                    if (class_exists('ContactMessage') && isset($db)) {
                                        $msg_check = new ContactMessage($db);
                                        $stats_check = $msg_check->getStats();
                                        if ($stats_check['unread'] > 0) {
                                            echo '<span class="bg-red-500 text-white text-xs rounded-full px-2 py-1 min-w-[1.25rem] text-center">' . $stats_check['unread'] . '</span>';
                                        }
                                    }
                                } catch (Exception $e) {
                                    // Silently fail if there's an error
                                }
                                ?>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (Auth::canEditContent()): ?>
                        <li>
                            <a href="media.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 <?= basename($_SERVER['PHP_SELF']) == 'media.php' ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : '' ?>">
                                <i class="fas fa-images mr-3 text-gray-400"></i>
                                Galeri Media
                                <?php if (Auth::isReadOnly()): ?>
                                <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Read-only</span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Admin Only Sections -->
                        <?php if (Auth::canManageUsers()): ?>
                        <li class="pt-4 mt-4 border-t border-gray-200">
                            <a href="users.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : '' ?>">
                                <i class="fas fa-users mr-3 text-gray-400"></i>
                                User Management
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (Auth::canViewSettings()): ?>
                        <li>
                            <a href="settings.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : '' ?>">
                                <i class="fas fa-cog mr-3 text-gray-400"></i>
                                Pengaturan
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <!-- User Info -->
                <div class="px-4 py-3 border-t border-gray-200">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 bg-primary-100 rounded-full">
                            <i class="fas fa-user text-primary-600 text-sm"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($current_user['name']) ?></p>
                            <div class="flex items-center">
                                <?= Auth::getRoleBadge($current_user['role']) ?>
                            </div>
                        </div>
                        <a href="logout.php" class="text-gray-400 hover:text-gray-600" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button type="button" class="lg:hidden -ml-2 mr-2 p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100" onclick="toggleMobileSidebar()">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($page_title) ?></h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Role indicator -->
                        <div class="hidden sm:block">
                            <?= Auth::getRoleBadge($current_user['role']) ?>
                            <?php if (Auth::isReadOnly()): ?>
                            <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                                <i class="fas fa-eye mr-1"></i>Read-Only Mode
                            </span>
                            <?php endif; ?>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative">
                            <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-primary-500" onclick="toggleProfileMenu()">
                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-primary-600 text-sm"></i>
                                </div>
                                <span class="ml-2 text-gray-700 hidden md:block"><?= htmlspecialchars($current_user['name']) ?></span>
                                <i class="fas fa-chevron-down ml-2 text-gray-400 text-xs"></i>
                            </button>
                            
                            <div id="profile-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-circle mr-2"></i>Profil Saya
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <?php 
                // Display flash messages
                $flash = Auth::getFlashMessage();
                if ($flash): ?>
                <div id="flash-message" class="mb-6 p-4 rounded-lg <?= $flash['type'] === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-green-100 text-green-700 border border-green-300' ?>">
                    <div class="flex items-center">
                        <i class="fas <?= $flash['type'] === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle' ?> mr-2"></i>
                        <span><?= htmlspecialchars($flash['message']) ?></span>
                        <button onclick="document.getElementById('flash-message').style.display='none'" class="ml-auto text-lg font-semibold">&times;</button>
                    </div>
                </div>
                <?php endif; ?>

    <script>
        function toggleProfileMenu() {
            const menu = document.getElementById('profile-menu');
            menu.classList.toggle('hidden');
        }

        function toggleMobileSidebar() {
            // Mobile sidebar toggle logic
            console.log('Toggle mobile sidebar');
        }

        // Close profile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('profile-menu');
            const button = event.target.closest('[onclick="toggleProfileMenu()"]');
            
            if (!button && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // Auto hide flash messages
        setTimeout(() => {
            const flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                flashMessage.style.display = 'none';
            }
        }, 5000);
    </script>
