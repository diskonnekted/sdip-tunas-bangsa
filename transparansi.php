<?php
require_once 'admin/config/database.php';
require_once 'admin/models/Transparency.php';
require_once 'includes/settings.php';

// Get school info from database settings
$school_info = getSchoolInfo();
$contact_info = getContactInfo();
$social_media = getSocialMedia();
$page_title = 'Transparansi';

// Get parameters
$section_filter = $_GET['section'] ?? '';
$search_query = $_GET['search'] ?? '';

// Section type names and icons
$section_info = [
    'financial' => ['name' => 'Laporan Keuangan', 'icon' => 'fas fa-chart-pie', 'color' => '#22c55e'],
    'budget' => ['name' => 'Anggaran Sekolah', 'icon' => 'fas fa-calculator', 'color' => '#8b5cf6'],
    'governance' => ['name' => 'Tata Kelola', 'icon' => 'fas fa-users-cog', 'color' => '#06b6d4'],
    'reports' => ['name' => 'Laporan Berkala', 'icon' => 'fas fa-file-alt', 'color' => '#10b981'],
    'policies' => ['name' => 'Kebijakan', 'icon' => 'fas fa-balance-scale', 'color' => '#f59e0b'],
    'procurement' => ['name' => 'Pengadaan', 'icon' => 'fas fa-shopping-cart', 'color' => '#ef4444'],
    'other' => ['name' => 'Lainnya', 'icon' => 'fas fa-folder', 'color' => '#6b7280']
];

