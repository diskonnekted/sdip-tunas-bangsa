<?php
// Include settings if not already included
if (!class_exists('Settings')) {
    require_once __DIR__ . '/settings.php';
}

// Get school info
    $school_info = getSchoolInfo();
    $page_title = $page_title ?? 'SDIP Tunas Bangsa';

    // Current page logic
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Navigation Theme Logic
    $nav_theme = $nav_theme ?? 'dark';
    
    if ($nav_theme === 'light') {
        $logo_text_color = 'text-slate-800';
        $logo_subtext_color = 'text-slate-500';
        $mobile_btn_color = 'text-slate-800';
        $menu_inactive_text = 'text-slate-600';
        $menu_hover_class = 'hover:bg-slate-100 hover:text-primary-600';
        $glass_class = 'bg-white/80 backdrop-blur-md border-b border-slate-200/50 shadow-sm';
        $mobile_btn_hover = 'hover:bg-slate-100';
    } else {
        $logo_text_color = 'text-white';
        $logo_subtext_color = 'text-gray-200';
        $mobile_btn_color = 'text-white';
        $menu_inactive_text = 'text-gray-100';
        $menu_hover_class = 'hover:bg-white/20 hover:text-white';
        $glass_class = 'glass-effect';
        $mobile_btn_hover = 'hover:bg-white/20';
    }
    
    // Helper function for active class
    function getMenuClass($page_name, $current_page) {
        global $menu_inactive_text, $menu_hover_class;
        $active_class = "bg-primary-500 text-white shadow-lg hover:bg-primary-600 hover-lift";
        $inactive_class = "$menu_inactive_text $menu_hover_class";
        
        // Handle array of pages (for dropdowns)
        if (is_array($page_name)) {
            return in_array($current_page, $page_name) ? $active_class : $inactive_class;
        }
        
        return ($current_page === $page_name) ? $active_class : $inactive_class;
    }
    
    // Check if dropdown is active
    function isDropdownActive($pages, $current_page) {
        return in_array($current_page, $pages);
    }

    // Helper function for mobile menu class
    function getMobileMenuClass($page_name, $current_page) {
        $active_class = "font-semibold bg-primary-500 text-white";
        $inactive_class = "font-medium text-gray-700 hover:bg-primary-50 transition-all duration-300";
        
        return ($current_page === $page_name) ? $active_class : $inactive_class;
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title . ' - ' . $school_info['name']); ?></title>
    <?php include 'includes/favicon.php'; ?>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Amiri:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'jakarta': ['Plus Jakarta Sans', 'sans-serif'],
                        'amiri': ['Amiri', 'serif']
                    },
                    colors: {
                        'primary': {
                            50: '#f0fdf4',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d'
                        },
                        'accent': {
                            500: '#06d6a0',
                            600: '#05c195'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hover-lift:hover { transform: translateY(-5px); transition: all 0.3s ease; }
    </style>
    <?php if (isset($extra_head)) echo $extra_head; ?>
</head>
<body class="<?php echo isset($body_class) ? $body_class : ''; ?>">
    <!-- Modern Navigation -->
    <nav class="fixed top-0 w-full z-50 transition-all duration-300" id="navbar">
        <div class="<?php echo $glass_class; ?>">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <?php if (!empty($school_info['logo'])): ?>
                                <img src="admin/uploads/<?php echo htmlspecialchars($school_info['logo']); ?>" alt="Logo" class="w-12 h-12 rounded-2xl shadow-lg object-cover bg-white p-1">
                            <?php else: ?>
                                <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold <?php echo $logo_text_color; ?>">
                                <?php echo htmlspecialchars($school_info['name']); ?>
                            </h1>
                            <p class="text-xs <?php echo $logo_subtext_color; ?> font-medium">Modern Education</p>
                        </div>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden lg:flex items-center space-x-1">
                        <a href="index.php" class="nav-link px-4 py-2 rounded-full text-sm font-medium <?php echo getMenuClass('index.php', $current_page); ?> transition-all duration-300">Beranda</a>
                        <div class="relative group">
                            <a href="profil.php" class="nav-link px-4 py-2 rounded-full text-sm font-medium <?php echo getMenuClass(['profil.php', 'sejarah.php', 'guru.php', 'staf.php'], $current_page); ?> transition-all duration-300 flex items-center">Profil <i class="fas fa-chevron-down ml-1 text-xs"></i></a>
                            <div class="absolute top-full left-0 mt-2 w-48 bg-white/90 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                                <a href="profil.php" class="block px-4 py-3 text-sm font-medium <?php echo ($current_page == 'profil.php') ? 'text-primary-600 bg-primary-50' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-600'; ?> rounded-t-2xl transition-all duration-300"><i class="fas fa-school mr-2"></i>Profil Sekolah</a>
                                <a href="sejarah.php" class="block px-4 py-3 text-sm font-medium <?php echo ($current_page == 'sejarah.php') ? 'text-primary-600 bg-primary-50' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-600'; ?> transition-all duration-300"><i class="fas fa-history mr-2"></i>Sejarah</a>
                                <a href="guru.php" class="block px-4 py-3 text-sm font-medium <?php echo ($current_page == 'guru.php') ? 'text-primary-600 bg-primary-50' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-600'; ?> transition-all duration-300"><i class="fas fa-chalkboard-teacher mr-2"></i>Guru</a>
                                <a href="staf.php" class="block px-4 py-3 text-sm font-medium <?php echo ($current_page == 'staf.php') ? 'text-primary-600 bg-primary-50' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-600'; ?> rounded-b-2xl transition-all duration-300"><i class="fas fa-users mr-2"></i>Staf</a>
                            </div>
                        </div>
                        <a href="berita.php" class="nav-link px-4 py-2 rounded-full text-sm font-medium <?php echo getMenuClass('berita.php', $current_page); ?> transition-all duration-300">Berita</a>
                        <a href="academic.php" class="nav-link px-4 py-2 rounded-full text-sm font-medium <?php echo getMenuClass('academic.php', $current_page); ?> transition-all duration-300">Akademik</a>
                        <a href="interaktif.php" class="nav-link px-4 py-2 rounded-full text-sm font-medium <?php echo getMenuClass('interaktif.php', $current_page); ?> transition-all duration-300">Interactive</a>
                        <a href="info.php" class="nav-link px-4 py-2 rounded-full text-sm font-medium <?php echo getMenuClass('info.php', $current_page); ?> transition-all duration-300">Info</a>
                        <a href="inovasi.php" class="nav-link px-4 py-2 rounded-full text-sm font-medium <?php echo getMenuClass('inovasi.php', $current_page); ?> transition-all duration-300">Inovasi</a>
                        <a href="contact.php" class="nav-link px-4 py-2 rounded-full text-sm font-medium <?php echo getMenuClass('contact.php', $current_page); ?> transition-all duration-300">Kontak</a>
                        <a href="parent/login.php" class="nav-link px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-red-600 to-red-800 text-white hover:from-red-700 hover:to-red-900 transition-all duration-300 shadow-lg shadow-red-500/30">7KAIH</a>
                        <a href="ppdb.php" class="nav-link px-4 py-2 rounded-full text-sm font-bold bg-primary-600 text-white hover:bg-primary-700 transition-all duration-300 shadow-lg hover:shadow-primary-500/30">PPDB Online</a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button class="lg:hidden p-2 rounded-full <?php echo $mobile_btn_hover; ?> transition-all duration-300 <?php echo $mobile_btn_color; ?>" id="mobile-menu-button">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div class="lg:hidden bg-white/95 backdrop-blur-md border-t border-white/20 hidden" id="mobile-menu">
                <div class="px-4 py-6 space-y-3">
                    <a href="index.php" class="block px-4 py-3 rounded-2xl text-sm <?php echo getMobileMenuClass('index.php', $current_page); ?>">Beranda</a>
                    
                    <!-- Profil Dropdown for Mobile -->
                    <div class="relative">
                        <button class="w-full text-left px-4 py-3 rounded-2xl text-sm <?php echo getMobileMenuClass(['profil.php', 'sejarah.php', 'guru.php', 'staf.php'], $current_page); ?> flex justify-between items-center" onclick="this.nextElementSibling.classList.toggle('hidden')">
                            Profil <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="hidden pl-4 space-y-2 mt-2">
                            <a href="profil.php" class="block px-4 py-2 rounded-xl text-sm <?php echo getMobileMenuClass('profil.php', $current_page); ?>">Profil Sekolah</a>
                            <a href="sejarah.php" class="block px-4 py-2 rounded-xl text-sm <?php echo getMobileMenuClass('sejarah.php', $current_page); ?>">Sejarah</a>
                            <a href="guru.php" class="block px-4 py-2 rounded-xl text-sm <?php echo getMobileMenuClass('guru.php', $current_page); ?>">Guru</a>
                            <a href="staf.php" class="block px-4 py-2 rounded-xl text-sm <?php echo getMobileMenuClass('staf.php', $current_page); ?>">Staf</a>
                        </div>
                    </div>

                    <a href="berita.php" class="block px-4 py-3 rounded-2xl text-sm <?php echo getMobileMenuClass('berita.php', $current_page); ?>">Berita</a>
                    <a href="academic.php" class="block px-4 py-3 rounded-2xl text-sm <?php echo getMobileMenuClass('academic.php', $current_page); ?>">Akademik</a>
                    <a href="interaktif.php" class="block px-4 py-3 rounded-2xl text-sm <?php echo getMobileMenuClass('interaktif.php', $current_page); ?>">Interactive</a>
                    <a href="info.php" class="block px-4 py-3 rounded-2xl text-sm <?php echo getMobileMenuClass('info.php', $current_page); ?>">Info</a>
                    <a href="inovasi.php" class="block px-4 py-3 rounded-2xl text-sm <?php echo getMobileMenuClass('inovasi.php', $current_page); ?>">Inovasi</a>
                    <a href="contact.php" class="block px-4 py-3 rounded-2xl text-sm <?php echo getMobileMenuClass('contact.php', $current_page); ?>">Kontak</a>
                    <a href="parent/login.php" class="block px-4 py-3 rounded-2xl text-sm font-bold bg-gradient-to-r from-red-600 to-red-800 text-white text-center shadow-md">Portal Wali 7KAIH</a>
                    <a href="ppdb.php" class="block px-4 py-3 rounded-2xl text-sm font-bold bg-primary-600 text-white hover:bg-primary-700 text-center shadow-md">PPDB Online</a>
                </div>
            </div>
        </div>
    </nav>
