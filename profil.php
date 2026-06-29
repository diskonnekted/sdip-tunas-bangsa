<?php
// Include necessary files
include_once 'includes/settings.php';

// Get school info
$school_info = getSchoolInfo();
$contact_info = getContactInfo();

// Set page title
$page_title = "Profil - " . $school_info['name'];
?>
<?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 to-purple-600/20 pointer-events-none"></div>
        <div class="container relative z-10">
            <div class="page-header-content">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 text-white drop-shadow-lg">Profil Sekolah</h1>
                <p>Mengenal lebih dekat <?php echo htmlspecialchars($school_info['name']); ?></p>
                <nav class="breadcrumb">
                    <a href="index.php">Beranda</a>
                    <span>/</span>
                    <span>Profil</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about py-20 bg-white">
        <div class="container">
            <div class="about-grid">
                <div class="about-image">
                    <div class="school-image bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                        <img src="images/hero.jpg" alt="Gedung <?php echo htmlspecialchars($school_info['name']); ?>" class="school-building">
                        <div class="image-overlay">
                            <h4>Gedung <?php echo htmlspecialchars($school_info['name']); ?></h4>
                            <p>Fasilitas Modern & Nyaman</p>
                        </div>
                    </div>
                </div>
                <div class="about-content">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Tentang <?php echo htmlspecialchars($school_info['name']); ?></h2>
                    <p><?php echo !empty($school_info['description']) ? htmlspecialchars($school_info['description']) : htmlspecialchars($school_info['name']) . ' didirikan pada tahun ' . ($school_info['established_year'] ?: '2009') . ' dengan visi menjadi sekolah dasar terdepan dalam menghasilkan generasi yang cerdas, berkarakter, dan berdaya saing global. Kami berkomitmen memberikan pendidikan berkualitas tinggi dengan menggabungkan kurikulum nasional dan internasional.'; ?></p>
                    
                    <p>Dengan fasilitas modern dan tenaga pengajar profesional, kami menciptakan lingkungan belajar yang kondusif untuk mengembangkan potensi setiap siswa secara optimal, baik dari segi akademik, karakter, maupun keterampilan life skills.</p>
                    
                    <div class="achievement-stats">
                        <div class="stat">
                            <span class="number">15+</span>
                            <span class="label">Tahun Pengalaman</span>
                        </div>
                        <div class="stat">
                            <span class="number">500+</span>
                            <span class="label">Alumni Sukses</span>
                        </div>
                        <div class="stat">
                            <span class="number">98%</span>
                            <span class="label">Tingkat Kelulusan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision Mission Section -->
    <section class="vision-mission py-20 bg-gray-50">
        <div class="container">
            <div class="vm-grid grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="vm-card bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="vm-icon w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-eye text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-gray-900 text-center">Visi</h3>
                    <p class="text-gray-600 text-center leading-relaxed">Menjadi sekolah dasar unggulan yang menghasilkan generasi cerdas, berkarakter mulia, dan berwawasan global untuk membangun masa depan bangsa yang gemilang.</p>
                </div>
                <div class="vm-card bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="vm-icon w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-bullseye text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-gray-900 text-center">Misi</h3>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start"><i class="fas fa-star text-yellow-400 mt-1 mr-3"></i><span>Menyelenggarakan pendidikan berkualitas dengan standar nasional dan internasional</span></li>
                        <li class="flex items-start"><i class="fas fa-star text-yellow-400 mt-1 mr-3"></i><span>Mengembangkan karakter siswa berdasarkan nilai-nilai Pancasila</span></li>
                        <li class="flex items-start"><i class="fas fa-star text-yellow-400 mt-1 mr-3"></i><span>Menerapkan teknologi pembelajaran terkini</span></li>
                        <li class="flex items-start"><i class="fas fa-star text-yellow-400 mt-1 mr-3"></i><span>Menciptakan lingkungan belajar yang aman, nyaman, dan inspiratif</span></li>
                        <li class="flex items-start"><i class="fas fa-star text-yellow-400 mt-1 mr-3"></i><span>Membangun kemitraan dengan orang tua dan masyarakat</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values py-20 bg-white">
        <div class="container">
            <div class="section-header text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Nilai-Nilai Sekolah</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Fondasi karakter yang kami tanamkan kepada setiap siswa</p>
            </div>
            
            <div class="values-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="value-card bg-gray-50 p-8 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 text-center group hover:-translate-y-1">
                    <div class="value-icon w-16 h-16 mx-auto mb-6 bg-white rounded-full flex items-center justify-center text-primary-600 shadow-md group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-heart text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-gray-900">CERDAS</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">Cinta belajar, Efektif, Religius, Disiplin, Aktif, Santun</p>
                </div>
                
                <div class="value-card bg-gray-50 p-8 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 text-center group hover:-translate-y-1">
                    <div class="value-icon w-16 h-16 mx-auto mb-6 bg-white rounded-full flex items-center justify-center text-primary-600 shadow-md group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-star text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-gray-900">CERIA</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">Cerdas, Empati, Responsif, Inovatif, Adaptif</p>
                </div>
                
                <div class="value-card bg-gray-50 p-8 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 text-center group hover:-translate-y-1">
                    <div class="value-icon w-16 h-16 mx-auto mb-6 bg-white rounded-full flex items-center justify-center text-primary-600 shadow-md group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-handshake text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-gray-900">INTEGRITAS</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">Jujur, bertanggung jawab, dan konsisten dalam perkataan dan perbuatan</p>
                </div>
                
                <div class="value-card bg-gray-50 p-8 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 text-center group hover:-translate-y-1">
                    <div class="value-icon w-16 h-16 mx-auto mb-6 bg-white rounded-full flex items-center justify-center text-primary-600 shadow-md group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-globe text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-gray-900">GLOBAL</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">Berpikiran terbuka dan siap menghadapi tantangan global</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="facilities py-20 bg-gray-50">
        <div class="container">
            <div class="section-header text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Fasilitas Unggulan</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Sarana dan prasarana terbaik untuk mendukung proses pembelajaran</p>
            </div>
            
            <div class="facilities-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="facility-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
                    <div class="facility-image-container aspect-video overflow-hidden relative">
                        <img src="images/belajar.jpg" alt="Smart Classroom" class="facility-image w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="facility-content p-6">
                        <h4 class="text-xl font-bold mb-2 text-gray-900 group-hover:text-primary-600 transition-colors">Smart Classroom</h4>
                        <p class="text-gray-600">Ruang kelas dilengkapi dengan teknologi digital interaktif</p>
                    </div>
                </div>
                
                <div class="facility-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
                    <div class="facility-image-container aspect-video overflow-hidden relative">
                        <img src="images/bersama guru.jpg" alt="Perpustakaan Modern" class="facility-image w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="facility-content p-6">
                        <h4 class="text-xl font-bold mb-2 text-gray-900 group-hover:text-primary-600 transition-colors">Perpustakaan Modern</h4>
                        <p class="text-gray-600">Koleksi buku lengkap dengan sistem digital dan ruang baca nyaman</p>
                    </div>
                </div>
                
                <div class="facility-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
                    <div class="facility-image-container aspect-video overflow-hidden relative">
                        <img src="images/mengaji.jpg" alt="Laboratorium Sains" class="facility-image w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="facility-content p-6">
                        <h4 class="text-xl font-bold mb-2 text-gray-900 group-hover:text-primary-600 transition-colors">Laboratorium Sains</h4>
                        <p class="text-gray-600">Laboratorium lengkap untuk eksplorasi dan eksperimen sains</p>
                    </div>
                </div>
                
                <div class="facility-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
                    <div class="facility-image-container aspect-video overflow-hidden relative">
                        <img src="images/latihan sepak bola.jpg" alt="Lapangan Olahraga" class="facility-image w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="facility-content p-6">
                        <h4 class="text-xl font-bold mb-2 text-gray-900 group-hover:text-primary-600 transition-colors">Lapangan Olahraga</h4>
                        <p class="text-gray-600">Area olahraga yang luas untuk berbagai aktivitas fisik</p>
                    </div>
                </div>
                
                <div class="facility-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
                    <div class="facility-image-container aspect-video overflow-hidden relative">
                        <img src="images/latihan seni siswa.jpg" alt="Ruang Seni" class="facility-image w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="facility-content p-6">
                        <h4 class="text-xl font-bold mb-2 text-gray-900 group-hover:text-primary-600 transition-colors">Ruang Seni</h4>
                        <p class="text-gray-600">Studio seni untuk mengembangkan kreativitas dan bakat siswa</p>
                    </div>
                </div>
                
                <div class="facility-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
                    <div class="facility-image-container aspect-video overflow-hidden relative">
                        <img src="images/panggung acara.jpg" alt="Ruang Musik" class="facility-image w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="facility-content p-6">
                        <h4 class="text-xl font-bold mb-2 text-gray-900 group-hover:text-primary-600 transition-colors">Ruang Musik</h4>
                        <p class="text-gray-600">Studio musik dengan berbagai alat musik untuk pembelajaran</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>