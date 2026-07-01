<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

// Require roles
Auth::requireRole([Auth::ROLE_SUPERADMIN, Auth::ROLE_ADMIN, Auth::ROLE_STAF, Auth::ROLE_GURU]);

$database = new Database();
$db = $database->getConnection();

$page_title = 'Daftar Kelas';

// Generate list of classes from 1A to 6F
$all_classes = [];
foreach (range(1, 6) as $grade) {
    foreach (range('A', 'F') as $section) {
        $all_classes[] = $grade . $section;
    }
}

// Selected Class
$selected_kelas = $_GET['kelas'] ?? '1A';
if (!in_array($selected_kelas, $all_classes)) {
    $selected_kelas = '1A';
}

// Fetch Homeroom Teacher (Wali Kelas)
$stmt_teacher = $db->prepare("
    SELECT u.full_name, tp.no_hp, tp.photo_filename 
    FROM admin_users u 
    JOIN teacher_profiles tp ON u.id = tp.user_id 
    WHERE tp.wali_kelas = ? AND u.is_active = 1
    LIMIT 1
");
$stmt_teacher->execute([$selected_kelas]);
$wali_kelas = $stmt_teacher->fetch(PDO::FETCH_ASSOC);

// Fetch Students
$stmt_students = $db->prepare("
    SELECT * FROM siswa 
    WHERE kelas = ? 
    ORDER BY nama_lengkap ASC
");
$stmt_students->execute([$selected_kelas]);
$students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include 'includes/admin_header.php'; ?>

<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Daftar Kelas</h2>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kelas</label>
                <select name="kelas" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-48">
                    <?php foreach($all_classes as $kls): ?>
                        <option value="<?= htmlspecialchars($kls) ?>" <?= $selected_kelas === $kls ? 'selected' : '' ?>>
                            Kelas <?= htmlspecialchars($kls) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <!-- Class Info Board -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6 border-l-4 border-blue-500">
        <div class="p-6 flex flex-col md:flex-row items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Kelas <?= htmlspecialchars($selected_kelas) ?></h3>
                <p class="text-gray-500 mt-1">Total Siswa: <?= count($students) ?> Anak</p>
            </div>
            
            <div class="mt-4 md:mt-0 flex items-center bg-blue-50 px-6 py-3 rounded-xl">
                <div class="flex-shrink-0 h-12 w-12 bg-white rounded-full flex items-center justify-center overflow-hidden border-2 border-blue-200">
                    <?php if (!empty($wali_kelas['photo_filename'])): ?>
                        <img src="uploads/teachers/<?= htmlspecialchars($wali_kelas['photo_filename']) ?>" class="h-full w-full object-cover">
                    <?php else: ?>
                        <i class="fas fa-user-tie text-blue-400 text-xl"></i>
                    <?php endif; ?>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-500">Wali Kelas</div>
                    <div class="font-bold text-gray-900 text-lg">
                        <?= $wali_kelas ? htmlspecialchars($wali_kelas['full_name']) : '<span class="text-red-500 font-normal italic">Belum Ditentukan</span>' ?>
                    </div>
                    <?php if ($wali_kelas && $wali_kelas['no_hp']): ?>
                    <div class="text-xs text-green-600 mt-1">
                        <i class="fab fa-whatsapp mr-1"></i> <?= htmlspecialchars($wali_kelas['no_hp']) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-700">Daftar Siswa Kelas <?= htmlspecialchars($selected_kelas) ?></h3>
            <button onclick="window.print()" class="text-gray-500 hover:text-gray-900" title="Cetak Data">
                <i class="fas fa-print"></i> Cetak
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NISN / NIS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">L/P</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-users-slash text-4xl mb-3 text-gray-300"></i><br>
                            Belum ada siswa yang terdaftar di kelas ini.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($students as $s): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $no++ ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($s['nisn']) ?></div>
                                <div class="text-xs text-gray-500"><?= htmlspecialchars($s['nis'] ?: '-') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($s['nama_lengkap']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $s['jenis_kelamin'] === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' ?>">
                                    <?= $s['jenis_kelamin'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    .bg-gray-900, .sidebar, nav, form, button, footer { display: none !important; }
    .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
    body { background-color: white !important; }
    .shadow { box-shadow: none !important; border: 1px solid #ddd; }
}
</style>

<?php include 'includes/admin_footer.php'; ?>
