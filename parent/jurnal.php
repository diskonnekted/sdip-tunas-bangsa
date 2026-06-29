<?php
require_once 'includes/header.php';
require_once '../admin/config/database.php';
require_once '../admin/models/ParentModel.php';
require_once '../admin/models/Student.php';
require_once '../admin/models/Jurnal7Kaih.php';

if (!isset($_GET['siswa_id'])) {
    header('Location: index.php');
    exit;
}

$siswa_id = $_GET['siswa_id'];
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

$db = new Database();
$conn = $db->getConnection();
$parentModel = new ParentModel($conn);
$studentModel = new Student($conn);
$jurnalModel = new Jurnal7Kaih($conn);

$parentData = $parentModel->getByUserId($_SESSION['parent_user_id']);
if (!$parentData) {
    header('Location: login.php');
    exit;
}

// Verify this child belongs to this parent
$children = $parentModel->getChildren($parentData['id']);
$is_my_child = false;
$child_name = "";
foreach ($children as $c) {
    if ($c['id'] == $siswa_id) {
        $is_my_child = true;
        $child_name = $c['nama_lengkap'];
        break;
    }
}

if (!$is_my_child) {
    echo "<div class='p-4 text-center text-red-600'>Akses ditolak.</div>";
    require_once 'includes/footer.php';
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jurnalData = [
        'siswa_id' => $siswa_id,
        'tanggal' => $tanggal,
        'is_bangun_pagi' => isset($_POST['is_bangun_pagi']) ? 1 : 0,
        'is_beribadah' => isset($_POST['is_beribadah']) ? 1 : 0,
        'is_berolahraga' => isset($_POST['is_berolahraga']) ? 1 : 0,
        'is_makan_sehat' => isset($_POST['is_makan_sehat']) ? 1 : 0,
        'is_gemar_belajar' => isset($_POST['is_gemar_belajar']) ? 1 : 0,
        'is_bermasyarakat' => isset($_POST['is_bermasyarakat']) ? 1 : 0,
        'is_tidur_cepat' => isset($_POST['is_tidur_cepat']) ? 1 : 0,
        'catatan' => $_POST['catatan'] ?? '',
        'created_by' => $parentData['id']
    ];
    
    // Handle optional photo upload
    if (isset($_FILES['foto_bukti']) && $_FILES['foto_bukti']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../upload/jurnal/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $ext = pathinfo($_FILES['foto_bukti']['name'], PATHINFO_EXTENSION);
        $filename = 'jurnal_' . $siswa_id . '_' . date('Ymd_His') . '.' . $ext;
        if (move_uploaded_file($_FILES['foto_bukti']['tmp_name'], $uploadDir . $filename)) {
            $jurnalData['foto_bukti'] = $filename;
        }
    }
    
    $result = $jurnalModel->simpanJurnal($jurnalData);
    if ($result['success']) {
        $message = '<div class="bg-green-50 text-green-700 p-4 rounded-xl text-sm mb-6 flex items-center border border-green-100 shadow-sm"><i class="fas fa-check-circle mr-3 text-lg"></i> Jurnal berhasil disimpan!</div>';
    } else {
        $message = '<div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm mb-6 flex items-center border border-red-100"><i class="fas fa-exclamation-circle mr-3 text-lg"></i> Gagal menyimpan jurnal.</div>';
    }
}

// Get existing data
$jurnal = $jurnalModel->getByTanggal($siswa_id, $tanggal);
?>
<style>
/* Toggle Switch CSS */
.toggle-checkbox:checked {
  right: 0;
  border-color: #dc2626;
}
.toggle-checkbox:checked + .toggle-label {
  background-color: #dc2626;
}
</style>

<div class="flex items-center mb-6">
    <a href="index.php" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-600 shadow-sm mr-3 active:scale-95 transition-transform">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h2 class="text-xl font-bold text-gray-900 leading-tight">Jurnal 7KAIH</h2>
        <p class="text-xs text-gray-500 truncate w-48"><?= htmlspecialchars($child_name) ?></p>
    </div>
