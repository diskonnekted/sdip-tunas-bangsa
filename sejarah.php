<?php
// Include necessary files
include_once 'includes/settings.php';

// Get school info
$school_info = getSchoolInfo();
$contact_info = getContactInfo();

// Set page title
$page_title = "Sejarah - " . $school_info['name'];
?>
<?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header relative overflow-hidden py-24 md:py-32">
        <i class="fas fa-landmark header-bg-icon"></i>
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="admin/uploads/sr1.jpg" alt="Para Pendiri" class="w-full h-full object-cover object-top filter sepia-[0.3]">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-900/90 to-purple-900/80 mix-blend-multiply"></div>
        </div>

        <div class="container relative z-10 text-center">
            <div class="page-header-content max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 text-white drop-shadow-lg">Sejarah Sekolah</h1>
                <p class="text-xl text-primary-100 mb-6">Perjalanan <?php echo htmlspecialchars($school_info['name']); ?> dari masa ke masa</p>
                <nav class="breadcrumb flex justify-center text-primary-200 space-x-2">
                    <a href="index.php" class="hover:text-white transition-colors">Beranda</a>
                    <span>/</span>
                    <a href="profil.php" class="hover:text-white transition-colors">Profil</a>
                    <span>/</span>
                    <span class="text-white">Sejarah</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- History Content -->
    <section class="history-section py-20 bg-white">
        <div class="container">
            <div class="max-w-4xl mx-auto">
                <div class="prose prose-lg text-gray-600 mb-16">
                    <p class="lead text-xl text-gray-700 font-medium leading-relaxed mb-6">
                        <?php echo htmlspecialchars($school_info['name']); ?> didirikan dengan semangat untuk memberikan pendidikan berkualitas yang terjangkau bagi masyarakat. Bermula dari sebuah inisiatif kecil, kini kami telah tumbuh menjadi salah satu lembaga pendidikan terpercaya.
                    </p>
                    <p class="mb-6">
                        Perjalanan kami dimulai pada tahun 1947, ketika benih pendidikan mulai disemai. Dengan semangat pengabdian yang tulus, sekolah ini didirikan untuk menjawab kebutuhan akan pendidikan yang berkualitas bagi masyarakat, menjadi pelita di tengah tantangan zaman pada masa itu.
                    </p>
                    <p>
                        Seiring berjalannya waktu, kepercayaan masyarakat terus tumbuh. Kami terus berbenah, meningkatkan kualitas sarana dan prasarana, serta mengembangkan kurikulum yang relevan dengan perkembangan zaman. Penghargaan demi penghargaan mulai kami raih, baik di tingkat lokal maupun nasional, menjadi bukti komitmen kami terhadap mutu pendidikan.
                    </p>
                </div>

                <!-- Timeline -->
                <div class="relative border-l-4 border-primary-200 ml-3 md:ml-6 space-y-12">
                    <!-- Timeline Item 1 -->
                    <div class="relative pl-8 md:pl-12 group">
                        <div class="absolute -left-[13px] top-0 w-6 h-6 bg-white border-4 border-primary-500 rounded-full group-hover:scale-125 transition-transform duration-300"></div>
                        <div class="bg-gray-50 p-6 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100">
                            <span class="inline-block px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-bold mb-3">1947</span>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Awal Pendirian</h3>
                            <div class="mb-4 flex flex-col md:flex-row gap-6 items-start">
                                <div class="shrink-0 w-full md:w-80">
                                    <img src="admin/uploads/sr.png" alt="Pendiri Awal 1947" class="w-full h-auto rounded-xl shadow-md border-2 border-white">
                                    <p class="text-sm text-center text-gray-500 mt-2 italic">Pendiri Awal</p>
                                </div>
                                <p class="text-gray-600 text-lg leading-relaxed">
                                    Tonggak sejarah dimulai pada tahun 1947. Sekolah didirikan oleh Suster Pendiri dengan visi mulia untuk memberikan akses pendidikan yang layak. Foto di samping mengabadikan sosok pendiri yang telah meletakkan dasar nilai-nilai keutamaan yang masih kami pegang teguh hingga hari ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Item 1987 -->
                    <div class="relative pl-8 md:pl-12 group">
                        <div class="absolute -left-[13px] top-0 w-6 h-6 bg-white border-4 border-primary-500 rounded-full group-hover:scale-125 transition-transform duration-300"></div>
                        <div class="bg-gray-50 p-6 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100">
                            <span class="inline-block px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-bold mb-3">1987</span>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Era Perkembangan</h3>
                            <div class="mb-4 flex flex-col md:flex-row gap-6 items-start">
                                <div class="shrink-0 w-full md:w-80">
                                    <img src="admin/uploads/80.jpg" alt="Suasana Sekolah 1987" class="w-full h-auto rounded-xl shadow-md border-2 border-white">
                                    <p class="text-sm text-center text-gray-500 mt-2 italic">Dokumentasi 1987</p>
                                </div>
                                <p class="text-gray-600 text-lg leading-relaxed">
                                    Pada tahun 1987, sekolah semakin memantapkan eksistensinya di dunia pendidikan. Berbagai kegiatan ekstrakurikuler mulai digalakkan untuk menyeimbangkan kemampuan akademik dan non-akademik siswa. Momen ini menjadi salah satu periode penting dalam sejarah pertumbuhan sekolah.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Item 2 -->
                    <div class="relative pl-8 md:pl-12 group">
                        <div class="absolute -left-[13px] top-0 w-6 h-6 bg-white border-4 border-primary-500 rounded-full group-hover:scale-125 transition-transform duration-300"></div>
                        <div class="bg-gray-50 p-6 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100">
                            <span class="inline-block px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-bold mb-3">2012</span>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Pengembangan Fasilitas</h3>
                            <p class="text-gray-600">
                                Penambahan gedung baru dan fasilitas laboratorium komputer untuk mendukung pembelajaran berbasis teknologi.
                            </p>
                        </div>
                    </div>

                    <!-- Timeline Item 3 -->
                    <div class="relative pl-8 md:pl-12 group">
                        <div class="absolute -left-[13px] top-0 w-6 h-6 bg-white border-4 border-primary-500 rounded-full group-hover:scale-125 transition-transform duration-300"></div>
                        <div class="bg-gray-50 p-6 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100">
                            <span class="inline-block px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-bold mb-3">2015</span>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Akreditasi A</h3>
                            <p class="text-gray-600">
                                Meraih predikat Akreditasi A dari Badan Akreditasi Nasional Sekolah/Madrasah, membuktikan standar kualitas pendidikan yang tinggi.
                            </p>
                        </div>
                    </div>

                    <!-- Timeline Item 4 -->
                    <div class="relative pl-8 md:pl-12 group">
                        <div class="absolute -left-[13px] top-0 w-6 h-6 bg-white border-4 border-primary-500 rounded-full group-hover:scale-125 transition-transform duration-300"></div>
                        <div class="bg-gray-50 p-6 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100">
                            <span class="inline-block px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-bold mb-3">2018</span>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Program Internasional</h3>
                            <div class="mb-4 flex flex-col md:flex-row gap-6 items-start">
                                <div class="shrink-0 w-full md:w-80">
                                    <img src="admin/uploads/2000.jpg" alt="Kegiatan Internasional 2018" class="w-full h-auto rounded-xl shadow-md border-2 border-white">
                                    <p class="text-sm text-center text-gray-500 mt-2 italic">Kerjasama Internasional</p>
                                </div>
                                <p class="text-gray-600 text-lg leading-relaxed">
                                    Memulai rintisan program kelas internasional dan kerjasama dengan institusi pendidikan luar negeri. Langkah ini diambil untuk mempersiapkan siswa menghadapi tantangan global dengan wawasan yang lebih luas.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Item 5 -->
                    <div class="relative pl-8 md:pl-12 group">
                        <div class="absolute -left-[13px] top-0 w-6 h-6 bg-white border-4 border-primary-500 rounded-full group-hover:scale-125 transition-transform duration-300"></div>
                        <div class="bg-white p-6 rounded-2xl shadow-xl border-2 border-primary-500 transform scale-105">
                            <span class="inline-block px-3 py-1 bg-primary-600 text-white rounded-full text-sm font-bold mb-3">Sekarang</span>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Menuju Masa Depan</h3>
                            <p class="text-gray-600">
                                Terus berinovasi dalam metode pembelajaran digital dan mempersiapkan siswa menjadi pemimpin masa depan yang berkarakter global.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-gradient-to-r from-primary-600 to-primary-800 text-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full bg-[url('images/pattern.png')] opacity-10"></div>
        <div class="container relative z-10 text-center">
            <h2 class="text-3xl font-bold mb-6">Jadilah Bagian dari Sejarah Kami</h2>
            <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">Bergabunglah bersama keluarga besar <?php echo htmlspecialchars($school_info['name']); ?> dan ukir prestasimu.</p>
            <a href="ppdb.php" class="inline-block px-8 py-4 bg-white text-primary-700 font-bold rounded-full shadow-lg hover:bg-gray-100 transform hover:-translate-y-1 transition-all duration-300">
                Daftar Sekarang <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>