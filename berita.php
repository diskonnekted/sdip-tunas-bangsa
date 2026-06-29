<?php
// Include necessary files
include_once 'includes/settings.php';

// Get school info
$school_info = getSchoolInfo();
$contact_info = getContactInfo();

// Set page title
$page_title = 'Portal Berita';
$body_class = 'bg-gray-50';

include 'includes/header.php';
?>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-green-600 via-purple-600 to-green-800 pt-32 pb-20 overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute inset-0">
            <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
            <div class="absolute top-40 right-20 w-32 h-32 bg-purple-300/20 rounded-full blur-2xl"></div>
            <div class="absolute bottom-20 left-1/3 w-24 h-24 bg-green-300/20 rounded-full blur-xl"></div>
        </div>
        
        <div class="relative container text-center text-white z-10">
            <div class="mb-6">
                <span class="inline-flex items-center bg-white/20 backdrop-blur px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <i class="fas fa-newspaper mr-2"></i>
                    Portal Berita Sekolah
                </span>
            </div>
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                Berita <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-orange-300">Terkini</span>
            </h1>
            <p class="text-xl md:text-2xl opacity-90 mb-10 max-w-3xl mx-auto">
                Ikuti perkembangan terbaru dan berbagai pencapaian membanggakan dari <?php echo htmlspecialchars($school_info['name']); ?>
            </p>
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all">
                    <div class="text-3xl font-bold" id="totalNewsCount">-</div>
                    <div class="text-sm opacity-90 mt-1">Total Berita</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all">
                    <div class="text-3xl font-bold" id="prestasiCount">-</div>
                    <div class="text-sm opacity-90 mt-1">Prestasi</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all">
                    <div class="text-3xl font-bold" id="kegiatanCount">-</div>
                    <div class="text-sm opacity-90 mt-1">Kegiatan</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all">
                    <div class="text-3xl font-bold" id="pengumumanCount">-</div>
                    <div class="text-sm opacity-90 mt-1">Pengumuman</div>
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

    <!-- Search and Filter Section -->
    <section class="py-12 bg-gray-50">
        <div class="container">
            <div class="max-w-6xl mx-auto">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Cari Berita</h2>
                        <p class="text-gray-600">Temukan berita yang Anda cari dengan mudah</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                        <div class="md:col-span-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-search mr-2 text-green-500"></i>Kata Kunci
                            </label>
                            <input type="text" id="searchInput" 
                                   placeholder="Masukkan kata kunci berita..." 
                                   class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all text-lg">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-tags mr-2 text-green-500"></i>Kategori
                            </label>
                            <select id="categoryFilter" class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all text-lg">
                                <option value="">Semua Kategori</option>
                                <option value="umum">Umum</option>
                                <option value="prestasi">Prestasi</option>
                                <option value="kegiatan">Kegiatan</option>
                                <option value="pengumuman">Pengumuman</option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <button onclick="filterNews()" 
                                    class="w-full px-8 py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                                <i class="fas fa-search mr-2"></i>Cari Berita
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News Content -->
    <section class="py-16">
        <div class="container">
            <!-- Loading State -->
            <div id="loading" class="text-center py-20">
                <div class="relative inline-block">
                    <div class="animate-spin rounded-full h-20 w-20 border-t-4 border-green-500 border-opacity-25"></div>
                    <div class="animate-ping absolute top-0 left-0 h-20 w-20 rounded-full bg-green-400 opacity-20"></div>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-900">Memuat Berita</h3>
                <p class="mt-2 text-gray-600">Sedang mengambil berita terbaru untuk Anda...</p>
            </div>

            <!-- Error State -->
            <div id="error" class="text-center py-20 hidden">
                <div class="max-w-md mx-auto">
                    <div class="bg-red-100 rounded-full p-8 w-32 h-32 mx-auto mb-8">
                        <i class="fas fa-exclamation-triangle text-5xl text-red-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Oops! Terjadi Kesalahan</h3>
                    <p class="text-gray-600 mb-8">Gagal memuat berita. Periksa koneksi internet Anda dan coba lagi.</p>
                    <button onclick="loadNews()" 
                            class="px-8 py-4 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-bold rounded-2xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-redo mr-2"></i>Coba Lagi
                    </button>
                </div>
            </div>

            <!-- Featured News -->
            <div id="featuredNews" class="mb-20 hidden">
                <div class="text-center mb-12">
                    <div class="inline-flex items-center bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-8 py-3 rounded-full text-sm font-bold mb-6">
                        <i class="fas fa-star mr-3 text-lg"></i>BERITA UNGGULAN
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Sorotan Utama</h2>
                    <p class="text-gray-600 text-xl max-w-2xl mx-auto">Berita-berita terpenting dan terbaru yang wajib Anda ketahui</p>
                </div>
                <div id="featuredContainer" class="max-w-7xl mx-auto"></div>
            </div>

            <!-- News List -->
            <div id="newsList" class="hidden">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-12">
                    <div>
                        <h2 class="text-4xl font-bold text-gray-900 mb-3">Semua Berita</h2>
                        <div id="resultsInfo" class="text-gray-600 text-lg"></div>
                    </div>
                    <div class="mt-6 lg:mt-0 flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-700">Tampilan:</span>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-1">
                            <button id="gridViewBtn" onclick="setViewMode('grid')" class="p-3 rounded-lg bg-green-100 text-green-600 transition-all" title="Grid View">
                                <i class="fas fa-th-large text-lg"></i>
                            </button>
                            <button id="listViewBtn" onclick="setViewMode('list')" class="p-3 rounded-lg text-gray-400 hover:text-gray-600 transition-all" title="List View">
                                <i class="fas fa-list text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="newsContainer"></div>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="mt-20 flex justify-center hidden">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-2"></div>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="text-center py-20 hidden">
                <div class="max-w-md mx-auto">
                    <div class="bg-gray-100 rounded-full p-12 w-48 h-48 mx-auto mb-8 flex items-center justify-center">
                        <i class="fas fa-newspaper text-6xl text-gray-400"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">Tidak Ada Berita</h3>
                    <p class="text-gray-600 text-lg mb-8">Maaf, tidak ada berita yang sesuai dengan pencarian Anda.</p>
                    <button onclick="clearFilters()" 
                            class="px-8 py-4 bg-gradient-to-r from-green-500 to-purple-600 text-white font-bold rounded-2xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-refresh mr-2"></i>Reset Pencarian
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- News Detail Modal -->
    <div id="newsModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 id="modalTitle" class="text-2xl font-bold text-gray-900"></h3>
                <button onclick="closeModal()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <i class="fas fa-times text-2xl text-gray-400"></i>
                </button>
            </div>
            <div class="overflow-y-auto max-h-[70vh]">
                <div id="modalContent" class="p-6"></div>
                <div id="modalMeta" class="px-6 pb-6 pt-4 border-t border-gray-100 text-sm text-gray-500 bg-gray-50"></div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
    <script>
    // News API Integration
    let currentPage = 1;
    let currentCategory = '';
    let currentSearch = '';
    let currentViewMode = 'grid';
    const itemsPerPage = 9;

    // API Base URL
    const API_URL = 'api/news.php';

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        loadNews();
        loadFeaturedNews();
        loadStats();
        
        // Setup search with debounce
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = this.value;
                currentPage = 1;
                loadNews();
            }, 500);
        });
    });

    // Load statistics
    async function loadStats() {
        try {
            const response = await fetch(`${API_URL}?action=stats`);
            const data = await response.json();
            
            if (data.success) {
                const stats = data.data;
                document.getElementById('totalNewsCount').textContent = stats.total || 0;
                document.getElementById('prestasiCount').textContent = stats.prestasi || 0;
                document.getElementById('kegiatanCount').textContent = stats.kegiatan || 0;
                document.getElementById('pengumumanCount').textContent = stats.pengumuman || 0;
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    // Load featured news
    async function loadFeaturedNews() {
        try {
            const response = await fetch(`${API_URL}?action=featured&limit=2`);
            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                displayFeaturedNews(data.data);
                document.getElementById('featuredNews').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading featured news:', error);
        }
    }

    // Display featured news
    function displayFeaturedNews(news) {
        const container = document.getElementById('featuredContainer');
        
        container.innerHTML = news.map(item => `
            <div class="group cursor-pointer" onclick="openNewsModal('${item.slug}')">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                    <div class="relative h-80 overflow-hidden">
                        ${item.featured_image ? 
                            `<img src="${item.featured_image}" alt="${item.title}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">` : 
                            `<div class="w-full h-full bg-gradient-to-br from-green-500 to-purple-600 flex items-center justify-center">
                                <i class="fas fa-newspaper text-white text-6xl opacity-80"></i>
                            </div>`
                        }
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute top-4 left-4">
                            <span class="bg-yellow-400 text-yellow-900 px-4 py-2 rounded-full text-sm font-bold">
                                <i class="fas fa-star mr-2"></i>UNGGULAN
                            </span>
                        </div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <span class="inline-flex items-center bg-white/20 backdrop-blur px-3 py-1 rounded-full text-sm font-medium mb-3 capitalize">
                                ${getCategoryIcon(item.category)} ${item.category}
                            </span>
                            <h3 class="text-xl font-bold leading-tight">${item.title}</h3>
                        </div>
                    </div>
                    <div class="p-8">
                        <p class="text-gray-600 leading-relaxed mb-6">${item.excerpt}</p>
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span class="flex items-center">
                                <i class="fas fa-calendar mr-2"></i>${item.formatted_date}
                            </span>
                            <span class="flex items-center">
                                <i class="fas fa-eye mr-2"></i>${item.views?.toLocaleString() || 0} views
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
        
        // Adjust grid based on news count
        if (news.length === 1) {
            container.className = 'max-w-2xl mx-auto';
        } else {
            container.className = 'grid grid-cols-1 lg:grid-cols-2 gap-8';
        }
    }

    // Load regular news
    async function loadNews() {
        showLoading();
        hideError();
        
        try {
            let url = `${API_URL}?action=list&page=${currentPage}&limit=${itemsPerPage}`;
            
            if (currentCategory) {
                url += `&category=${currentCategory}`;
            }
            if (currentSearch) {
                url += `&search=${encodeURIComponent(currentSearch)}`;
            }
            
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success) {
                displayNews(data.data);
                displayPagination(data.pagination);
                updateResultsInfo(data.pagination);
                showContent();
            } else {
                throw new Error(data.message || 'Failed to load news');
            }
        } catch (error) {
            console.error('Error loading news:', error);
            showError();
        }
    }

    // Display news list
    function displayNews(news) {
        const container = document.getElementById('newsContainer');
        const newsList = document.getElementById('newsList');
        const emptyState = document.getElementById('emptyState');
        
        if (news.length === 0) {
            newsList.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }
        
        // Set container class based on view mode
        if (currentViewMode === 'grid') {
            container.className = 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8';
        } else {
            container.className = 'space-y-6';
        }
        
        container.innerHTML = news.map(item => 
            currentViewMode === 'grid' ? renderNewsCard(item) : renderNewsListItem(item)
        ).join('');
        
        newsList.classList.remove('hidden');
        emptyState.classList.add('hidden');
    }

    // Render news card (grid view)
    function renderNewsCard(item) {
        return `
            <article class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1 cursor-pointer overflow-hidden"
                     onclick="openNewsModal('${item.slug}')">
                <div class="relative h-56 overflow-hidden">
                    ${item.featured_image ? 
                        `<img src="${item.featured_image}" alt="${item.title}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">` : 
                        `<div class="w-full h-full bg-gradient-to-br ${getCategoryGradient(item.category)} flex items-center justify-center">
                            <i class="fas fa-newspaper text-white text-4xl opacity-80"></i>
                        </div>`
                    }
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                    <div class="absolute top-3 left-3">
                        <span class="inline-flex items-center ${getCategoryColor(item.category)} px-3 py-1 rounded-full text-xs font-semibold">
                            ${getCategoryIcon(item.category)} ${item.category}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-3 group-hover:text-green-600 transition-colors line-clamp-2">
                        ${item.title}
                    </h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">${item.excerpt}</p>
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-calendar mr-1"></i>${item.formatted_date}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-eye mr-1"></i>${item.views?.toLocaleString() || 0}
                        </span>
                    </div>
                </div>
            </article>
        `;
    }

    // Render news list item (list view)
    function renderNewsListItem(item) {
        return `
            <article class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer overflow-hidden"
                     onclick="openNewsModal('${item.slug}')">
                <div class="md:flex">
                    <div class="md:w-80 h-48 md:h-auto overflow-hidden">
                        ${item.featured_image ? 
                            `<img src="${item.featured_image}" alt="${item.title}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">` : 
                            `<div class="w-full h-full bg-gradient-to-br ${getCategoryGradient(item.category)} flex items-center justify-center">
                                <i class="fas fa-newspaper text-white text-5xl opacity-80"></i>
                            </div>`
                        }
                    </div>
                    <div class="flex-1 p-8">
                        <div class="flex items-center mb-4">
                            <span class="inline-flex items-center ${getCategoryColor(item.category)} px-3 py-1 rounded-full text-sm font-semibold">
                                ${getCategoryIcon(item.category)} ${item.category}
                            </span>
                            <span class="ml-4 text-sm text-gray-500">${item.formatted_date}</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4 hover:text-green-600 transition-colors">
                            ${item.title}
                        </h3>
                        <p class="text-gray-600 leading-relaxed mb-6">${item.excerpt}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-green-600 font-semibold hover:text-green-700 transition-colors">
                                Baca Selengkapnya <i class="fas fa-arrow-right ml-1"></i>
                            </span>
                            <span class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-eye mr-1"></i>${item.views?.toLocaleString() || 0} views
                            </span>
                        </div>
                    </div>
                </div>
            </article>
        `;
    }

    // Helper functions for categories
    function getCategoryIcon(category) {
        const icons = {
            'umum': '<i class="fas fa-newspaper"></i>',
            'prestasi': '<i class="fas fa-trophy"></i>',
            'kegiatan': '<i class="fas fa-bullseye"></i>',
            'pengumuman': '<i class="fas fa-bullhorn"></i>'
        };
        return icons[category] || '<i class="fas fa-newspaper"></i>';
    }

    function getCategoryColor(category) {
        const colors = {
            'umum': 'bg-gray-100 text-gray-800',
            'prestasi': 'bg-green-100 text-green-800',
            'kegiatan': 'bg-green-100 text-green-800',
            'pengumuman': 'bg-red-100 text-red-800'
        };
        return colors[category] || 'bg-gray-100 text-gray-800';
    }

    function getCategoryGradient(category) {
        const gradients = {
            'umum': 'from-gray-500 to-gray-700',
            'prestasi': 'from-green-500 to-emerald-600',
            'kegiatan': 'from-green-500 to-green-600',
            'pengumuman': 'from-red-500 to-pink-600'
        };
        return gradients[category] || 'from-gray-500 to-gray-700';
    }

    // Set view mode
    function setViewMode(mode) {
        currentViewMode = mode;
        
        const gridBtn = document.getElementById('gridViewBtn');
        const listBtn = document.getElementById('listViewBtn');
        
        if (mode === 'grid') {
            gridBtn.className = 'p-3 rounded-lg bg-green-100 text-green-600 transition-all';
            listBtn.className = 'p-3 rounded-lg text-gray-400 hover:text-gray-600 transition-all';
        } else {
            gridBtn.className = 'p-3 rounded-lg text-gray-400 hover:text-gray-600 transition-all';
            listBtn.className = 'p-3 rounded-lg bg-green-100 text-green-600 transition-all';
        }
        
        // Re-render current news with new view mode
        const newsContainer = document.getElementById('newsContainer');
        if (newsContainer.children.length > 0) {
            loadNews();
        }
    }

    // Display pagination
    function displayPagination(pagination) {
        const container = document.getElementById('pagination');
        
        if (pagination.total_pages <= 1) {
            container.classList.add('hidden');
            return;
        }
        
        let paginationHTML = '';
        
        // Previous button
        if (pagination.current_page > 1) {
            paginationHTML += `<button onclick="changePage(${pagination.current_page - 1})" 
                               class="px-4 py-3 text-gray-600 hover:text-green-600 hover:bg-green-50 transition-all rounded-l-xl">
                               <i class="fas fa-chevron-left"></i>
                               </button>`;
        }
        
        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === pagination.current_page;
            paginationHTML += `<button onclick="changePage(${i})" 
                               class="px-4 py-3 ${isActive ? 
                                   'bg-green-600 text-white' : 
                                   'text-gray-600 hover:text-green-600 hover:bg-green-50'} 
                               transition-all font-semibold">${i}</button>`;
        }
        
        // Next button
        if (pagination.current_page < pagination.total_pages) {
            paginationHTML += `<button onclick="changePage(${pagination.current_page + 1})" 
                               class="px-4 py-3 text-gray-600 hover:text-green-600 hover:bg-green-50 transition-all rounded-r-xl">
                               <i class="fas fa-chevron-right"></i>
                               </button>`;
        }
        
        container.querySelector('div').innerHTML = paginationHTML;
        container.classList.remove('hidden');
    }

    // Update results info
    function updateResultsInfo(pagination) {
        const info = document.getElementById('resultsInfo');
        const start = (pagination.current_page - 1) * pagination.per_page + 1;
        const end = Math.min(pagination.current_page * pagination.per_page, pagination.total_records);
        info.textContent = `Menampilkan ${start}-${end} dari ${pagination.total_records} berita`;
    }

    // Change page
    function changePage(page) {
        currentPage = page;
        loadNews();
        document.getElementById('newsList').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Filter news
    function filterNews() {
        currentCategory = document.getElementById('categoryFilter').value;
        currentPage = 1;
        loadNews();
    }

    // Clear filters
    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('categoryFilter').value = '';
        currentSearch = '';
        currentCategory = '';
        currentPage = 1;
        loadNews();
    }

    // Open news modal
    async function openNewsModal(slug) {
        try {
            const response = await fetch(`${API_URL}?action=detail&slug=${slug}`);
            const data = await response.json();
            
            if (data.success) {
                displayNewsModal(data.data);
            } else {
                alert('Gagal memuat detail berita');
            }
        } catch (error) {
            console.error('Error loading news detail:', error);
            alert('Terjadi kesalahan saat memuat detail berita');
        }
    }

    // Display news modal
    function displayNewsModal(news) {
        const modal = document.getElementById('newsModal');
        const title = document.getElementById('modalTitle');
        const content = document.getElementById('modalContent');
        const meta = document.getElementById('modalMeta');
        
        title.textContent = news.title;
        
        content.innerHTML = `
            ${news.featured_image ? 
                `<img src="${news.featured_image}" alt="${news.title}" class="w-full h-80 object-cover rounded-2xl mb-6">` : 
                ''
            }
            <div class="flex items-center mb-6 space-x-4">
                <span class="inline-flex items-center ${getCategoryColor(news.category)} px-4 py-2 rounded-full font-semibold">
                    ${getCategoryIcon(news.category)} ${news.category}
                </span>
                <span class="text-gray-500">${news.formatted_date}</span>
                <span class="text-gray-500">${news.views?.toLocaleString() || 0} views</span>
            </div>
            <div class="prose prose-lg max-w-none">${news.content}</div>
        `;
        
        meta.innerHTML = `
            <div class="flex items-center justify-between">
                <span>Diterbitkan oleh <strong>${news.author_name || 'Admin'}</strong></span>
                <div class="flex items-center space-x-4">
                    <button onclick="shareNews('${news.title}', '${window.location.origin}/berita.php')" 
                            class="text-green-600 hover:text-green-700">
                        <i class="fas fa-share mr-1"></i>Bagikan
                    </button>
                </div>
            </div>
        `;
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Close modal
    function closeModal() {
        document.getElementById('newsModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Share news
    function shareNews(title, url) {
        if (navigator.share) {
            navigator.share({
                title: title,
                url: url
            });
        } else {
            navigator.clipboard.writeText(url).then(() => {
                alert('Link berhasil disalin!');
            });
        }
    }

    // Show/hide states
    function showLoading() {
        document.getElementById('loading').classList.remove('hidden');
        document.getElementById('newsList').classList.add('hidden');
        document.getElementById('pagination').classList.add('hidden');
        document.getElementById('emptyState').classList.add('hidden');
    }

    function showContent() {
        document.getElementById('loading').classList.add('hidden');
    }

    function showError() {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('error').classList.remove('hidden');
        document.getElementById('newsList').classList.add('hidden');
        document.getElementById('pagination').classList.add('hidden');
        document.getElementById('emptyState').classList.add('hidden');
    }

    function hideError() {
        document.getElementById('error').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('newsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    </script>

    <!-- Custom styles for better appearance -->
    <style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .prose h1, .prose h2, .prose h3, .prose h4 {
        color: #1f2937;
        font-weight: 700;
    }
    
    .prose p {
        color: #4b5563;
        line-height: 1.7;
    }
    
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
