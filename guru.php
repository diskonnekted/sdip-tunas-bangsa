<?php
require_once 'includes/settings.php';
require_once 'admin/config/database.php';
require_once 'admin/models/User.php';

$page_title = 'Profil Guru';
$school_info = getSchoolInfo();

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

function getTeacherPhotoPath($user) {
    $baseDir = 'admin/uploads/teachers';
    $candidates = [
        $baseDir . '/' . $user['id'] . '.jpg',
        $baseDir . '/' . $user['id'] . '.png',
        $baseDir . '/' . $user['id'] . '.jpeg',
        $baseDir . '/' . $user['username'] . '.jpg',
        $baseDir . '/' . $user['username'] . '.png',
        $baseDir . '/' . $user['username'] . '.jpeg'
    ];
    foreach ($candidates as $path) {
        if (file_exists($path)) return $path;
    }
    return null;
}

// Ambil data guru aktif memakai model agar adaptif terhadap skema (subject/teacher_profiles)
$teachers = $userModel->getAll(User::ROLE_GURU, 'active', 200, 0, '');
if (empty($teachers)) {
    $teachers = $userModel->getAll(User::ROLE_GURU, '', 200, 0, '');
}
if (empty($teachers)) {
    try {
        $stmt = $db->prepare("SELECT au.id, au.username, au.full_name, au.email, tp.subject, tp.photo_filename, tp.bio
                               FROM admin_users au LEFT JOIN teacher_profiles tp ON tp.user_id = au.id
                               WHERE au.is_active = 1 AND (
                                    LOWER(TRIM(au.role)) = 'guru' OR tp.user_id IS NOT NULL
                               ) AND LOWER(TRIM(au.role)) NOT IN ('admin','super_admin','demo')
                               ORDER BY au.full_name ASC");
        $stmt->execute();
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // ignore and continue to next fallback
    }
}
if (empty($teachers)) {
    try {
        $stmt = $db->prepare("SELECT id, username, full_name, email FROM admin_users WHERE LOWER(TRIM(role)) = 'guru' AND (is_active = 1 OR LOWER(TRIM(is_active)) = 'active') ORDER BY full_name ASC");
        $stmt->execute();
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $teachers = [];
    }
}
if (empty($teachers)) {
    try {
        $stmt = $db->prepare("SELECT id, username, full_name, email, role, is_active FROM admin_users ORDER BY full_name ASC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $teachers = array_values(array_filter($rows, function($u) {
            return isset($u['role']) && strtolower(trim($u['role'])) === 'guru';
        }));
    } catch (Exception $e) {
        $teachers = [];
    }
}
?>
<?php include 'includes/header.php'; ?>

<section class="page-header">
    <i class="fas fa-chalkboard-teacher header-bg-icon"></i>
    <div class="container">
        <div class="page-header-content">
            <h1>Profil Guru</h1>
            <p>Daftar guru SDIP Tunas Bangsa beserta mata pelajaran yang diajarkan</p>
            <nav class="breadcrumb">
                <a href="index.php">Beranda</a>
                <span>/</span>
                <span>Guru</span>
            </nav>
        </div>
    </div>
</section>

