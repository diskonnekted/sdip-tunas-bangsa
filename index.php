<?php
require_once 'includes/settings.php';

$page_title = 'Beranda';
$school_info = getSchoolInfo();
$contact_info = getContactInfo();
$social_media = getSocialMedia();
$body_class = 'font-inter bg-gradient-to-br from-slate-50 via-green-50 to-green-100';
$extra_head = <<<EOT
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'jakarta': ['Plus Jakarta Sans', 'sans-serif']
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
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-in-up': 'fade-in-up 0.6s ease-out',
                        'slide-in-right': 'slide-in-right 0.8s ease-out',
                        'pulse-glow': 'pulse-glow 2s ease-in-out infinite alternate'
                    },
                    backgroundImage: {
                        'hero-gradient': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        'card-gradient': 'linear-gradient(145deg, #f8fafc 0%, #e2e8f0 100%)'
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes slide-in-right {
            0% { opacity: 0; transform: translateX(30px); }
            100% { opacity: 1; transform: translateX(0); }
        }
        @keyframes pulse-glow {
            0% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.4); }
            100% { box-shadow: 0 0 40px rgba(99, 102, 241, 0.8); }
        }
        .text-shadow { text-shadow: 2px 2px 4px rgba(0,0,0,0.1); }
    </style>
EOT;