</div>

<?= $message ?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <div class="font-bold text-gray-800">
            <i class="far fa-calendar-alt text-red-600 mr-2"></i> <?= date('d M Y', strtotime($tanggal)) ?>
        </div>
        <input type="date" value="<?= $tanggal ?>" onchange="window.location.href='jurnal.php?siswa_id=<?= $siswa_id ?>&tanggal='+this.value" class="text-xs border-gray-300 rounded focus:ring-red-500 focus:border-red-500 p-1">
    </div>
    
    <form action="" method="POST" enctype="multipart/form-data" class="p-5">
        <div class="space-y-5">
            
            <?php
            $kebiasaan = [
                ['name' => 'is_bangun_pagi', 'label' => 'Bangun Pagi & Merapikan Tempat Tidur', 'icon' => 'fa-sun', 'color' => 'text-yellow-500'],
                ['name' => 'is_beribadah', 'label' => 'Beribadah Tepat Waktu', 'icon' => 'fa-praying-hands', 'color' => 'text-blue-500'],
                ['name' => 'is_berolahraga', 'label' => 'Berolahraga / Aktivitas Fisik', 'icon' => 'fa-running', 'color' => 'text-orange-500'],
                ['name' => 'is_makan_sehat', 'label' => 'Makan Makanan Sehat & Bergizi', 'icon' => 'fa-apple-alt', 'color' => 'text-green-500'],
                ['name' => 'is_gemar_belajar', 'label' => 'Gemar Belajar / Membaca Buku', 'icon' => 'fa-book-reader', 'color' => 'text-purple-500'],
                ['name' => 'is_bermasyarakat', 'label' => 'Berbuat Baik / Membantu Orang Tua', 'icon' => 'fa-hands-helping', 'color' => 'text-teal-500'],
                ['name' => 'is_tidur_cepat', 'label' => 'Tidur Tepat Waktu (Maks 21.30)', 'icon' => 'fa-moon', 'color' => 'text-indigo-500'],
            ];
            
            foreach ($kebiasaan as $k):
                $isChecked = ($jurnal && $jurnal[$k['name']] == 1) ? 'checked' : '';
            ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center mr-3 shrink-0">
                        <i class="fas <?= $k['icon'] ?> <?= $k['color'] ?> text-lg"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700 leading-tight"><?= $k['label'] ?></span>
                </div>
                <div class="relative inline-block w-12 align-middle select-none transition duration-200 ease-in ml-2 shrink-0">
                    <input type="checkbox" name="<?= $k['name'] ?>" id="<?= $k['name'] ?>" <?= $isChecked ?> class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 border-gray-300 appearance-none cursor-pointer transition-all duration-300 z-10 top-0 left-0"/>
                    <label for="<?= $k['name'] ?>" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300"></label>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div class="mt-6 border-t border-gray-100 pt-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Foto Kegiatan (Opsional)</label>
                <input type="file" name="foto_bukti" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                <?php if($jurnal && !empty($jurnal['foto_bukti'])): ?>
                    <p class="text-xs text-green-600 mt-2"><i class="fas fa-check"></i> Foto sudah diunggah</p>
                <?php endif; ?>
            </div>
            
            <div class="mt-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Tambahan (Opsional)</label>
                <textarea name="catatan" rows="2" class="w-full border-gray-300 bg-gray-50 rounded-xl focus:ring-red-500 focus:border-red-500 p-3 text-sm" placeholder="Contoh: Anak sakit demam hari ini..."><?= htmlspecialchars($jurnal['catatan_orang_tua'] ?? '') ?></textarea>
            </div>
            
        </div>
        
        <button type="submit" class="mt-6 w-full py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-200 transition-all active:scale-95 flex justify-center items-center">
            <i class="fas fa-save mr-2"></i> Simpan Jurnal 7KAIH
        </button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