<!-- Principal Section -->
<section class="principal-section py-16 bg-white">
    <div class="container">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/3 relative group">
                    <?php 
                    $principal_photo = !empty($school_info['principal_photo']) ? 'admin/uploads/' . $school_info['principal_photo'] : null;
                    if ($principal_photo && file_exists($principal_photo)): 
                    ?>
                        <img src="<?= htmlspecialchars($principal_photo) ?>" alt="Kepala Sekolah" class="w-full h-full object-cover min-h-[300px] md:min-h-[400px]">
                    <?php else: ?>
                        <div class="w-full h-full min-h-[300px] bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center">
                            <i class="fas fa-user-tie text-6xl text-primary-300"></i>
                        </div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                <div class="md:w-2/3 p-8 md:p-12 flex flex-col justify-center">
                    <div class="mb-6">
                        <span class="inline-block px-4 py-1.5 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-3">Kepala Sekolah</span>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($school_info['principal_name'] ?? 'Kepala Sekolah') ?></h2>
                        <div class="w-20 h-1.5 bg-primary-500 rounded-full"></div>
                    </div>
                    <blockquote class="text-gray-600 text-lg leading-relaxed italic mb-6">
                        "Pendidikan adalah tiket ke masa depan. Hari esok dimiliki oleh orang-orang yang mempersiapkan dirinya pada hari ini. Di SDIP Tunas Bangsa, kami berkomitmen untuk memberikan bekal terbaik bagi putra-putri Anda."
                    </blockquote>
                    <div class="flex items-center space-x-4">
                        <a href="#contact-teacher" class="px-6 py-2.5 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/30">
                            Hubungi Kepala Sekolah
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section py-16 bg-gray-50">
    <div class="container">
        <div class="section-header text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Tim Pengajar Kami</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Guru-guru berdedikasi dan berpengalaman yang siap membimbing siswa mencapai potensi terbaiknya</p>
        </div>

        <?php if (empty($teachers)): ?>
            <div class="text-center py-12 bg-white rounded-2xl shadow-sm p-8">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Data</h3>
                <p class="text-gray-500">Data guru belum tersedia saat ini.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($teachers as $t): ?>
                <?php 
                    $photo = null;
                    if (!empty($t['photo_filename'])) {
                        $path = 'admin/uploads/teachers/' . $t['photo_filename'];
                        if (file_exists($path)) {
                            $photo = $path;
                        }
                    }
                    if (!$photo) {
                        $photo = getTeacherPhotoPath($t);
                    }
                    $subject = $t['subject'] ?? 'Guru Kelas';
                ?>
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group hover:-translate-y-2 border border-gray-100">
                    <div class="aspect-square relative overflow-hidden bg-gray-100">
                        <?php if ($photo): ?>
                            <img src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($t['full_name']) ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-primary-50">
                                <i class="fas fa-user text-6xl text-primary-200"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-6">
                            <div class="flex space-x-3 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                <button type="button" 
                                    onclick="openTeacherModal(this)"
                                    data-name="<?= htmlspecialchars($t['full_name']) ?>"
                                    data-subject="<?= htmlspecialchars($subject) ?>"
                                    data-photo="<?= htmlspecialchars($photo ?? '') ?>"
                                    data-email="<?= htmlspecialchars($t['email']) ?>"
                                    data-id="<?= htmlspecialchars($t['id']) ?>"
                                    data-bio="<?= htmlspecialchars($t['bio'] ?? '') ?>"
                                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-primary-600 hover:bg-primary-600 hover:text-white transition-colors shadow-lg cursor-pointer" 
                                    title="Lihat Profil">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="mailto:<?= htmlspecialchars($t['email']) ?>" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-primary-600 hover:bg-primary-600 hover:text-white transition-colors shadow-lg" title="Kirim Email">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1" title="<?= htmlspecialchars($t['full_name']) ?>">
                            <?= htmlspecialchars($t['full_name']) ?>
                        </h3>
                        <div class="flex items-center text-primary-600 text-sm font-medium mb-3">
                            <i class="fas fa-book-open mr-2 text-xs"></i>
                            <span class="line-clamp-1"><?= htmlspecialchars($subject) ?></span>
                        </div>
                        <?php if (!empty($t['bio'])): ?>
                        <p class="text-gray-500 text-sm line-clamp-2 mb-4 h-10 leading-relaxed">
                            <?= strip_tags(htmlspecialchars_decode($t['bio'])) ?>
                        </p>
                        <?php endif; ?>
                        <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                            <span class="flex items-center"><i class="fas fa-id-card mr-1.5"></i> NIP/ID: <?= $t['id'] ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Contact Teacher Form Section -->
