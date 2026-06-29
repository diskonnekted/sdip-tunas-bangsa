<?php
require_once 'includes/settings.php';

$page_title = 'Beranda Alternatif';
$school_info = getSchoolInfo();
$contact_info = getContactInfo();
$body_class = 'font-jakarta bg-white text-slate-800';
$nav_theme = 'light';

// Custom Tailwind Config for this page
$extra_head = <<<EOT
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'jakarta': ['Plus Jakarta Sans', 'sans-serif'],
                        'inter': ['Inter', 'sans-serif']
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e', // Sky blue
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        },
                        accent: {
                            500: '#f59e0b', // Amber
                            600: '#d97706',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'glow': '0 0 15px rgba(34, 197, 94, 0.3)'
                    }
                }
            }
        }
    </script>
    <style>
        .clip-diagonal {
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
        }
        .pattern-grid {
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
EOT;

include 'includes/header.php';
?>

<!-- Hero Section with Pattern -->
<section class="relative min-h-[90vh] flex items-center overflow-hidden bg-white">
    <!-- Subtle Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="images/sch3.jpg" alt="Background" class="w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-b from-white/20 via-transparent to-white/20"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-white/80 via-white/40 to-transparent"></div>
    </div>

    <div class="absolute inset-0 pattern-grid opacity-10 z-0"></div>
    <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-white/60 to-transparent z-0"></div>
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-20">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="text-left space-y-8 animate-fade-in-up">
                <div class="inline-flex items-center space-x-2 bg-white px-4 py-2 rounded-full shadow-sm border border-brand-100">
                    <span class="w-2 h-2 rounded-full bg-accent-500 animate-pulse"></span>
                    <span class="text-sm font-semibold text-brand-700 tracking-wide uppercase">Penerimaan Siswa Baru 2025</span>
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-extrabold text-slate-900 leading-tight">
                    Mewujudkan <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-700 to-brand-500 filter drop-shadow-sm">Generasi Emas</span> <br>
                    Berintegritas
                </h1>
                
                <p class="text-xl text-slate-600 max-w-lg leading-relaxed">
                    <?php echo htmlspecialchars($school_info['name']); ?> membangun karakter siswa melalui pendidikan holistik yang mengutamakan nilai-nilai luhur dan keunggulan akademik.
                </p>
                
                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="profil.php" class="px-8 py-4 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold transition-all transform hover:-translate-y-1 shadow-lg shadow-brand-500/30 flex items-center">
                        <i class="fas fa-compass mr-2"></i> Jelajahi Profil
                    </a>
                    <a href="contact.php" class="px-8 py-4 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl font-bold transition-all hover:shadow-md flex items-center">
                        <i class="fas fa-paper-plane mr-2 text-brand-500"></i> Hubungi Kami
                    </a>
                </div>

                <div class="flex items-center gap-8 pt-8 border-t border-slate-200/60">
                    <div>
                        <div class="text-3xl font-bold text-slate-900">A</div>
                        <div class="text-sm text-slate-500 font-medium">Akreditasi Unggul</div>
                    </div>
                    <div class="w-px h-12 bg-slate-200"></div>
                    <div>
                        <div class="text-3xl font-bold text-slate-900">98%</div>
                        <div class="text-sm text-slate-500 font-medium">Lulusan Terbaik</div>
                    </div>
                </div>
            </div>

            <!-- Right Visual -->
            <div class="relative hidden lg:block">
                <div class="absolute -inset-4 bg-brand-500/10 rounded-[2rem] transform rotate-3"></div>
                <div class="relative bg-white p-2 rounded-[2rem] shadow-2xl transform -rotate-2 hover:rotate-0 transition-all duration-500">
                    <img src="admin/uploads/guru45.jpg" alt="Pendidikan Berkualitas" class="rounded-[1.5rem] w-full object-cover h-[500px]">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section (Modern Cards) -->
<section class="py-24 bg-slate-50 relative overflow-hidden">
    <!-- Decorative Background -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-green-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse" style="animation-delay: 2s"></div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-brand-600 font-bold tracking-wider uppercase text-sm">Nilai-Nilai Utama</span>
            <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mt-2 mb-4">Fondasi Karakter Siswa</h2>
            <p class="text-slate-600 text-lg">Membangun generasi yang tidak hanya cerdas secara akademik, tetapi juga memiliki kepribadian yang tangguh dan beretika.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1: Integritas -->
            <div class="group relative bg-white rounded-[2.5rem] p-10 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-100">
                <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-transparent rounded-[2.5rem] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative z-10">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-brand-500 to-emerald-400 flex items-center justify-center text-white shadow-lg shadow-brand-500/30 mb-8 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                        <i class="fas fa-handshake text-3xl"></i>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Integritas</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Menanamkan kejujuran sebagai pondasi utama dalam setiap tindakan dan perkataan.
                    </p>
                    
                    <a href="#" class="inline-flex items-center text-brand-600 font-bold group-hover:translate-x-2 transition-transform">
                        Pelajari <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
            </div>

             <!-- Card 2: Kritis -->
            <div class="group relative bg-white rounded-[2.5rem] p-10 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-100">
                 <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-transparent rounded-[2.5rem] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative z-10">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-400 flex items-center justify-center text-white shadow-lg shadow-purple-500/30 mb-8 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                        <i class="fas fa-brain text-3xl"></i>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Berpikir Kritis</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                         Menganalisis masalah dan menemukan solusi kreatif secara mandiri.
                    </p>
                    
                    <a href="#" class="inline-flex items-center text-purple-600 font-bold group-hover:translate-x-2 transition-transform">
                        Pelajari <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
            </div>

            <!-- Card 3: Global -->
            <div class="group relative bg-white rounded-[2.5rem] p-10 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-100">
                 <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-transparent rounded-[2.5rem] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative z-10">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-400 flex items-center justify-center text-white shadow-lg shadow-amber-500/30 mb-8 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                        <i class="fas fa-globe-asia text-3xl"></i>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Wawasan Global</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Siap menghadapi tantangan dunia dengan tetap menjunjung kearifan lokal.
                    </p>
                    
                    <a href="#" class="inline-flex items-center text-amber-600 font-bold group-hover:translate-x-2 transition-transform">
                        Pelajari <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Principal Section (Modern Split) -->
<section class="py-24 bg-slate-900 text-white relative overflow-hidden">
    <!-- Background Patterns -->
    <div class="absolute top-0 right-0 w-1/3 h-full bg-brand-900/50 skew-x-12 transform translate-x-20"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-accent-500/10 rounded-full blur-3xl"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-5 relative">
                <div class="relative z-10">
                    <?php if (!empty($school_info['principal_photo'])): ?>
                        <img src="admin/uploads/<?php echo htmlspecialchars($school_info['principal_photo']); ?>" 
                             alt="Kepala Sekolah" 
                             class="rounded-3xl shadow-2xl w-full object-cover h-[500px] grayscale hover:grayscale-0 transition-all duration-500">
                    <?php else: ?>
                        <div class="rounded-3xl shadow-2xl w-full h-[500px] bg-slate-800 flex items-center justify-center border border-slate-700">
                            <i class="fas fa-user-tie text-9xl text-slate-700"></i>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Floating Quote Card -->
                    <div class="absolute -bottom-8 -right-8 bg-brand-600 p-6 rounded-2xl shadow-xl max-w-xs hidden md:block">
                        <p class="text-brand-100 text-sm italic">
                            "Pendidikan adalah senjata paling ampuh untuk mengubah dunia."
                        </p>
                    </div>
                </div>
                <!-- Decorative Border -->
                <div class="absolute top-8 -left-8 w-full h-full border-2 border-slate-700 rounded-3xl -z-0 hidden md:block"></div>
            </div>

            <div class="lg:col-span-7 space-y-8">
                <div>
                    <h2 class="text-brand-400 font-bold tracking-widest uppercase mb-2 text-sm">Sambutan Kepala Sekolah</h2>
                    <h3 class="text-4xl lg:text-5xl font-bold mb-6">Membangun Masa Depan <br> Bersama Kami</h3>
                </div>
                
                <div class="space-y-6 text-slate-300 text-lg leading-relaxed">
                    <p>
                        "Selamat datang di era baru pendidikan. Di <?php echo htmlspecialchars($school_info['name']); ?>, kami percaya bahwa setiap anak memiliki potensi luar biasa yang menunggu untuk digali."
                    </p>
                    <p>
                        "Kami tidak hanya fokus pada pencapaian akademik semata, tetapi juga pada pembentukan karakter yang kuat. Kejujuran, disiplin, dan empati adalah mata pelajaran kehidupan yang kami tanamkan setiap hari."
                    </p>
                </div>

                <div class="pt-8 flex items-center gap-6">
                    <div>
                        <h4 class="text-xl font-bold text-white"><?php echo htmlspecialchars($school_info['principal_name']); ?></h4>
                        <p class="text-brand-400">Kepala Sekolah</p>
                    </div>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Signature_sample.svg" alt="Signature" class="h-12 opacity-50 invert">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA / Newsletter Section -->
<section class="py-24 bg-brand-50 relative">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-brand-600 rounded-[3rem] p-12 lg:p-20 text-center relative overflow-hidden shadow-2xl">
            <!-- Circles Background -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-2xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-accent-500/20 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl"></div>

            <div class="relative z-10 max-w-3xl mx-auto space-y-8">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Siap Menjadi Bagian dari Keluarga Besar Kami?</h2>
                <p class="text-brand-100 text-xl mb-10">
                    Daftarkan putra-putri Anda sekarang dan berikan pendidikan terbaik untuk masa depan mereka.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="contact.php" class="px-10 py-5 bg-white text-brand-600 font-bold rounded-xl shadow-lg hover:shadow-xl hover:bg-slate-50 transition-all transform hover:-translate-y-1">
                        Daftar Sekarang
                    </a>
                    <a href="info.php" class="px-10 py-5 bg-transparent border-2 border-white/30 text-white font-bold rounded-xl hover:bg-white/10 transition-all">
                        Informasi Pendaftaran
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
