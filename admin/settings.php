<?php
$page_title = 'Pengaturan Sekolah';
require_once 'includes/functions.php';
require_once 'models/Settings.php';

// Check if user is logged in
requireLogin();

// Initialize database
$database = new Database();
$db = $database->getConnection();
$settings = new Settings($db);

$errors = [];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['csrf_token']) && validateCSRFToken($_POST['csrf_token'])) {
        
        // Get current settings for file handling
        $currentSettings = $settings->getSettings();
        
        $data = [
            'school_name' => sanitizeInput($_POST['school_name'] ?? ''),
            'school_motto' => sanitizeInput($_POST['school_motto'] ?? ''),
            'school_description' => $_POST['school_description'] ?? '',
            'school_address' => sanitizeInput($_POST['school_address'] ?? ''),
            'school_phone' => sanitizeInput($_POST['school_phone'] ?? ''),
            'school_email' => sanitizeInput($_POST['school_email'] ?? ''),
            'school_website' => sanitizeInput($_POST['school_website'] ?? ''),
            'school_latitude' => sanitizeInput($_POST['school_latitude'] ?? ''),
            'school_longitude' => sanitizeInput($_POST['school_longitude'] ?? ''),
            'principal_name' => sanitizeInput($_POST['principal_name'] ?? ''),
            'established_year' => sanitizeInput($_POST['established_year'] ?? ''),
            'npsn' => sanitizeInput($_POST['npsn'] ?? ''),
            'accreditation' => sanitizeInput($_POST['accreditation'] ?? ''),
            'facebook_url' => sanitizeInput($_POST['facebook_url'] ?? ''),
            'instagram_url' => sanitizeInput($_POST['instagram_url'] ?? ''),
            'youtube_url' => sanitizeInput($_POST['youtube_url'] ?? ''),
            'twitter_url' => sanitizeInput($_POST['twitter_url'] ?? '')
        ];
        
        // Handle school logo upload
        if (isset($_FILES['school_logo']) && $_FILES['school_logo']['error'] === UPLOAD_ERR_OK) {
            $logo_filename = uploadFile($_FILES['school_logo'], 'uploads/', ['jpg', 'jpeg', 'png', 'gif']);
            if ($logo_filename) {
                // Delete old logo if exists
                if ($currentSettings['school_logo'] && file_exists('uploads/' . $currentSettings['school_logo'])) {
                    unlink('uploads/' . $currentSettings['school_logo']);
                }
                $data['school_logo'] = $logo_filename;
            } else {
                $errors[] = 'Gagal upload logo sekolah';
            }
        } else {
            $data['school_logo'] = $currentSettings['school_logo'] ?? '';
        }
        
        // Handle principal photo upload
        if (isset($_FILES['principal_photo']) && $_FILES['principal_photo']['error'] === UPLOAD_ERR_OK) {
            $photo_filename = uploadFile($_FILES['principal_photo'], 'uploads/', ['jpg', 'jpeg', 'png', 'gif']);
            if ($photo_filename) {
                // Delete old photo if exists
                if ($currentSettings['principal_photo'] && file_exists('uploads/' . $currentSettings['principal_photo'])) {
                    unlink('uploads/' . $currentSettings['principal_photo']);
                }
                $data['principal_photo'] = $photo_filename;
            } else {
                $errors[] = 'Gagal upload foto kepala sekolah';
            }
        } else {
            $data['principal_photo'] = $currentSettings['principal_photo'] ?? '';
        }
        
        // Validate data
        $validation_errors = $settings->validate($data);
        $errors = array_merge($errors, $validation_errors);
        
        if (empty($errors)) {
            if ($settings->saveSettings($data)) {
                setAlert('Pengaturan sekolah berhasil disimpan!', 'success');
                header('Location: settings.php');
                exit;
            } else {
                $errors[] = 'Gagal menyimpan pengaturan sekolah';
            }
        }
    } else {
        $errors[] = 'Token CSRF tidak valid';
    }
}