<!-- Contact Teacher Section -->
<section id="contact-teacher" class="py-24 bg-gradient-to-br from-orange-50 via-amber-50 to-orange-100 relative overflow-hidden">
    <!-- Decorative Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-orange-400/20 rounded-full blur-3xl opacity-60"></div>
        <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-white/40 to-transparent"></div>
        <div class="absolute top-1/2 left-10 w-64 h-64 bg-yellow-400/10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
            <!-- Left Side: Text & Info -->
            <div class="lg:w-5/12">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-orange-100 border border-orange-200 mb-6 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                    <span class="text-xs font-bold tracking-wide uppercase text-orange-700">Komunikasi Efektif</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold mb-6 leading-tight text-gray-900">Hubungi <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-amber-600">Guru Kami</span></h2>
                <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                    Kami percaya komunikasi yang baik antara guru dan orang tua adalah kunci keberhasilan siswa. Jangan ragu untuk menghubungi kami.
                </p>
                
                <div class="space-y-6 hidden md:block">
                    <div class="flex items-start space-x-4 p-4 rounded-2xl bg-white/60 border border-white/40 backdrop-blur-sm hover:bg-white/80 transition-all duration-300 hover:translate-x-2 shadow-sm">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center flex-shrink-0 text-white shadow-md">
                            <i class="fas fa-comments text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg text-gray-800">Konsultasi Akademik</h4>
                            <p class="text-sm text-gray-500">Diskusikan perkembangan belajar putra-putri Anda secara langsung.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4 p-4 rounded-2xl bg-white/60 border border-white/40 backdrop-blur-sm hover:bg-white/80 transition-all duration-300 hover:translate-x-2 shadow-sm">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center flex-shrink-0 text-white shadow-md">
                            <i class="fas fa-calendar-check text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg text-gray-800">Jadwal Temu</h4>
                            <p class="text-sm text-gray-500">Atur jadwal pertemuan tatap muka untuk diskusi yang lebih mendalam.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="lg:w-7/12 w-full">
                <div class="bg-white rounded-3xl shadow-xl shadow-orange-900/5 p-6 md:p-10 border border-orange-100 relative">
                    <!-- Decorative corner -->
                    <div class="absolute -top-6 -right-6 w-24 h-24 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full opacity-10 blur-xl"></div>
                    
                    <h3 class="text-2xl font-bold text-gray-800 mb-8 flex items-center relative z-10">
                        <span class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center mr-3 text-lg border border-orange-100">
                            <i class="fas fa-envelope-open-text"></i>
                        </span>
                        Kirim Pesan
                    </h3>

                    <form id="teacherContactForm" class="space-y-5 relative z-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="group">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-orange-600 transition-colors">Nama Orang Tua/Wali</label>
                                <input type="text" id="name" name="name" class="w-full px-5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none" placeholder="Nama Lengkap" required>
                            </div>
                            <div class="group">
                                <label for="student_name" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-orange-600 transition-colors">Nama Siswa</label>
                                <input type="text" id="student_name" name="student_name" class="w-full px-5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none" placeholder="Nama Siswa" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="group">
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-orange-600 transition-colors">Email</label>
                                <input type="email" id="email" name="email" class="w-full px-5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none" placeholder="contoh@email.com" required>
                            </div>
                            <div class="group">
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-orange-600 transition-colors">Nomor Telepon/WA</label>
                                <input type="tel" id="phone" name="phone" class="w-full px-5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none" placeholder="08xxxxxxxxxx" required>
                            </div>
                        </div>

                        <div class="group">
                            <label for="teacher_id" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-orange-600 transition-colors">Tujuan Guru</label>
                            <div class="relative">
                                <select id="teacher_id" name="teacher_id" class="w-full px-5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none appearance-none cursor-pointer" required>
                                    <option value="" disabled selected>Pilih Guru</option>
                                    <option value="principal">Kepala Sekolah</option>
                                    <?php if (!empty($teachers)): ?>
                                        <?php foreach ($teachers as $t): ?>
                                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['full_name']) ?> (<?= htmlspecialchars($t['subject'] ?? 'Guru Kelas') ?>)</option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="absolute right-5 top-1/2 transform -translate-y-1/2 pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <div class="group">
                            <label for="message" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-orange-600 transition-colors">Pesan</label>
                            <textarea id="message" name="message" rows="3" class="w-full px-5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none resize-none" placeholder="Tuliskan pesan Anda di sini..." required></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-bold rounded-xl hover:from-orange-600 hover:to-amber-700 focus:ring-4 focus:ring-orange-500/30 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                                <i class="fas fa-paper-plane mr-2.5"></i> Kirim Pesan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

</section>

