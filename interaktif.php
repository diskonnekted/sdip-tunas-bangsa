<?php
require_once 'includes/settings.php';

$page_title = 'Interactive Learning';
include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="relative bg-primary-600 pt-32 pb-20 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-500 via-primary-600 to-primary-800 opacity-90"></div>
        <div class="absolute inset-0 bg-[url('images/pattern.png')] opacity-10"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 animate-fade-in-up">
            Interactive Learning
        </h1>
        <p class="text-xl text-gray-200 max-w-3xl mx-auto animate-fade-in-up delay-100">
            Modul pembelajaran interaktif yang dirancang khusus untuk pengalaman belajar yang menyenangkan melalui layar sentuh.
        </p>
    </div>
</div>

<!-- Content Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <!-- Filter/Category Buttons -->
    <div class="flex flex-wrap justify-center gap-4 mb-12 animate-fade-in-up delay-200">
        <button class="px-6 py-3 bg-primary-600 text-white rounded-full font-semibold shadow-lg hover:bg-primary-700 active:scale-95 transition-all duration-200 ring-2 ring-primary-600 ring-offset-2">
            Semua
        </button>
        <button class="px-6 py-3 bg-white text-gray-600 rounded-full font-semibold shadow-md hover:bg-gray-50 hover:text-primary-600 active:scale-95 transition-all duration-200">
            Biologi
        </button>
        <button class="px-6 py-3 bg-white text-gray-600 rounded-full font-semibold shadow-md hover:bg-gray-50 hover:text-primary-600 active:scale-95 transition-all duration-200">
            Kimia
        </button>
        <button class="px-6 py-3 bg-white text-gray-600 rounded-full font-semibold shadow-md hover:bg-gray-50 hover:text-primary-600 active:scale-95 transition-all duration-200">
            Geografi
        </button>
        <button class="px-6 py-3 bg-white text-gray-600 rounded-full font-semibold shadow-md hover:bg-gray-50 hover:text-primary-600 active:scale-95 transition-all duration-200">
            Matematika
        </button>
        <button class="px-6 py-3 bg-white text-gray-600 rounded-full font-semibold shadow-md hover:bg-gray-50 hover:text-primary-600 active:scale-95 transition-all duration-200">
            Fisika
        </button>
    </div>

    <!-- Modules Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Module Card 1: Biologi -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover-lift group border border-gray-100 animate-fade-in-up delay-300">
            <div class="relative h-48 overflow-hidden">
                <img src="images/belajar.jpg" alt="Biologi" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-4 left-4 text-white">
                    <span class="bg-green-500 px-3 py-1 rounded-full text-xs font-semibold mb-2 inline-block">Biologi</span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Dunia Biologi</h3>
                <p class="text-gray-600 mb-4 line-clamp-2">
                    Eksplorasi struktur sel, anatomi tubuh manusia, dan keanekaragaman hayati secara interaktif.
                </p>
                <div class="flex flex-col gap-3">
                    <button onclick="launchModule('biology_pollination')" class="w-full py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 active:scale-95 transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-seedling"></i>
                        <span>Penyerbukan</span>
                    </button>
                    <button onclick="launchModule('biology_cell')" class="w-full py-3 bg-white border-2 border-primary-600 text-primary-600 rounded-xl font-semibold hover:bg-primary-50 active:scale-95 transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-dna"></i>
                        <span>Struktur Sel</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Module Card 2: Kimia -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover-lift group border border-gray-100 animate-fade-in-up delay-400">
            <div class="relative h-48 overflow-hidden">
                <img src="images/bersama guru.jpg" alt="Kimia" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-4 left-4 text-white">
                    <span class="bg-purple-500 px-3 py-1 rounded-full text-xs font-semibold mb-2 inline-block">Kimia</span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Laboratorium Kimia</h3>
                <p class="text-gray-600 mb-4 line-clamp-2">
                    Simulasi reaksi kimia, tabel periodik interaktif, dan struktur molekul 3D.
                </p>
                <div class="flex flex-col gap-3">
                    <button onclick="launchModule('chemistry_changes')" class="w-full py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 active:scale-95 transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-flask"></i>
                        <span>Perubahan Zat</span>
                    </button>
                    <button onclick="launchModule('chemistry_periodic')" class="w-full py-3 bg-white border-2 border-primary-600 text-primary-600 rounded-xl font-semibold hover:bg-primary-50 active:scale-95 transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-table"></i>
                        <span>Tabel Periodik</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Module Card 3: Geografi -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover-lift group border border-gray-100 animate-fade-in-up delay-500">
            <div class="relative h-48 overflow-hidden">
                <img src="images/field trip.jpg" alt="Geografi" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-4 left-4 text-white">
                    <span class="bg-green-500 px-3 py-1 rounded-full text-xs font-semibold mb-2 inline-block">Geografi</span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Jelajah Bumi</h3>
                <p class="text-gray-600 mb-4 line-clamp-2">
                    Peta dunia interaktif, lapisan bumi, dan fenomena geografi yang menakjubkan.
                </p>
                <div class="flex flex-col gap-3">
                    <button onclick="launchModule('geography_explore')" class="w-full py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 active:scale-95 transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-globe-asia"></i>
                        <span>Jelajahi Indonesia</span>
                    </button>
                    <button onclick="launchModule('geography_layers')" class="w-full py-3 bg-white border-2 border-primary-600 text-primary-600 rounded-xl font-semibold hover:bg-primary-50 active:scale-95 transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-layer-group"></i>
                        <span>Lapisan Bumi</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Module Card 4: Matematika -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover-lift group border border-gray-100 animate-fade-in-up delay-300">
            <div class="relative h-48 overflow-hidden">
                <img src="images/kegiatan guru.jpg" alt="Matematika" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-4 left-4 text-white">
                    <span class="bg-red-500 px-3 py-1 rounded-full text-xs font-semibold mb-2 inline-block">Matematika</span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Matematika Cerdas</h3>
                <p class="text-gray-600 mb-4 line-clamp-2">
                    Visualisasi konsep matematika, geometri, dan pemecahan masalah yang seru.
                </p>
                <div class="flex flex-col gap-3">
                    <button onclick="launchModule('mathematics_volume')" class="w-full py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 active:scale-95 transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-cube"></i>
                        <span>Volume Bangun</span>
                    </button>
                    <button onclick="launchModule('mathematics_fractions')" class="w-full py-3 bg-white border-2 border-primary-600 text-primary-600 rounded-xl font-semibold hover:bg-primary-50 active:scale-95 transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-chart-pie"></i>
                        <span>Pecahan Senilai</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Module Card 5: Fisika -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover-lift group border border-gray-100 animate-fade-in-up delay-400">
            <div class="relative h-48 overflow-hidden">
                <img src="images/mengaji.jpg" alt="Fisika" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-4 left-4 text-white">
                    <span class="bg-yellow-500 px-3 py-1 rounded-full text-xs font-semibold mb-2 inline-block">Fisika</span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Fisika Eksperimental</h3>
                <p class="text-gray-600 mb-4 line-clamp-2">
                    Simulasi hukum fisika, mekanika, optik, dan listrik dinamis.
                </p>
                <div class="flex flex-col gap-3">
                    <button onclick="launchModule('physics_motion')" class="w-full py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 active:scale-95 transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-running"></i>
                        <span>Gaya & Gerak</span>
                    </button>
                    <button onclick="launchModule('physics_electricity')" class="w-full py-3 bg-white border-2 border-primary-600 text-primary-600 rounded-xl font-semibold hover:bg-primary-50 active:scale-95 transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-bolt"></i>
                        <span>Listrik Dinamis</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Modal Container for Interactive Module -->
    <div id="moduleModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeModule()"></div>
        <div class="absolute inset-2 md:inset-6 bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col animate-fade-in-up h-[95vh] md:h-[90vh]">
            <!-- Header -->
            <div class="bg-primary-600 px-6 py-3 flex justify-between items-center shrink-0 z-10">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-gamepad mr-3"></i>
                    <span id="moduleTitle">Module Title</span>
                </h3>
                <button onclick="closeModule()" class="text-white hover:text-red-200 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <!-- Content Area (Iframe) -->
            <div class="flex-1 bg-gray-100 relative w-full h-full overflow-hidden">
                <iframe id="moduleFrame" src="" class="w-full h-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                
                <!-- Loading State -->
                <div id="moduleLoading" class="absolute inset-0 flex items-center justify-center bg-white z-0">
                    <div class="text-center">
                        <div class="w-16 h-16 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin mx-auto mb-4"></div>
                        <p class="text-gray-600">Memuat Modul...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function launchModule(moduleId) {
            const modal = document.getElementById('moduleModal');
            const title = document.getElementById('moduleTitle');
            const iframe = document.getElementById('moduleFrame');
            const loading = document.getElementById('moduleLoading');
            
            // Map IDs to specific URLs
            const moduleConfig = {
                'biology_pollination': {
                    title: 'Petualangan Penyerbukan',
                    url: 'modules/biology/pollination.php'
                },
                'biology_cell': {
                    title: 'Struktur Sel',
                    url: 'modules/biology/cell.php'
                },
                'chemistry_changes': {
                    title: 'Perubahan Apa Ini?',
                    url: 'modules/chemistry/changes.php'
                },
                'chemistry_periodic': {
                    title: 'Tabel Periodik Unsur',
                    url: 'modules/chemistry/periodic_table.php'
                },
                'geography_explore': {
                    title: 'Jelajahi Indonesia',
                    url: 'modules/geography/explore.php'
                },
                'geography_layers': {
                    title: 'Lapisan Bumi',
                    url: 'modules/geography/layers.php'
                },
                'mathematics_volume': {
                    title: 'Volume Bangun Ruang',
                    url: 'modules/mathematics/volume.php'
                },
                'mathematics_fractions': {
                    title: 'Pecahan Senilai',
                    url: 'modules/mathematics/fractions.php'
                },
                'physics_motion': {
                    title: 'Eksperimen Gaya dan Gerak',
                    url: 'modules/physics/motion.php'
                },
                'physics_electricity': {
                    title: 'Listrik Dinamis',
                    url: 'modules/physics/electricity.php'
                }
            };
            
            const config = moduleConfig[moduleId];
            
            if (config) {
                title.textContent = config.title;
                loading.style.display = 'flex'; // Show loading
                
                // Set iframe src
                iframe.src = config.url;
                
                // Hide loading when loaded
                iframe.onload = function() {
                    loading.style.display = 'none';
                };
                
                modal.classList.remove('hidden');
            } else {
                alert('Modul ini belum tersedia.');
            }
        }

        function closeModule() {
            const modal = document.getElementById('moduleModal');
            const iframe = document.getElementById('moduleFrame');
            
            modal.classList.add('hidden');
            // Clear iframe to stop sounds/scripts
            setTimeout(() => {
                iframe.src = 'about:blank';
            }, 300);
        }
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModule();
            }
        });
    </script>

<?php include 'includes/footer.php'; ?>