// Get current settings for form
$currentSettings = $settings->getSettings();

require_once 'includes/admin_header.php';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Pengaturan Sekolah</h2>
                <p class="text-gray-600 mt-1">Kelola informasi dasar sekolah, kontak, dan media sosial</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-clock mr-1"></i>
                    Terakhir diperbarui: <?= $currentSettings ? formatTanggal($currentSettings['updated_at']) : 'Belum pernah' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-school text-primary-600 mr-2"></i>Informasi Dasar
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sekolah *</label>
                            <input type="text" name="school_name" required
                                   value="<?= htmlspecialchars($currentSettings['school_name'] ?? 'SDIP Tunas Bangsa') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Motto Sekolah</label>
                            <input type="text" name="school_motto"
                                   value="<?= htmlspecialchars($currentSettings['school_motto'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Cerdas, Berkarakter, dan Berintegritas">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Berdiri</label>
                            <input type="number" name="established_year" min="1900" max="<?= date('Y') ?>"
                                   value="<?= htmlspecialchars($currentSettings['established_year'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NPSN</label>
                            <input type="text" name="npsn"
                                   value="<?= htmlspecialchars($currentSettings['npsn'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Nomor Pokok Sekolah Nasional">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Akreditasi</label>
                            <select name="accreditation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Akreditasi</option>
                                <option value="A" <?= ($currentSettings['accreditation'] ?? '') === 'A' ? 'selected' : '' ?>>A (Unggul)</option>
                                <option value="B" <?= ($currentSettings['accreditation'] ?? '') === 'B' ? 'selected' : '' ?>>B (Baik)</option>
                                <option value="C" <?= ($currentSettings['accreditation'] ?? '') === 'C' ? 'selected' : '' ?>>C (Cukup)</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Sekolah</label>
                            <textarea name="school_description" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="Deskripsi singkat tentang sekolah..."><?= htmlspecialchars($currentSettings['school_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-address-card text-green-600 mr-2"></i>Informasi Kontak
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Sekolah</label>
                            <textarea name="school_address" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="Alamat lengkap sekolah..."><?= htmlspecialchars($currentSettings['school_address'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                                <input type="tel" name="school_phone"
                                       value="<?= htmlspecialchars($currentSettings['school_phone'] ?? '') ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="(021) 1234-5678">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="school_email"
                                       value="<?= htmlspecialchars($currentSettings['school_email'] ?? '') ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="info@sekolah.sch.id">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                            <input type="url" name="school_website"
                                   value="<?= htmlspecialchars($currentSettings['school_website'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="https://sekolah.sch.id">
                        </div>
                    </div>
                </div>
                
                <!-- Location Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>Lokasi & Peta
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                            <input type="number" step="any" name="school_latitude" id="latitude"
                                   value="<?= htmlspecialchars($currentSettings['school_latitude'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="-6.2088">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                            <input type="number" step="any" name="school_longitude" id="longitude"
                                   value="<?= htmlspecialchars($currentSettings['school_longitude'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="106.8456">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <button type="button" onclick="getCurrentLocation()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-location-arrow mr-2"></i>Gunakan Lokasi Saat Ini
                        </button>
                        <button type="button" onclick="showMap()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 ml-2">
                            <i class="fas fa-map mr-2"></i>Preview Peta
                        </button>
                    </div>
                    
                    <!-- Map Preview -->
                    <div id="mapContainer" class="hidden">
                        <div id="map" style="height: 300px; border-radius: 8px;"></div>
                        <p class="text-xs text-gray-500 mt-2">Klik pada peta untuk memperbarui koordinat</p>
                    </div>
                </div>
                
                <!-- Social Media -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-share-alt text-green-600 mr-2"></i>Media Sosial
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fab fa-facebook text-green-600 mr-2"></i>Facebook
                            </label>
                            <input type="url" name="facebook_url"
                                   value="<?= htmlspecialchars($currentSettings['facebook_url'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="https://facebook.com/sekolah">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fab fa-instagram text-pink-600 mr-2"></i>Instagram
                            </label>
                            <input type="url" name="instagram_url"
                                   value="<?= htmlspecialchars($currentSettings['instagram_url'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="https://instagram.com/sekolah">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fab fa-youtube text-red-600 mr-2"></i>YouTube
                            </label>
                            <input type="url" name="youtube_url"
                                   value="<?= htmlspecialchars($currentSettings['youtube_url'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="https://youtube.com/@sekolah">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fab fa-twitter text-green-400 mr-2"></i>Twitter
                            </label>
                            <input type="url" name="twitter_url"
                                   value="<?= htmlspecialchars($currentSettings['twitter_url'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="https://twitter.com/sekolah">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Upload Images -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-images text-purple-600 mr-2"></i>Gambar
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- School Logo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Logo Sekolah</label>
                            
                            <?php if (!empty($currentSettings['school_logo'])): ?>
                            <div class="mb-3">
                                <img src="uploads/<?= htmlspecialchars($currentSettings['school_logo']) ?>" 
                                     alt="Logo Sekolah" class="w-24 h-24 object-contain rounded-lg border">
                            </div>
                            <?php endif; ?>
                            
                            <input type="file" name="school_logo" accept="image/*"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF (max 2MB)</p>
                        </div>
                        
                        <!-- Principal Photo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Kepala Sekolah</label>
                            
                            <?php if (!empty($currentSettings['principal_photo'])): ?>
                            <div class="mb-3">
                                <img src="uploads/<?= htmlspecialchars($currentSettings['principal_photo']) ?>" 
                                     alt="Kepala Sekolah" class="w-24 h-24 object-cover rounded-lg border">
                            </div>
                            <?php endif; ?>
                            
                            <input type="file" name="principal_photo" accept="image/*"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF (max 2MB)</p>
                            
                            <div class="mt-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kepala Sekolah</label>
                                <input type="text" name="principal_name"
                                       value="<?= htmlspecialchars($currentSettings['principal_name'] ?? '') ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Dra. Nama Kepala Sekolah, M.Pd">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                        </button>
                        
                        <a href="index.php" class="block w-full px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg text-center transition-colors">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Preview Frontend</h4>
                        <div class="space-y-2">
                            <a href="../index.php" target="_blank" class="block text-sm text-green-600 hover:text-green-700">
                                <i class="fas fa-external-link-alt mr-1"></i>Lihat Beranda
                            </a>
                            <a href="../kontak.php" target="_blank" class="block text-sm text-green-600 hover:text-green-700">
                                <i class="fas fa-external-link-alt mr-1"></i>Lihat Halaman Kontak
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map;

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            alert('Koordinat berhasil diperbarui berdasarkan lokasi saat ini!');
        }, function(error) {
            alert('Gagal mendapatkan lokasi: ' + error.message);
        });
    } else {
        alert('Browser tidak mendukung geolocation');
    }
}

function showMap() {
    const lat = parseFloat(document.getElementById('latitude').value) || -6.2088;
    const lng = parseFloat(document.getElementById('longitude').value) || 106.8456;
    
    const mapContainer = document.getElementById('mapContainer');
    mapContainer.classList.remove('hidden');
    
    if (map) {
        map.remove();
    }
    
    map = L.map('map').setView([lat, lng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Â© OpenStreetMap contributors'
    }).addTo(map);
    
    const marker = L.marker([lat, lng], {draggable: true}).addTo(map);
    
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        document.getElementById('latitude').value = position.lat;
        document.getElementById('longitude').value = position.lng;
    });
    
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
    });
}
</script>

<?php 
// Display errors if any
if (!empty($errors)): ?>
<script>
<?php foreach ($errors as $error): ?>
showToast('<?= addslashes($error) ?>', 'error');
<?php endforeach; ?>
</script>
<?php endif; ?>

<?php require_once 'includes/admin_footer.php'; ?>