<!-- Teacher Detail Modal -->
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
<div id="teacherModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdrop"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="modalPanel">
            <!-- Close Button -->
            <div class="absolute right-4 top-4 z-10">
                <button type="button" onclick="closeTeacherModal()" class="rounded-full bg-white/80 p-2 text-gray-400 hover:text-gray-500 hover:bg-white transition-all focus:outline-none">
                    <span class="sr-only">Close</span>
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="flex flex-col md:flex-row">
                <!-- Photo Section -->
                <div class="md:w-2/5 bg-gray-100 relative h-64 md:h-auto">
                    <img id="modalPhoto" src="" alt="" class="absolute inset-0 w-full h-full object-cover">
                    <div id="modalPhotoPlaceholder" class="absolute inset-0 flex items-center justify-center bg-primary-50 hidden">
                        <i class="fas fa-user text-6xl text-primary-200"></i>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="md:w-3/5 p-6 md:p-8">
                    <div class="mb-6">
                        <span id="modalSubject" class="inline-block px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-bold uppercase tracking-wide mb-2"></span>
                        <h3 id="modalName" class="text-2xl font-bold text-gray-900 mb-1"></h3>
                        <p id="modalNip" class="text-sm text-gray-500 flex items-center">
                            <i class="fas fa-id-card mr-2 text-gray-400"></i>
                            <span>NIP/ID: <span id="modalNipValue"></span></span>
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-2">Tentang Pengajar</h4>
                            <div id="modalBio" class="text-gray-600 text-sm leading-relaxed max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                                <!-- Bio content -->
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Kontak</h4>
                            <a id="modalEmailLink" href="#" class="flex items-center text-gray-600 hover:text-primary-600 transition-colors group">
                                <div class="w-8 h-8 rounded-full bg-gray-100 group-hover:bg-primary-50 flex items-center justify-center mr-3 transition-colors">
                                    <i class="fas fa-envelope text-gray-400 group-hover:text-primary-500"></i>
                                </div>
                                <span id="modalEmail"></span>
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                        <button type="button" onclick="closeTeacherModal()" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors mr-3">
                            Tutup
                        </button>
                        <a href="#contact-teacher" onclick="closeTeacherModal(); setTeacherSelect(document.getElementById('modalId').value)" class="px-5 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/30">
                            Kirim Pesan
                        </a>
                        <input type="hidden" id="modalId">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    // Modal Functions
    function openTeacherModal(button) {
        const modal = document.getElementById('teacherModal');
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('modalPanel');
        
        // Set Data
        document.getElementById('modalName').textContent = button.dataset.name;
        document.getElementById('modalSubject').textContent = button.dataset.subject;
        document.getElementById('modalNipValue').textContent = button.dataset.id;
        document.getElementById('modalEmail').textContent = button.dataset.email;
        document.getElementById('modalEmailLink').href = 'mailto:' + button.dataset.email;
        document.getElementById('modalBio').innerHTML = button.dataset.bio || 'Belum ada bio.';
        document.getElementById('modalId').value = button.dataset.id;

        // Photo handling
        const photo = button.dataset.photo;
        const img = document.getElementById('modalPhoto');
        const placeholder = document.getElementById('modalPhotoPlaceholder');
        
        if (photo) {
            img.src = photo;
            img.classList.remove('hidden');
            placeholder.classList.add('hidden');
        } else {
            img.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }

        // Show Modal
        modal.classList.remove('hidden');
        // Animate in
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'translate-y-4', 'scale-95');
        }, 10);
        
        document.body.style.overflow = 'hidden';
    }

    function closeTeacherModal() {
        const modal = document.getElementById('teacherModal');
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('modalPanel');

        // Animate out
        backdrop.classList.add('opacity-0');
        panel.classList.add('opacity-0', 'translate-y-4', 'scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    function setTeacherSelect(id) {
        const select = document.getElementById('teacher_id');
        select.value = id;
    }

    // Close on backdrop click
    document.getElementById('teacherModal').addEventListener('click', function(e) {
        if (e.target === this || e.target === document.getElementById('modalBackdrop')) {
            closeTeacherModal();
        }
    });

    // Form Submission
    document.getElementById('teacherContactForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...';

        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        // Handle teacher/principal
        if (data.teacher_id === 'principal') {
            data.recipient_type = 'principal';
            data.recipient_id = null;
        } else {
            data.recipient_type = 'teacher';
            data.recipient_id = data.teacher_id;
        }
        
        data.subject = 'Pesan untuk Guru/Staf';

        try {
            const response = await fetch('api/contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Show success toast (you can use a library or custom toast)
                alert('Pesan berhasil dikirim!'); 
                this.reset();
            } else {
                alert('Gagal mengirim pesan: ' + (result.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim pesan.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
</script>
