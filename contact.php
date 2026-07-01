<?php
// Include necessary files
include_once 'includes/settings.php';

// Get school info
$school_info = getSchoolInfo();
$contact_info = getContactInfo();

// Set page title
$page_title = 'Hubungi Kami';
?>
<?php include 'includes/header.php'; ?>
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Restore body background class
    document.body.classList.add('bg-gray-50');
</script>

    <!-- Hero Section -->
    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 overflow-hidden">
        <i class="fas fa-phone-alt absolute top-1/2 -right-[5%] transform -translate-y-1/2 text-[35rem] text-white/10 pointer-events-none leading-none z-0"></i>
        <!-- Background Image & Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="images/sch2.jpg" alt="Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-900/90 via-green-900/90 to-green-900/90"></div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
            <div class="absolute top-40 right-20 w-32 h-32 bg-purple-300/20 rounded-full blur-2xl"></div>
            <div class="absolute bottom-20 left-1/3 w-24 h-24 bg-green-300/20 rounded-full blur-xl"></div>
        </div>
        
        <div class="relative container mx-auto px-4 z-10">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="w-full lg:w-1/2 text-center lg:text-left text-white">
                    <div class="mb-6 flex justify-center lg:justify-start">
                        <span class="inline-flex items-center bg-white/20 backdrop-blur px-4 py-2 rounded-full text-sm font-semibold">
                            <i class="fas fa-phone mr-2"></i>
                            Hubungi Kami
                        </span>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                        Kontak <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-orange-300">& Lokasi</span>
                    </h1>
                    <p class="text-xl md:text-2xl opacity-90 mb-10">
                        Kami siap membantu dan menjawab pertanyaan Anda. Jangan ragu untuk menghubungi kami!
                    </p>
                </div>
                
                <div class="w-full lg:w-1/2 mt-8 lg:mt-0 flex justify-center lg:justify-end">
                    <?php if (!empty($school_info['logo'])): ?>
                        <img src="admin/uploads/<?php echo htmlspecialchars($school_info['logo']); ?>" alt="Logo <?php echo htmlspecialchars($school_info['name']); ?>" class="w-auto max-h-[250px] lg:max-h-[350px] object-contain relative z-0 filter drop-shadow-2xl">
                    <?php else: ?>
                        <i class="fas fa-graduation-cap text-9xl text-white opacity-80"></i>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Wave separator -->
        <div class="absolute bottom-0 left-0 w-full overflow-hidden">
            <svg class="relative block w-full h-20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z" fill="#f9fafb"></path>
            </svg>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="py-16">
        <div class="container">
            <div class="max-w-6xl mx-auto">
                <!-- Contact Info -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
                    <div class="bg-white rounded-3xl shadow-xl p-8 text-center group hover:shadow-2xl transition-all duration-500">
                        <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-map-marker-alt text-3xl text-green-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Alamat</h3>
                        <p class="text-gray-600 leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($contact_info['address'] ?: 'Alamat belum diatur')); ?>
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-3xl shadow-xl p-8 text-center group hover:shadow-2xl transition-all duration-500">
                        <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-phone text-3xl text-green-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Telepon</h3>
                        <p class="text-gray-600 leading-relaxed">
                            <strong>Telepon:</strong> <?php echo htmlspecialchars($contact_info['phone'] ?: 'Belum diatur'); ?><br>
                            <strong>Jam Operasional:</strong> <?php echo htmlspecialchars($contact_info['operating_hours']); ?>
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-3xl shadow-xl p-8 text-center group hover:shadow-2xl transition-all duration-500">
                        <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-purple-200 transition-colors">
                            <i class="fas fa-envelope text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Email</h3>
                        <p class="text-gray-600 leading-relaxed">
                            <strong>Email:</strong> <?php echo htmlspecialchars($contact_info['email'] ?: 'Belum diatur'); ?><br>
                            <?php if (!empty($school_info['website'])): ?>
                            <strong>Website:</strong> <a href="<?php echo htmlspecialchars($school_info['website']); ?>" target="_blank" class="text-green-600 hover:text-green-800"><?php echo htmlspecialchars($school_info['website']); ?></a>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- Contact Form & Office Hours -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Contact Form -->
                    <div class="bg-white rounded-3xl shadow-xl p-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">Kirim Pesan</h2>
                        <form id="contactForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-user mr-2 text-green-500"></i>Nama Lengkap
                                    </label>
                                    <input type="text" id="name" name="name" required
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-envelope mr-2 text-green-500"></i>Email
                                    </label>
                                    <input type="email" id="email" name="email" required
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-phone mr-2 text-purple-500"></i>Nomor Telepon
                                </label>
                                <input type="tel" id="phone" name="phone"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-tag mr-2 text-orange-500"></i>Subjek
                                </label>
                                <input type="text" id="subject" name="subject" required
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-comment mr-2 text-red-500"></i>Pesan
                                </label>
                                <textarea id="message" name="message" rows="5" required
                                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all resize-none"></textarea>
                            </div>
                            
                            <button type="submit"
                                    class="w-full px-8 py-4 bg-gradient-to-r from-green-500 to-purple-600 hover:from-green-600 hover:to-purple-700 text-white font-bold rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                                <i class="fas fa-paper-plane mr-2"></i>Kirim Pesan
                            </button>
                        </form>
                        
                        <!-- Success/Error Messages -->
                        <div id="success" class="mt-6 p-4 bg-green-100 border border-green-300 rounded-xl text-green-700 hidden">
                            <i class="fas fa-check-circle mr-2"></i>Pesan berhasil dikirim! Kami akan segera membalas.
                        </div>
                        <div id="error" class="mt-6 p-4 bg-red-100 border border-red-300 rounded-xl text-red-700 hidden">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Terjadi kesalahan. Silakan coba lagi.
                        </div>

                        <!-- Form Instructions -->
                        <div class="mt-8 pt-8 border-t border-gray-100">
                            <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-green-500 mr-2"></i>Petunjuk Pengisian
                            </h4>
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                    <span>Pastikan alamat email yang Anda masukkan valid agar kami dapat membalas pesan Anda.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                    <span>Gunakan bahasa yang sopan dan jelas dalam menyampaikan pesan.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                    <span>Kami berusaha membalas setiap pesan dalam waktu 1x24 jam pada hari kerja.</span>
                                </li>
                            </ul>
                            <div class="mt-6 p-3 bg-gray-50 rounded-lg text-xs text-gray-500 flex items-start">
                                <i class="fas fa-shield-alt text-gray-400 mt-0.5 mr-2"></i>
                                <p>Privasi Anda terjaga. Informasi kontak Anda tidak akan dibagikan kepada pihak ketiga tanpa persetujuan Anda.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Office Hours & Social Media -->
                    <div class="space-y-8">
                        <!-- Office Hours -->
                        <div class="bg-white rounded-3xl shadow-xl p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6">
                                <i class="fas fa-clock mr-3 text-green-500"></i>Jam Operasional
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="font-semibold text-gray-700">Senin - Jumat</span>
                                    <span class="text-gray-600">07:00 - 16:00</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="font-semibold text-gray-700">Sabtu</span>
                                    <span class="text-gray-600">07:00 - 12:00</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="font-semibold text-gray-700">Minggu</span>
                                    <span class="text-red-500 font-semibold">Tutup</span>
                                </div>
                            </div>
                            <div class="mt-6 p-4 bg-green-50 rounded-xl">
                                <p class="text-sm text-green-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Untuk kunjungan, mohon membuat janji terlebih dahulu melalui telepon atau email.
                                </p>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="bg-white rounded-3xl shadow-xl p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6">
                                <i class="fas fa-share-alt mr-3 text-green-500"></i>Media Sosial
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <a href="<?php echo htmlspecialchars($social_media['facebook']); ?>" target="_blank" rel="noopener" class="flex flex-col items-center justify-center p-4 bg-green-50 rounded-3xl aspect-square hover:bg-green-100 transition-all duration-300 group text-center hover:-translate-y-1">
                                    <div class="bg-green-600 w-12 h-12 flex items-center justify-center rounded-full mb-3 group-hover:scale-110 transition-transform shadow-sm">
                                        <i class="fab fa-facebook-f text-white text-xl"></i>
                                    </div>
                                    <div class="w-full">
                                        <p class="font-bold text-gray-900 text-sm mb-1">Facebook</p>
                                        <p class="text-xs text-gray-600 truncate px-2">@tunasbangsa.bna</p>
                                    </div>
                                </a>
                                
                                <a href="<?php echo htmlspecialchars($social_media['instagram']); ?>" target="_blank" rel="noopener" class="flex flex-col items-center justify-center p-4 bg-pink-50 rounded-3xl aspect-square hover:bg-pink-100 transition-all duration-300 group text-center hover:-translate-y-1">
                                    <div class="bg-pink-600 w-12 h-12 flex items-center justify-center rounded-full mb-3 group-hover:scale-110 transition-transform shadow-sm">
                                        <i class="fab fa-instagram text-white text-xl"></i>
                                    </div>
                                    <div class="w-full">
                                        <p class="font-bold text-gray-900 text-sm mb-1">Instagram</p>
                                        <p class="text-xs text-gray-600 truncate px-2">@sdip_tunasbangsa</p>
                                    </div>
                                </a>
                                
                                <a href="#" class="flex flex-col items-center justify-center p-4 bg-green-50 rounded-3xl aspect-square hover:bg-green-100 transition-all duration-300 group text-center hover:-translate-y-1">
                                    <div class="bg-green-500 w-12 h-12 flex items-center justify-center rounded-full mb-3 group-hover:scale-110 transition-transform shadow-sm">
                                        <i class="fab fa-twitter text-white text-xl"></i>
                                    </div>
                                    <div class="w-full">
                                        <p class="font-bold text-gray-900 text-sm mb-1">Twitter</p>
                                        <p class="text-xs text-gray-600 truncate px-2">@SDIntegralIV</p>
                                    </div>
                                </a>
                                
                                <a href="<?php echo htmlspecialchars($social_media['youtube']); ?>" target="_blank" rel="noopener" class="flex flex-col items-center justify-center p-4 bg-red-50 rounded-3xl aspect-square hover:bg-red-100 transition-all duration-300 group text-center hover:-translate-y-1">
                                    <div class="bg-red-600 w-12 h-12 flex items-center justify-center rounded-full mb-3 group-hover:scale-110 transition-transform shadow-sm">
                                        <i class="fab fa-youtube text-white text-xl"></i>
                                    </div>
                                    <div class="w-full">
                                        <p class="font-bold text-gray-900 text-sm mb-1">YouTube</p>
                                        <p class="text-xs text-gray-600 truncate px-2">SDIP Tunas Bangsa</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="mt-16 bg-white rounded-3xl shadow-xl p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-map mr-3 text-red-500"></i>Lokasi Sekolah
                    </h3>
                    <div id="map" class="rounded-2xl h-96 w-full z-10 shadow-inner"></div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
    <script>
    // Initialize Leaflet Map
    document.addEventListener('DOMContentLoaded', function() {
        // Koordinat sekolah SDIP Tunas Bangsa
        const schoolLat = -7.394153608658456;
        const schoolLng = 109.71290577116419;
        
        var map = L.map('map').setView([schoolLat, schoolLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.marker([schoolLat, schoolLng]).addTo(map)
            .bindPopup('<div class="text-center"><b><?php echo addslashes($school_info["name"]); ?></b><br><?php echo addslashes($contact_info["address"]); ?></div>')
            .openPopup();
    });

    // Contact form handling
    document.getElementById('contactForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        try {
            const response = await fetch('/api/contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showSuccess();
                this.reset();
            } else {
                showError(result.errors || [result.message]);
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            showError();
        }
    });

    function showSuccess() {
        document.getElementById('success').classList.remove('hidden');
        document.getElementById('error').classList.add('hidden');
        setTimeout(() => {
            document.getElementById('success').classList.add('hidden');
        }, 5000);
    }

    function showError(errors = []) {
        const errorDiv = document.getElementById('error');
        errorDiv.classList.remove('hidden');
        document.getElementById('success').classList.add('hidden');
        
        if (errors && errors.length > 0) {
            errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>' + errors.join('<br><i class="fas fa-exclamation-triangle mr-2" style="visibility:hidden"></i>');
        } else {
            errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Terjadi kesalahan. Silakan coba lagi.';
        }
    }

    function openMaps() {
        const address = encodeURIComponent('<?php echo addslashes($contact_info["address"]); ?>');
        window.open(`https://maps.google.com/maps?q=${address}`, '_blank');
    }
    </script>

    <!-- Custom styles -->
    <style>
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    </style>
</body>
</html>