include 'includes/header.php';
?>

    <!-- Modern Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-20">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0">
            <img src="images/hero.jpg" alt="SDIP Tunas Bangsa Hero" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-slate-900/40 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-primary-800/30 via-transparent to-transparent"></div>
        </div>
        
        <!-- Floating Geometric Shapes -->
        <div class="absolute inset-0">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-gradient-to-br from-accent-500/20 to-primary-300/20 rounded-full blur-3xl animate-float"></div>
            <div class="absolute top-3/4 right-1/4 w-96 h-96 bg-gradient-to-br from-primary-300/20 to-accent-500/20 rounded-full blur-3xl animate-float" style="animation-delay: -2s;"></div>
            <div class="absolute top-1/2 left-1/2 w-32 h-32 bg-white/5 rounded-2xl rotate-45 animate-float" style="animation-delay: -4s;"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left animate-fade-in-up">
                    <!-- Badge -->
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white/90 text-sm font-medium mb-6">
                        <i class="fas fa-star mr-2 text-accent-400"></i>
                        Sekolah Berintegritas Terdepan
                    </div>
                    
                    <!-- Main Headline -->
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white leading-tight mb-6">
                        Membentuk
                        <span class="bg-gradient-to-r from-primary-400 to-emerald-300 bg-clip-text text-transparent animate-pulse-glow">
                            Generasi
                        </span>
                        <br>Berintegritas
                    </h1>
                    
                    <!-- Subtitle -->
                    <p class="text-xl md:text-2xl text-white/80 leading-relaxed mb-8 max-w-2xl">
                        <?php echo htmlspecialchars($school_info['name']); ?> menghadirkan pendidikan karakter yang holistik dengan menanamkan 9 nilai integritas sejak dini.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="profil.html" class="group inline-flex items-center justify-center px-8 py-4 bg-primary-600 hover:bg-primary-500 text-white font-semibold rounded-2xl shadow-[0_0_20px_rgba(34,197,94,0.3)] hover-lift transition-all duration-300">
                            <i class="fas fa-school mr-3 group-hover:scale-110 transition-transform"></i>
                            Tentang Kami
                            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="academic.php" class="group inline-flex items-center justify-center px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white font-semibold rounded-2xl hover-lift transition-all duration-300">
                            <i class="fas fa-book-open mr-3 group-hover:scale-110 transition-transform"></i>
                            Program Akademik
                        </a>
                    </div>
                    
                    <!-- Stats Row -->
                    <div class="grid grid-cols-3 gap-4 mt-12 pt-8 border-t border-white/20">
                        <div class="text-center">
                            <div class="text-2xl md:text-3xl font-bold text-white mb-1">500+</div>
                            <div class="text-sm text-white/70">Siswa Aktif</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl md:text-3xl font-bold text-white mb-1"><?php echo htmlspecialchars($school_info['accreditation']); ?></div>
                            <div class="text-sm text-white/70">Akreditasi</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl md:text-3xl font-bold text-white mb-1">25+</div>
                            <div class="text-sm text-white/70">Guru Expert</div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Content - 3D Card -->
                <div class="relative animate-slide-in-right">
                    <div class="relative">
                        <!-- Main Card -->
                        <div class="bg-white/95 backdrop-blur-xl border border-white/20 rounded-3xl p-8 hover-lift transition-all duration-500 hover:bg-white shadow-2xl">
                            <div class="flex items-center space-x-4 mb-6">
                                <div class="w-16 h-16 bg-green-600 rounded-2xl flex items-center justify-center shadow-lg" style="background-color: #16a34a;">
                                    <i class="fas fa-shield-alt text-white text-3xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-800">Pendidikan Karakter</h3>
                                    <p class="text-primary-600 font-medium">Integritas Sejak Dini</p>
                                </div>
                            </div>
                            
                            <p class="text-gray-600 leading-relaxed mb-6">
                                Menanamkan 9 nilai integritas: Kejujuran, Tanggung Jawab, Disiplin, Keadilan, Kepedulian, Kesederhanaan, Kerja Keras, Kemandirian, dan Keberanian.
                            </p>
                            
                            <!-- Values Grid -->
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-primary-50 rounded-xl p-3 text-center hover:bg-primary-100 transition-all duration-300">
                                    <div class="text-2xl mb-1 text-green-600"><i class="fas fa-handshake"></i></div>
                                    <div class="text-xs text-gray-700 font-medium">Kejujuran</div>
                                </div>
                                <div class="bg-primary-50 rounded-xl p-3 text-center hover:bg-primary-100 transition-all duration-300">
                                    <div class="text-2xl mb-1 text-green-600"><i class="fas fa-book-open"></i></div>
                                    <div class="text-xs text-gray-700 font-medium">Tanggung Jawab</div>
                                </div>
                                <div class="bg-primary-50 rounded-xl p-3 text-center hover:bg-primary-100 transition-all duration-300">
                                    <div class="text-2xl mb-1 text-green-600"><i class="fas fa-clock"></i></div>
                                    <div class="text-xs text-gray-700 font-medium">Disiplin</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Floating Mini Cards -->
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center animate-float shadow-2xl">
                            <i class="fas fa-star text-white text-xl"></i>
                        </div>
                        
                        <div class="absolute -bottom-6 -left-6 w-16 h-16 bg-gradient-to-br from-green-400 to-green-500 rounded-2xl flex items-center justify-center animate-float shadow-2xl" style="animation-delay: -3s;">
                            <i class="fas fa-heart text-white"></i>
                        </div>
                        
                        <div class="absolute top-1/2 -right-8 w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center animate-float shadow-2xl" style="animation-delay: -1s;">
                            <i class="fas fa-lightbulb text-white text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <div class="w-8 h-12 border-2 border-white/30 rounded-full flex justify-center">
                <div class="w-1 h-3 bg-white/60 rounded-full mt-2 animate-pulse"></div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Keunggulan <?php echo htmlspecialchars($school_info['name']); ?></h2>
                <p class="section-subtitle">Membangun karakter berintegritas melalui pendidikan yang holistik dan inovatif</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Pendidikan Kejujuran</h3>
                    <p>Menanamkan nilai kejujuran melalui kantin kejujuran dan budaya berkata benar</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <h3>Keadilan & Kepedulian</h3>
                    <p>Menciptakan lingkungan yang adil dan peduli antar sesama siswa dan guru</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3>Tanggung Jawab</h3>
                    <p>Membentuk siswa yang bertanggung jawab terhadap tugas dan lingkungan sekolah</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Prestasi Berintegritas</h3>
                    <p>Meraih prestasi dengan cara yang jujur dan kerja keras tanpa kecurangan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 9 Nilai Integritas Section -->
    <section class="integrity-values" style="background: linear-gradient(135deg, #166534 0%, #22c55e 100%); color: white; padding: 80px 0;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 60px;">
                <h2 class="section-title" style="color: white; font-size: 2.5rem; margin-bottom: 16px;">
                    9 Nilai Integritas <?php echo htmlspecialchars($school_info['name']); ?>
                </h2>
                <p class="section-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.2rem;">
                    Membangun karakter integritas sejak dini untuk generasi Indonesia yang berintegritas
                </p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 40px;">
                <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 24px; border-radius: 16px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="font-size: 3rem; margin-bottom: 16px;"><i class="fas fa-handshake"></i></div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 12px; color: #FEF3C7;">Kejujuran</h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">Berkata benar, tidak menyontek, dan mengakui kesalahan dengan lapang dada</p>
                </div>
                
                <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 24px; border-radius: 16px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="font-size: 3rem; margin-bottom: 16px;"><i class="fas fa-book-open"></i></div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 12px; color: #FEF3C7;">Tanggung Jawab</h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">Mengerjakan tugas dengan sungguh-sungguh dan bertanggung jawab atas perbuatan</p>
                </div>
                
                <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 24px; border-radius: 16px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="font-size: 3rem; margin-bottom: 16px;"><i class="fas fa-clock"></i></div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 12px; color: #FEF3C7;">Disiplin</h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">Tepat waktu, tertib, dan mengikuti tata tertib sekolah dengan baik</p>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 40px;">
                <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 24px; border-radius: 16px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="font-size: 3rem; margin-bottom: 16px;"><i class="fas fa-balance-scale"></i></div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 12px; color: #FEF3C7;">Keadilan</h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">Tidak pilih kasih, berbagi dengan teman, dan menghormati perbedaan</p>
                </div>
                
                <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 24px; border-radius: 16px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="font-size: 3rem; margin-bottom: 16px;"><i class="fas fa-heart"></i></div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 12px; color: #FEF3C7;">Kepedulian</h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">Membantu teman yang kesulitan dan menjaga kebersihan lingkungan</p>
                </div>
                
                <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 24px; border-radius: 16px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="font-size: 3rem; margin-bottom: 16px;"><i class="fas fa-leaf"></i></div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 12px; color: #FEF3C7;">Kesederhanaan</h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">Hidup hemat, tidak sombong, dan bersyukur dengan yang dimiliki</p>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 24px; border-radius: 16px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="font-size: 3rem; margin-bottom: 16px;"><i class="fas fa-hammer"></i></div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 12px; color: #FEF3C7;">Kerja Keras</h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">Berusaha maksimal, pantang menyerah, dan tidak mudah putus asa</p>
                </div>
                
                <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 24px; border-radius: 16px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="font-size: 3rem; margin-bottom: 16px;"><i class="fas fa-bullseye"></i></div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 12px; color: #FEF3C7;">Kemandirian</h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">Tidak bergantung pada orang lain, berani mengambil keputusan sendiri</p>
                </div>
                
                <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 24px; border-radius: 16px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="font-size: 3rem; margin-bottom: 16px;"><i class="fas fa-fist-raised"></i></div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 12px; color: #FEF3C7;">Berani</h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">Berani mengatakan yang benar, melaporkan pelanggaran, dan membela keadilan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Principal Message Section -->
    <section class="principal-message" style="padding: 80px 0; background: #f8fafc;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 60px;">
                <h2 class="section-title">Pesan Kepala Sekolah</h2>
                <p class="section-subtitle">Komitmen kami untuk pendidikan berintegritas</p>
            </div>
            
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <?php if (!empty($school_info['principal_photo'])): ?>
                        <img src="admin/uploads/<?php echo htmlspecialchars($school_info['principal_photo']); ?>" 
                             alt="<?php echo htmlspecialchars($school_info['principal_name']); ?>" 
                             style="width: 100px; height: 100px; border-radius: 50%; margin: 0 auto 24px; object-fit: cover; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); display: block;">
                    <?php else: ?>
                        <div style="width: 100px; height: 100px; border-radius: 50%; margin: 0 auto 24px; background: linear-gradient(135deg, #3B82F6, #1E40AF); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user-tie" style="font-size: 2.5rem; color: white;"></i>
                        </div>
                    <?php endif; ?>
                    <h3 style="font-size: 1.5rem; color: #1E40AF; margin-bottom: 12px;">
                        <?php echo htmlspecialchars($school_info['principal_name']); ?>
                    </h3>
                    <p style="color: #6B7280; margin-bottom: 24px; font-weight: 500;">Kepala Sekolah</p>
                    <blockquote style="font-style: italic; font-size: 1.1rem; line-height: 1.8; color: #374151; margin-bottom: 24px;">
                        "Selamat datang di <?php echo htmlspecialchars($school_info['name']); ?>. Kami berkomitmen untuk membentuk generasi yang tidak hanya cerdas secara akademik, tetapi juga memiliki karakter yang kuat dan integritas yang tinggi. Melalui pendidikan integritas sejak dini, kami mempersiapkan anak-anak untuk menjadi pemimpin masa depan yang jujur dan bertanggung jawab."
                    </blockquote>
                    <p style="font-weight: 600; color: #1E40AF;">
                        <?php echo htmlspecialchars($school_info['motto']); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats" style="background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%); color: white; padding: 80px 0;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; text-align: center;">
                <div>
                    <div style="font-size: 3rem; font-weight: 700; margin-bottom: 12px;"><?php echo htmlspecialchars($school_info['established_year']); ?></div>
                    <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Tahun Berdiri</p>
                </div>
                <div>
                    <div style="font-size: 3rem; font-weight: 700; margin-bottom: 12px;">500+</div>
                    <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Siswa Aktif</p>
                </div>
                <div>
                    <div style="font-size: 3rem; font-weight: 700; margin-bottom: 12px;"><?php echo htmlspecialchars($school_info['accreditation']); ?></div>
                    <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Akreditasi</p>
                </div>
                <div>
                    <div style="font-size: 3rem; font-weight: 700; margin-bottom: 12px;">25+</div>
                    <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Guru Berpengalaman</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta" style="padding: 80px 0; background: #f8fafc;">
        <div class="container">
            <div style="max-width: 600px; margin: 0 auto; text-align: center;">
                <h2 style="font-size: 2.5rem; color: #1E40AF; margin-bottom: 24px; font-weight: 700;">
                    Bergabunglah dengan Kami
                </h2>
                <p style="font-size: 1.2rem; color: #6B7280; margin-bottom: 32px; line-height: 1.6;">
                    Mari bersama-sama membangun generasi berintegritas untuk masa depan Indonesia yang lebih baik
                </p>
                <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                    <a href="profil.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-info-circle"></i>
                        Pelajari Lebih Lanjut
                    </a>
                    <a href="kontak.html" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-phone"></i>
                        Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
