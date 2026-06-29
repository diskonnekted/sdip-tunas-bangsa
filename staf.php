<?php
require_once 'includes/settings.php';
require_once 'admin/config/database.php';

$page_title = 'Profil Staf';
$school_info = getSchoolInfo();

$database = new Database();
$db = $database->getConnection();

try {
    $stmt = $db->prepare("SELECT au.id, au.username, au.full_name, au.email, tp.subject, tp.photo_filename, tp.bio
                           FROM admin_users au LEFT JOIN teacher_profiles tp ON tp.user_id = au.id
                           WHERE (LOWER(TRIM(au.role)) = 'staf' OR LOWER(TRIM(au.role)) = 'staff') AND (au.is_active = 1 OR LOWER(TRIM(au.is_active)) = 'active') ORDER BY au.full_name ASC");
    $stmt->execute();
    $staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $staffs = [];
}

function getStaffPhotoPath($user) {
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
?>
<?php include 'includes/header.php'; ?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Profil Staf</h1>
            <p>Daftar staf SDIP Tunas Bangsa</p>
            <nav class="breadcrumb">
                <a href="index.php">Beranda</a>
                <span>/</span>
                <span>Staf</span>
            </nav>
        </div>
    </div>
</section>

<section class="section py-16 bg-gray-50">
    <div class="container">
        <?php if (empty($staffs)): ?>
            <div class="text-center py-12 bg-white rounded-2xl shadow-sm p-8">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Data</h3>
                <p class="text-gray-500">Data staf belum tersedia saat ini.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($staffs as $s): ?>
                <?php 
                    $photo = null;
                    if (!empty($s['photo_filename'])) {
                        $path = 'admin/uploads/teachers/' . $s['photo_filename'];
                        if (file_exists($path)) {
                            $photo = $path;
                        }
                    }
                    if (!$photo) {
                        $photo = getStaffPhotoPath($s);
                    }
                ?>
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group hover:-translate-y-2 border border-gray-100">
                    <div class="aspect-square relative overflow-hidden bg-gray-100">
                        <?php if ($photo): ?>
                            <img src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($s['full_name']) ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-primary-50">
                                <i class="fas fa-user text-6xl text-primary-200"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-6">
                            <div class="flex space-x-3 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                <a href="mailto:<?= htmlspecialchars($s['email']) ?>" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-primary-600 hover:bg-primary-600 hover:text-white transition-colors shadow-lg" title="Kirim Email">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1" title="<?= htmlspecialchars($s['full_name']) ?>">
                            <?= htmlspecialchars($s['full_name']) ?>
                        </h3>
                        <div class="flex items-center text-primary-600 text-sm font-medium mb-3">
                            <i class="fas fa-briefcase mr-2 text-xs"></i>
                            <span class="line-clamp-1"><?= htmlspecialchars($s['subject'] ?? 'Staf Sekolah') ?></span>
                        </div>
                        <?php if (!empty($s['bio'])): ?>
                        <p class="text-gray-500 text-sm line-clamp-2 mb-4 h-10 leading-relaxed">
                            <?= strip_tags(htmlspecialchars_decode($s['bio'])) ?>
                        </p>
                        <?php endif; ?>
                        <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                            <span class="flex items-center"><i class="fas fa-id-card mr-1.5"></i> ID: <?= $s['id'] ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Consultation Section -->
<section id="consultation" class="py-24 bg-gradient-to-br from-primary-900 via-green-900 to-slate-900 relative overflow-hidden">
    <!-- Decorative Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-primary-500/10 -skew-x-12 transform translate-x-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-accent-500/10 rounded-full blur-3xl -translate-x-1/2 translate-y-1/2"></div>
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-primary-400/10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
            <!-- Left Side: Text & Info -->
            <div class="lg:w-5/12 text-white">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm mb-6 shadow-lg">
                    <span class="w-2 h-2 rounded-full bg-accent-400 animate-pulse"></span>
                    <span class="text-xs font-bold tracking-wide uppercase text-green-100">Layanan Digital</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">Konsultasi <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-200 to-accent-300">Administrasi</span></h2>
                <p class="text-green-100 text-lg mb-8 leading-relaxed">
                    Kami siap membantu Anda dalam urusan administrasi sekolah. Mulai dari keuangan, data siswa, hingga persuratan, semua bisa dikonsultasikan secara online.
                </p>
                
                <div class="space-y-6 hidden md:block">
                    <div class="flex items-start space-x-4 p-4 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:translate-x-2">
                        <div class="w-12 h-12 rounded-xl bg-primary-500/20 flex items-center justify-center flex-shrink-0 text-accent-300 shadow-inner">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg text-white">Respon Cepat</h4>
                            <p class="text-sm text-green-200">Tim kami akan membalas pesan Anda pada jam kerja (07.00 - 15.00 WIB).</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4 p-4 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:translate-x-2">
                        <div class="w-12 h-12 rounded-xl bg-primary-500/20 flex items-center justify-center flex-shrink-0 text-accent-300 shadow-inner">
                            <i class="fas fa-shield-alt text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg text-white">Privasi Terjaga</h4>
                            <p class="text-sm text-green-200">Data dan pertanyaan Anda dijamin kerahasiaannya oleh pihak sekolah.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="lg:w-7/12 w-full">
                <div class="bg-white rounded-3xl shadow-2xl p-6 md:p-10 border border-white/20 relative backdrop-blur-sm">
                    <!-- Decorative corner -->
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-accent-400/20 rounded-full blur-3xl"></div>
                    
                    <h3 class="text-2xl font-bold text-gray-800 mb-8 flex items-center relative z-10">
                        <span class="w-10 h-10 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center mr-3 text-lg">
                            <i class="fas fa-paper-plane"></i>
                        </span>
                        Form Konsultasi
                    </h3>

                    <form id="staffContactForm" class="space-y-5 relative z-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="group">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-primary-600 transition-colors">Nama Lengkap</label>
                                <input type="text" id="name" name="name" class="w-full px-5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none" placeholder="Nama Anda" required>
                            </div>
                            <div class="group">
                                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-primary-600 transition-colors">Status</label>
                                <div class="relative">
                                    <select id="status" name="status" class="w-full px-5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none appearance-none cursor-pointer" required>
                                        <option value="" disabled selected>Pilih Status</option>
                                        <option value="Orang Tua">Orang Tua/Wali</option>
                                        <option value="Siswa">Siswa</option>
                                        <option value="Umum">Umum</option>
                                    </select>
                                    <div class="absolute right-5 top-1/2 transform -translate-y-1/2 pointer-events-none text-gray-400">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="group">
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-primary-600 transition-colors">Email</label>
                                <input type="email" id="email" name="email" class="w-full px-5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none" placeholder="contoh@email.com" required>
                            </div>
                            <div class="group">
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-primary-600 transition-colors">Nomor Telepon/WA</label>
                                <input type="tel" id="phone" name="phone" class="w-full px-5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none" placeholder="08xxxxxxxxxx" required>
                            </div>
                        </div>

                        <div class="group">
                            <label for="topic" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-primary-600 transition-colors">Topik Konsultasi</label>
                            <div class="relative">
                                <select id="topic" name="topic" class="w-full px-5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none appearance-none cursor-pointer" required>
                                    <option value="" disabled selected>Pilih Topik Administrasi</option>
                                    <option value="keuangan">Administrasi Keuangan (SPP/Uang Pangkal)</option>
                                    <option value="dapodik">Data Siswa (Dapodik)</option>
                                    <option value="surat">Persuratan & Legalisir</option>
                                    <option value="mutasi">Mutasi Siswa</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                                <div class="absolute right-5 top-1/2 transform -translate-y-1/2 pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <div class="group">
                            <label for="message" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-primary-600 transition-colors">Pesan/Pertanyaan</label>
                            <textarea id="message" name="message" rows="3" class="w-full px-5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none resize-none" placeholder="Tuliskan detail pertanyaan atau keperluan Anda..." required></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-xl hover:from-primary-700 hover:to-primary-800 focus:ring-4 focus:ring-primary-500/30 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                                <i class="fas fa-paper-plane mr-2.5"></i> Kirim Permintaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('staffContactForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...';

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Map topic to subject
    const topicMap = {
        'keuangan': 'Administrasi Keuangan',
        'dapodik': 'Data Siswa (Dapodik)',
        'surat': 'Persuratan & Legalisir',
        'mutasi': 'Mutasi Siswa',
        'lainnya': 'Lainnya'
    };
    
    const topicLabel = topicMap[data.topic] || data.topic;
    data.subject = 'Konsultasi: ' + topicLabel;
    
    // Append status to message
    data.message = data.message + '\n\nStatus Pengirim: ' + data.status;
    
    // Set recipient type
    data.recipient_type = 'staff';
    data.recipient_id = null;
    
    // Set student name if status is Siswa (using sender name as proxy since there's no separate field)
    if (data.status === 'Siswa') {
        data.student_name = data.name;
    }

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
            // Use the showToast function if available, otherwise alert
            if (typeof showToast === 'function') {
                showToast('Permintaan konsultasi berhasil dikirim!', 'success');
            } else {
                alert('Permintaan konsultasi berhasil dikirim!');
            }
            this.reset();
        } else {
            if (typeof showToast === 'function') {
                showToast('Gagal mengirim: ' + (result.message || 'Terjadi kesalahan'), 'error');
            } else {
                alert('Gagal mengirim: ' + (result.message || 'Terjadi kesalahan'));
            }
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

<?php include 'includes/footer.php'; ?>

