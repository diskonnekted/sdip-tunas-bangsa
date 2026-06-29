<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'models/Student.php';
require_once 'models/Jurnal7Kaih.php';

Auth::requireRole([Auth::ROLE_ADMIN, Auth::ROLE_SUPERADMIN]);

if (!isset($_GET['id'])) {
    header('Location: students.php');
    exit;
}

$siswa_id = $_GET['id'];
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

$database = new Database();
$db = $database->getConnection();
$studentModel = new Student($db);
$jurnalModel = new Jurnal7Kaih($db);

$student = $studentModel->getById($siswa_id);
if (!$student) {
    Auth::setFlashMessage('error', 'Data siswa tidak ditemukan.');
    header('Location: students.php');
    exit;
}

$page_title = 'Jurnal 7KAIH - ' . htmlspecialchars($student['nama_lengkap']);
$jurnals = $jurnalModel->getJurnalSiswa($siswa_id, $bulan, $tahun);

// Helper for formatting icons
function renderIcon($status) {
    if ($status) {
        return '<i class="fas fa-check-circle text-green-500"></i>';
    }
    return '<i class="fas fa-times-circle text-gray-300"></i>';
}
?>
<?php include 'includes/admin_header.php'; ?>

<div class="mb-6">
    <div class="flex items-center space-x-4 mb-6">
        <a href="students.php" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
        <h2 class="text-2xl font-bold text-gray-900">Rekap Jurnal 7KAIH</h2>
    </div>

    <!-- Student Info Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6 flex items-center">
        <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center text-2xl mr-6">
            <i class="fas <?= $student['jenis_kelamin'] == 'L' ? 'fa-male' : 'fa-female' ?>"></i>
        </div>
        <div>
            <h3 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($student['nama_lengkap']) ?></h3>
            <p class="text-gray-500">NISN: <?= htmlspecialchars($student['nisn']) ?> | Kelas: <?= htmlspecialchars($student['kelas'] ?: '-') ?></p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <input type="hidden" name="id" value="<?= $siswa_id ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select name="bulan" class="w-40 px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                    <?php
                    $months = [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ];
                    foreach ($months as $m => $mName) {
                        $sel = ($m == $bulan) ? 'selected' : '';
                        echo "<option value='$m' $sel>$mName</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select name="tahun" class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                    <?php
                    $currentYear = date('Y');
                    for ($y = $currentYear; $y >= 2024; $y--) {
                        $sel = ($y == $tahun) ? 'selected' : '';
                        echo "<option value='$y' $sel>$y</option>";
                    }
                    ?>
                </select>
            </div>
            
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded-lg transition-colors">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
        </form>
    </div>

    <!-- Jurnal Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase" title="Bangun Pagi & Merapikan Tempat Tidur">Bangun Pagi</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase" title="Beribadah Tepat Waktu">Beribadah</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase" title="Berolahraga">Olahraga</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase" title="Makan Makanan Sehat">Makan Sehat</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase" title="Gemar Belajar / Membaca">Belajar</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase" title="Berbuat Baik / Membantu Orang Tua">Berbuat Baik</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase" title="Tidur Tepat Waktu">Tidur Cepat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Catatan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Foto Bukti</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($jurnals)): ?>
                    <tr>
                        <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                            Belum ada rekam jurnal untuk bulan ini.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($jurnals as $j): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?= date('d M Y', strtotime($j['tanggal'])) ?>
                            </td>
                            <td class="px-4 py-3 text-center text-lg"><?= renderIcon($j['is_bangun_pagi']) ?></td>
                            <td class="px-4 py-3 text-center text-lg"><?= renderIcon($j['is_beribadah']) ?></td>
                            <td class="px-4 py-3 text-center text-lg"><?= renderIcon($j['is_berolahraga']) ?></td>
                            <td class="px-4 py-3 text-center text-lg"><?= renderIcon($j['is_makan_sehat']) ?></td>
                            <td class="px-4 py-3 text-center text-lg"><?= renderIcon($j['is_gemar_belajar']) ?></td>
                            <td class="px-4 py-3 text-center text-lg"><?= renderIcon($j['is_bermasyarakat']) ?></td>
                            <td class="px-4 py-3 text-center text-lg"><?= renderIcon($j['is_tidur_cepat']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <?= $j['catatan_orang_tua'] ? htmlspecialchars($j['catatan_orang_tua']) : '-' ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php if ($j['foto_bukti']): ?>
                                <a href="../upload/jurnal/<?= htmlspecialchars($j['foto_bukti']) ?>" target="_blank" class="text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-image text-xl"></i>
                                </a>
                                <?php else: ?>
                                <span class="text-gray-300">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
