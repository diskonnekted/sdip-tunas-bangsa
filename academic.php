<?php
// Include necessary files
include_once 'includes/settings.php';

// Get school info
$school_info = getSchoolInfo();
$contact_info = getContactInfo();

// Set page title
$page_title = "Program Akademik - " . $school_info['name'];
$body_class = 'bg-gray-50';

include 'includes/header.php';
?>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-green-600 via-green-600 to-green-800 pt-32 pb-20 overflow-hidden">
        <i class="fas fa-graduation-cap absolute top-1/2 -right-[5%] transform -translate-y-1/2 text-[35rem] text-white/10 pointer-events-none leading-none z-0"></i>
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute inset-0">
            <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
            <div class="absolute top-40 right-20 w-32 h-32 bg-green-300/20 rounded-full blur-2xl"></div>
            <div class="absolute bottom-20 left-1/3 w-24 h-24 bg-green-300/20 rounded-full blur-xl"></div>
        </div>
        
        <div class="relative container text-center text-white z-10">
            <div class="mb-6">
                <span class="inline-flex items-center bg-white/20 backdrop-blur px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <i class="fas fa-book mr-2"></i>
                    Program Akademik
                </span>
            </div>
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                Akademik <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-orange-300">Berkualitas</span>
            </h1>
            <p class="text-xl md:text-2xl opacity-90 mb-10 max-w-3xl mx-auto">
                Program pembelajaran yang komprehensif dan inovatif untuk mengembangkan potensi setiap siswa
            </p>
        </div>
        
        <!-- Wave separator -->
        <div class="absolute bottom-0 left-0 w-full overflow-hidden">
            <svg class="relative block w-full h-20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z" fill="#f9fafb"></path>
            </svg>
        </div>
    </section>

    <!-- Academic Content -->
    <section class="py-16">
        <div class="container">
            <!-- Loading State -->
            <div id="loading" class="text-center py-20">
                <div class="relative inline-block">
                    <div class="animate-spin rounded-full h-20 w-20 border-t-4 border-green-500 border-opacity-25"></div>
                    <div class="animate-ping absolute top-0 left-0 h-20 w-20 rounded-full bg-green-400 opacity-20"></div>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-900">Memuat Data Akademik</h3>
                <p class="mt-2 text-gray-600">Sedang mengambil informasi program akademik...</p>
            </div>

            <!-- Error State -->
            <div id="error" class="text-center py-20 hidden">
                <div class="max-w-md mx-auto">
                    <div class="bg-red-100 rounded-full p-8 w-32 h-32 mx-auto mb-8">
                        <i class="fas fa-exclamation-triangle text-5xl text-red-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Oops! Terjadi Kesalahan</h3>
                    <p class="text-gray-600 mb-8">Gagal memuat data akademik. Periksa koneksi internet Anda dan coba lagi.</p>
                    <button onclick="loadAcademicData()" 
                            class="px-8 py-4 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-bold rounded-2xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-redo mr-2"></i>Coba Lagi
                    </button>
                </div>
            </div>

            <!-- Academic Content -->
            <div id="academicContent" class="hidden">
                <div id="academicData"></div>
            </div>
        </div>
    </section>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full relative z-[101]">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-2" id="modalTitle"></h3>
                            <div class="flex flex-wrap gap-2 mb-4" id="modalBadges">
                                <!-- Badges will be inserted here -->
                            </div>
                            
                            <div class="mt-4 space-y-6">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Deskripsi</h4>
                                    <p class="text-gray-600" id="modalDescription"></p>
                                </div>
                                
                                <div id="modalSubjectsContainer">
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Mata Pelajaran</h4>
                                    <div class="flex flex-wrap gap-2" id="modalSubjects"></div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div id="modalLearningMethodsContainer">
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Metode Pembelajaran</h4>
                                        <ul class="list-disc list-inside text-gray-600 text-sm space-y-1" id="modalLearningMethods"></ul>
                                    </div>
                                    
                                    <div id="modalAssessmentMethodsContainer">
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Metode Penilaian</h4>
                                        <ul class="list-disc list-inside text-gray-600 text-sm space-y-1" id="modalAssessmentMethods"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeModal()" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
    <script>
    // Academic API Integration
    const API_URL = 'api/academic.php';

    // Global data store
    let academicData = [];

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        loadAcademicData();
    });

    // Load academic data
    async function loadAcademicData() {
        showLoading();
        hideError();
        
        try {
            console.log('Fetching academic data from:', API_URL);
            const response = await fetch(API_URL);
            const data = await response.json();
            
            if (data.success) {
                console.log('Academic data loaded:', data.data);
                academicData = data.data; // Store data for modal
                displayAcademicData(data.data);
                showContent();
            } else {
                throw new Error(data.message || 'Failed to load academic data');
            }
        } catch (error) {
            console.error('Error loading academic data:', error);
            showError();
        }
    }

    // Display academic data
    function displayAcademicData(data) {
        const container = document.getElementById('academicData');
        
        container.innerHTML = `
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Program Akademik Kami</h2>
                    <p class="text-gray-600 text-xl max-w-2xl mx-auto">
                        Kurikulum yang dirancang untuk mengembangkan kemampuan akademik dan karakter siswa
                    </p>
                </div>

                <!-- Academic Programs Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                    ${data.map((item, index) => `
                        <div onclick="openModal(${index})" class="bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 overflow-hidden group cursor-pointer transform hover:-translate-y-2">
                            <div class="relative h-48 bg-gradient-to-br from-green-500 to-green-500 flex items-center justify-center">
                                ${item.image ? 
                                    `<img src="${item.image}" alt="${item.title}" class="absolute inset-0 w-full h-full object-cover">
                                     <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition-colors"></div>` : 
                                    `<div class="absolute inset-0 bg-gradient-to-br from-green-500 to-green-500"></div>`
                                }
                                <div class="relative z-10 text-white text-center p-4">
                                    <i class="fas fa-book text-4xl mb-3 opacity-90 drop-shadow-lg"></i>
                                    <h3 class="text-2xl font-bold drop-shadow-lg">${item.title}</h3>
                                </div>
                            </div>
                            <div class="p-8">
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                        ${item.grade_level_name}
                                    </span>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                        ${item.curriculum_type_name}
                                    </span>
                                </div>
                                <p class="text-gray-600 leading-relaxed mb-6 line-clamp-3">${item.description}</p>
                                
                                <div class="flex items-center text-green-600 font-semibold group-hover:translate-x-2 transition-transform">
                                    Selengkapnya <i class="fas fa-arrow-right ml-2"></i>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>

                <!-- Additional Info -->
                <div class="bg-gradient-to-r from-green-600 to-purple-600 rounded-3xl p-12 text-center text-white">
                    <h3 class="text-3xl font-bold mb-6">Mengapa Memilih Program Akademik Kami?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                        <div class="text-center">
                            <i class="fas fa-medal text-4xl mb-4 opacity-90"></i>
                            <h4 class="text-xl font-semibold mb-2">Berkualitas Tinggi</h4>
                            <p class="opacity-90">Kurikulum yang telah teruji dan sesuai standar nasional</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-users text-4xl mb-4 opacity-90"></i>
                            <h4 class="text-xl font-semibold mb-2">Pengajar Berpengalaman</h4>
                            <p class="opacity-90">Tim pengajar yang profesional dan berdedikasi</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-lightbulb text-4xl mb-4 opacity-90"></i>
                            <h4 class="text-xl font-semibold mb-2">Metode Inovatif</h4>
                            <p class="opacity-90">Pembelajaran yang menyenangkan dan efektif</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Modal Functions
    function openModal(index) {
        console.log('Opening modal for index:', index);
        const item = academicData[index];
        if (!item) {
            console.error('No data found for index:', index);
            return;
        }

        // Set content
        document.getElementById('modalTitle').textContent = item.title;
        document.getElementById('modalDescription').textContent = item.description;
        
        // Badges
        const badgesContainer = document.getElementById('modalBadges');
        badgesContainer.innerHTML = `
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                ${item.grade_level_name}
            </span>
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                ${item.curriculum_type_name}
            </span>
        `;

        // Subjects
        const subjectsContainer = document.getElementById('modalSubjects');
        const subjectsWrapper = document.getElementById('modalSubjectsContainer');
        if (item.subjects && item.subjects.length > 0) {
            subjectsContainer.innerHTML = item.subjects.map(subject => 
                `<span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm border border-gray-200">${subject}</span>`
            ).join('');
            subjectsWrapper.classList.remove('hidden');
        } else {
            subjectsWrapper.classList.add('hidden');
        }

        // Learning Methods
        const learningMethodsContainer = document.getElementById('modalLearningMethods');
        const learningMethodsWrapper = document.getElementById('modalLearningMethodsContainer');
        if (item.learning_methods && item.learning_methods.length > 0) {
            learningMethodsContainer.innerHTML = item.learning_methods.map(method => 
                `<li>${method}</li>`
            ).join('');
            learningMethodsWrapper.classList.remove('hidden');
        } else {
            learningMethodsWrapper.classList.add('hidden');
        }

        // Assessment Methods
        const assessmentMethodsContainer = document.getElementById('modalAssessmentMethods');
        const assessmentMethodsWrapper = document.getElementById('modalAssessmentMethodsContainer');
        if (item.assessment_methods && item.assessment_methods.length > 0) {
            assessmentMethodsContainer.innerHTML = item.assessment_methods.map(method => 
                `<li>${method}</li>`
            ).join('');
            assessmentMethodsWrapper.classList.remove('hidden');
        } else {
            assessmentMethodsWrapper.classList.add('hidden');
        }

        // Show modal
        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        // Restore body scroll
        document.body.style.overflow = 'auto';
    }

    // Show/hide states
    function showLoading() {
        document.getElementById('loading').classList.remove('hidden');
        document.getElementById('academicContent').classList.add('hidden');
    }

    function showContent() {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('academicContent').classList.remove('hidden');
    }

    function showError() {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('error').classList.remove('hidden');
        document.getElementById('academicContent').classList.add('hidden');
    }

    function hideError() {
        document.getElementById('error').classList.add('hidden');
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