try {
    $database = new Database();
    $db = $database->getConnection();
    $transparency = new Transparency($db);
    
    // Get all active transparency data
    $all_transparencies = $transparency->getAll($section_filter);
    
    // Apply search if provided
    if (!empty($search_query)) {
        $all_transparencies = array_filter($all_transparencies, function($item) use ($search_query) {
            return stripos($item['title'], $search_query) !== false || 
                   stripos($item['content'], $search_query) !== false;
        });
    }
    
    // Group by section type
    $grouped_data = [];
    foreach ($all_transparencies as $item) {
        $grouped_data[$item['section_type']][] = $item;
    }
    
    // Get statistics
    $stats = $transparency->getStats();
    
} catch (Exception $e) {
    error_log("Error in transparansi.php: " . $e->getMessage());
    $all_transparencies = [];
    $grouped_data = [];
    $stats = ['total' => 0, 'active' => 0];
}
?>
<?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <h1>Transparansi Sekolah</h1>
                <p>Keterbukaan informasi dan akuntabilitas untuk kepercayaan publik</p>
                <nav class="breadcrumb">
                    <a href="index.php">Beranda</a>
                    <span>/</span>
                    <a href="info.php">Info</a>
                    <span>/</span>
                    <span>Transparansi</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Search & Filter Section -->
    <section class="py-8 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <form method="GET" class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-64">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-search mr-2"></i>Cari Informasi
                            </label>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" 
                                   placeholder="Cari laporan, dokumen, atau informasi..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        
                        <div class="min-w-48">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-filter mr-2"></i>Kategori
                            </label>
                            <select name="section" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($section_info as $type => $info): ?>
                                    <option value="<?php echo $type; ?>" <?php echo $section_filter === $type ? 'selected' : ''; ?>>
                                        <?php echo $info['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-search mr-2"></i>Cari
                            </button>
                        </div>
                        
                        <?php if (!empty($search_query) || !empty($section_filter)): ?>
                        <div>
                            <a href="transparansi.php" class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg border border-gray-300 transition-colors">
                                <i class="fas fa-times mr-2"></i>Reset
                            </a>
                        </div>
                        <?php endif; ?>
                    </form>
                    
                    <?php if (!empty($search_query) || !empty($section_filter)): ?>
                    <div class="mt-4 text-sm text-gray-600">
                        Menampilkan <?php echo count($all_transparencies); ?> hasil
                        <?php if (!empty($search_query)): ?>
                            untuk "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
                        <?php endif; ?>
                        <?php if (!empty($section_filter)): ?>
                            dalam kategori <strong><?php echo $section_info[$section_filter]['name'] ?? $section_filter; ?></strong>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Overview -->
    <section class="py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-50 to-white -z-10"></div>
        <!-- Decorative blobs -->
        <div class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-green-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
        <div class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 w-96 h-96 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16">
                <span class="inline-block py-1 px-3 rounded-full bg-green-100 text-green-700 text-sm font-semibold mb-4 tracking-wide uppercase">Statistik & Kinerja</span>
                <h2 class="text-4xl font-extrabold text-gray-900 mb-6 tracking-tight leading-tight">
                    Ringkasan <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-green-600">Transparansi</span>
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto leading-relaxed">
                    Komitmen kami untuk memberikan informasi yang terbuka, akurat, dan dapat dipertanggungjawabkan kepada publik.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Card 1 -->
                <div class="group relative bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-green-50 rounded-full blur-2xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 text-white flex items-center justify-center shadow-lg shadow-green-500/30 transform group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-file-alt text-2xl"></i>
                            </div>
                            <span class="text-green-100 bg-green-50 px-3 py-1 rounded-full text-xs font-bold text-green-600">
                                Updated
                            </span>
                        </div>
                        <h3 class="text-5xl font-extrabold text-gray-900 mb-2 tracking-tight group-hover:text-green-600 transition-colors">
                            <?php echo $stats['active'] ?? 0; ?>
                        </h3>
                        <p class="text-gray-500 font-medium text-lg group-hover:text-gray-700">Dokumen Aktif</p>
                    </div>
                </div>
                
                <!-- Card 2 -->
                <div class="group relative bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-emerald-50 rounded-full blur-2xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30 transform group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-layer-group text-2xl"></i>
                            </div>
                            <span class="text-emerald-100 bg-emerald-50 px-3 py-1 rounded-full text-xs font-bold text-emerald-600">
                                Terverifikasi
                            </span>
                        </div>
                        <h3 class="text-5xl font-extrabold text-gray-900 mb-2 tracking-tight group-hover:text-emerald-600 transition-colors">
                            <?php echo count($section_info); ?>
                        </h3>
                        <p class="text-gray-500 font-medium text-lg group-hover:text-gray-700">Kategori Informasi</p>
                    </div>
                </div>
                
                <!-- Card 3 -->
                <div class="group relative bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-violet-50 rounded-full blur-2xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 text-white flex items-center justify-center shadow-lg shadow-violet-500/30 transform group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-clock text-2xl"></i>
                            </div>
                            <span class="text-violet-100 bg-violet-50 px-3 py-1 rounded-full text-xs font-bold text-violet-600">
                                Online
                            </span>
                        </div>
                        <h3 class="text-5xl font-extrabold text-gray-900 mb-2 tracking-tight group-hover:text-violet-600 transition-colors">
                            24/7
                        </h3>
                        <p class="text-gray-500 font-medium text-lg group-hover:text-gray-700">Akses Publik</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Integrity Assessment (DESAKTI) Integration -->
    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="bg-gradient-to-r from-green-50 to-green-50 rounded-2xl p-8 md:p-12 shadow-sm border border-green-100">
                <div class="flex flex-col md:flex-row items-center justify-between mb-8">
                    <div class="mb-6 md:mb-0 md:pr-8 w-full">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mb-4">
                            <i class="fas fa-shield-alt mr-2"></i>Integritas Sekolah
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Penilaian Integritas (DESAKTI)</h2>
                        <p class="text-gray-600 mb-6 text-lg">
                            Transparansi capaian pemenuhan indikator Desa Antikorupsi dan Tata Kelola Sekolah Berintegritas.
                            Silakan cek nilai integritas sekolah kami melalui jendela di bawah ini atau kunjungi portal resmi.
                        </p>
                        
                        <div class="flex flex-wrap gap-4 items-center">
                            <div class="bg-white px-4 py-3 rounded-lg border border-gray-200 flex items-center shadow-sm">
                                <span class="text-sm text-gray-500 mr-2">NPSN Sekolah:</span>
                                <span class="font-mono font-bold text-gray-900 text-lg"><?php echo htmlspecialchars($school_info['npsn'] ?? '-'); ?></span>
                                <button onclick="navigator.clipboard.writeText('<?php echo htmlspecialchars($school_info['npsn'] ?? ''); ?>'); alert('NPSN disalin!');" class="ml-3 text-green-600 hover:text-green-800 transition-colors" title="Salin NPSN">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                            <a href="https://desakti.banjarnegarakab.go.id/front/hasil" target="_blank" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition-colors shadow-sm">
                                Buka Portal DESAKTI <i class="fas fa-external-link-alt ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- External Link View (Replaced Iframe due to Security Policy) -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 mx-auto mb-6 bg-green-100 rounded-full flex items-center justify-center animate-pulse">
                            <i class="fas fa-external-link-alt text-green-600 text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Akses Portal Penilaian</h3>
                        <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
                            Demi alasan keamanan dan privasi data, portal penilaian DESAKTI tidak dapat ditampilkan langsung di halaman ini. 
                            Silakan ikuti langkah mudah berikut untuk melihat nilai integritas sekolah:
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto mb-8 text-left">
                            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 relative">
                                <div class="absolute -top-3 -left-3 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold shadow-sm">1</div>
                                <h4 class="font-semibold text-gray-900 mb-2">Salin NPSN</h4>
                                <p class="text-sm text-gray-600 mb-3">Salin Nomor Pokok Sekolah Nasional (NPSN) kami.</p>
                                <div class="flex items-center bg-white border border-gray-300 rounded px-3 py-2">
                                    <span class="font-mono font-bold text-gray-800 flex-1"><?php echo htmlspecialchars($school_info['npsn'] ?? '-'); ?></span>
                                    <button onclick="navigator.clipboard.writeText('<?php echo htmlspecialchars($school_info['npsn'] ?? ''); ?>'); alert('NPSN disalin!');" class="text-green-600 hover:text-green-800" title="Salin">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 relative">
                                <div class="absolute -top-3 -left-3 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold shadow-sm">2</div>
                                <h4 class="font-semibold text-gray-900 mb-2">Buka Portal</h4>
                                <p class="text-sm text-gray-600 mb-3">Klik tombol di bawah untuk membuka portal resmi DESAKTI di tab baru.</p>
                                <a href="https://desakti.banjarnegarakab.go.id/front/hasil" target="_blank" class="text-green-600 hover:text-green-800 text-sm font-medium inline-flex items-center">
                                    Buka sekarang <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                            
                            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 relative">
                                <div class="absolute -top-3 -left-3 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold shadow-sm">3</div>
                                <h4 class="font-semibold text-gray-900 mb-2">Cari Data</h4>
                                <p class="text-sm text-gray-600">Tempel (Paste) NPSN pada kolom pencarian di website DESAKTI untuk melihat hasil.</p>
                            </div>
                        </div>

                        <a href="https://desakti.banjarnegarakab.go.id/front/hasil" target="_blank" class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-xl text-white bg-gradient-to-r from-green-600 to-green-600 hover:from-green-700 hover:to-green-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-search-location mr-2"></i> Buka Portal DESAKTI Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Transparency Content by Categories -->
    <?php if (empty($all_transparencies)): ?>
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-search text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Data</h3>
                    <p class="text-gray-600">
                        <?php if (!empty($search_query) || !empty($section_filter)): ?>
                            Tidak ditemukan hasil yang sesuai dengan pencarian Anda.
                        <?php else: ?>
                            Belum ada informasi transparansi yang tersedia saat ini.
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($search_query) || !empty($section_filter)): ?>
                    <a href="transparansi.php" class="inline-block mt-4 text-green-600 hover:text-green-800">
                        Lihat semua informasi Ã¢â€ â€™
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php else: ?>
        <?php
            // Flatten all transparencies into a single grid so cards flow across categories
            $flat_items = $all_transparencies;
        ?>

        <section class="py-12 bg-white">
            <div class="container mx-auto px-4">
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Semua Informasi Transparansi</h2>
                            <p class="text-gray-600">Menampilkan semua dokumen dari berbagai kategori.</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <?php foreach ($section_info as $key => $s): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-gray-700 bg-gray-100" style="color: <?php echo $s['color']; ?>;">
                                    <i class="<?php echo $s['icon']; ?> mr-2"></i><?php echo $s['name']; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($flat_items as $item):
                        $info = $section_info[$item['section_type']] ?? ['name' => ucfirst($item['section_type']), 'icon' => 'fas fa-folder', 'color' => '#6b7280'];
                    ?>
                    <div class="group relative bg-white rounded-2xl border border-gray-100 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                        <span class="absolute inset-x-0 top-0 h-1 opacity-0 group-hover:opacity-100 transition-opacity" style="background-color: <?php echo $info['color']; ?>;"></span>
                        <div class="p-3 md:p-6">
                            <div class="flex justify-between items-start mb-3 md:mb-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl flex items-center justify-center" style="background-color: <?php echo $info['color']; ?>20; color: <?php echo $info['color']; ?>;">
                                        <i class="<?php echo $info['icon']; ?> text-sm md:text-base"></i>
                                    </div>
                                    <h3 class="text-base md:text-xl font-semibold tracking-tight text-gray-900 leading-snug line-clamp-2 md:line-clamp-3">
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </h3>
                                </div>
                                <?php if ($item['file_attachment']): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 ring-1 ring-green-600/10">
                                    <i class="fas fa-paperclip mr-1"></i>File
                                </span>
                                <?php endif; ?>
                            </div>

                            <div class="text-gray-600 mb-4 md:mb-6 line-clamp-3 text-sm md:text-base">
                                <?php 
                                $content_preview = strip_tags($item['content']);
                                echo htmlspecialchars(strlen($content_preview) > 200 ? substr($content_preview, 0, 200) . '...' : $content_preview);
                                ?>
                            </div>

                            <div class="mt-4 md:mt-6 border-t border-gray-100 pt-3 md:pt-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 text-gray-600 text-[11px] md:text-xs font-medium">
                                        <i class="fas fa-calendar mr-1"></i>
                                        <?php echo date('d F Y', strtotime($item['created_at'])); ?>
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full bg-<?php echo 'white'; ?> text-sm text-gray-700" style="border:1px solid <?php echo $info['color']; ?>20;color:<?php echo $info['color']; ?>;">
                                        <?php echo $info['name']; ?>
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button onclick="viewTransparencyDetail(<?php echo $item['id']; ?>)" 
                                            class="inline-flex items-center px-2.5 py-1.5 md:px-3 md:py-2 rounded-lg text-green-700 bg-green-50 hover:bg-green-100 text-xs md:text-sm font-medium transition-colors shadow-sm">
                                        <i class="fas fa-eye mr-2"></i>Lihat
                                    </button>

                                    <?php if ($item['file_attachment']): ?>
                                    <a href="admin/uploads/attachments/<?php echo htmlspecialchars($item['file_attachment']); ?>" 
                                       target="_blank" download
                                       class="inline-flex items-center px-2.5 py-1.5 md:px-3 md:py-2 rounded-lg text-green-700 bg-green-50 hover:bg-green-100 text-xs md:text-sm font-medium transition-colors shadow-sm">
                                        <i class="fas fa-download mr-2"></i>Unduh
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Contact for Transparency -->
    <section class="py-16 bg-green-600 text-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h3 class="text-3xl font-bold mb-6">Butuh Informasi Lebih Lanjut?</h3>
                        <p class="text-green-100 mb-8 text-lg">Kami berkomitmen untuk memberikan informasi yang transparan dan mudah diakses. Hubungi kami untuk mendapatkan dokumen atau penjelasan lebih detail.</p>
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <i class="fas fa-user-tie text-green-200 text-xl mr-4 w-6"></i>
                                <div>
                                    <div class="font-semibold">Koordinator Transparansi</div>
                                    <div class="text-green-100">Dra. Siti Nurlaela, M.Pd</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-green-200 text-xl mr-4 w-6"></i>
                                <div>
                                    <div class="font-semibold">Email</div>
                                    <div class="text-green-100"><?php echo htmlspecialchars($contact_info['email']); ?></div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-phone text-green-200 text-xl mr-4 w-6"></i>
                                <div>
                                    <div class="font-semibold">Telepon</div>
                                    <div class="text-green-100"><?php echo htmlspecialchars($contact_info['phone']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-8 text-gray-900">
                        <h4 class="text-xl font-semibold mb-6">Ajukan Pertanyaan</h4>
                        <form class="space-y-4" id="transparencyForm">
                            <div>
                                <input type="text" name="name" placeholder="Nama Lengkap" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                            <div>
                                <input type="email" name="email" placeholder="Email" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                            <div>
                                <select name="category" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="">Pilih Kategori Informasi</option>
                                    <option value="financial">Laporan Keuangan</option>
                                    <option value="budget">Anggaran Sekolah</option>
                                    <option value="governance">Tata Kelola</option>
                                    <option value="reports">Laporan Berkala</option>
                                    <option value="policies">Kebijakan</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <textarea name="message" rows="4" placeholder="Pertanyaan atau informasi yang dibutuhkan" required
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i>Kirim Pertanyaan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Detail Informasi</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="p-6" id="modalContent">
                    <div class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-green-500 text-2xl"></i>
                        <p class="mt-2 text-gray-600">Memuat...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // View transparency detail
        function viewTransparencyDetail(id) {
            document.getElementById('detailModal').classList.remove('hidden');
            document.getElementById('modalContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-green-500 text-2xl"></i>
                    <p class="mt-2 text-gray-600">Memuat...</p>
                </div>
            `;
            
            fetch(`transparansi_api.php?action=view&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = data.data;
                        document.getElementById('modalTitle').textContent = item.title;
                        document.getElementById('modalContent').innerHTML = `
                            <div class="space-y-6">
                                <div class="flex flex-wrap items-center gap-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-tag mr-2"></i>${item.section_name}
                                    </span>
                                    <span class="text-gray-500">
                                        <i class="fas fa-calendar mr-2"></i>${item.formatted_date}
                                    </span>
                                    ${item.has_file ? `
                                    <a href="${item.file_url}" target="_blank" download 
                                       class="inline-flex items-center text-green-600 hover:text-green-800">
                                        <i class="fas fa-download mr-2"></i>Unduh File
                                    </a>
                                    ` : ''}
                                </div>
                                
                                <div class="prose max-w-none">
                                    <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                                        ${item.content}
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        document.getElementById('modalContent').innerHTML = `
                            <div class="text-center py-8">
                                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                                <p class="mt-2 text-gray-600">${data.message || 'Terjadi kesalahan'}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    document.getElementById('modalContent').innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                            <p class="mt-2 text-gray-600">Terjadi kesalahan saat memuat data</p>
                        </div>
                    `;
                });
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // Close modal on ESC key or outside click
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Form submission
        document.getElementById('transparencyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Add form submission logic here
            alert('Terima kasih! Pertanyaan Anda akan segera kami respons.');
            this.reset();
        });

    </script>
</body>
</html>
