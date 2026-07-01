<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'models/PpdbRegistration.php';
require_once 'includes/functions.php';

// Require login
Auth::requireLogin();

// Initialize database
$database = new Database();
$db = $database->getConnection();
$ppdb = new PpdbRegistration($db);

// Get Statistics
$stats = $ppdb->getStats();

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    
    if (in_array($status, ['pending', 'approved', 'rejected'])) {
        if ($ppdb->updateStatus($id, $status)) {
            $success_message = "Status pendaftaran berhasil diperbarui.";
            
            // Auto sync to Siswa if approved
            if ($status === 'approved') {
                require_once 'models/Student.php';
                require_once 'models/ParentModel.php';
                $studentModel = new Student($db);
                $parentModel = new ParentModel($db);
                
                // Get PPDB details
                $reg = $ppdb->getById($id);
                if ($reg) {
                    $nisn_temp = 'PPDB-' . $reg['registration_number'];
                    
                    // Add to Siswa
                    $studentData = [
                        'nis' => null,
                        'nisn' => $nisn_temp,
                        'nama_lengkap' => $reg['child_name'],
                        'jenis_kelamin' => $reg['gender'],
                        'tanggal_lahir' => $reg['dob'],
                        'kelas' => 'Siswa Baru'
                    ];
                    $sResult = $studentModel->create($studentData);
                    
                    if ($sResult['success']) {
                        $studentId = $sResult['id'];
                        // Add to Parent
                        $parentData = [
                            'nama_ayah' => $reg['parent_name'],
                            'nama_ibu' => '',
                            'no_hp_ayah' => $reg['parent_phone'],
                            'no_hp_ibu' => '',
                            'alamat_lengkap' => $reg['address'],
                            'siswa_id' => $studentId,
                            'status_hubungan' => 'Wali',
                            'password_default' => $nisn_temp
                        ];
                        $pResult = $parentModel->create($parentData);
                        if ($pResult['success']) {
                            $success_message .= " Data Siswa & Wali otomatis digenerate.";
                        } else {
                            $success_message .= " Siswa disinkron, tapi pembuatan akun Wali gagal.";
                        }
                    }
                }
            }

            // Refresh stats
            $stats = $ppdb->getStats();
        } else {
            $error_message = "Gagal memperbarui status.";
        }
    }
}

// Handle Export
if (isset($_GET['action']) && $_GET['action'] == 'export') {
    $filename = "Data_PPDB_" . date('Y-m-d') . ".xls";
    
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $registrations = $ppdb->getAll('', 10000, 0); // Get all (limit 10000)

    echo "<table border='1'>";
    echo "<thead>";
    echo "<tr>
            <th style='background-color:#f2f2f2;'>No. Pendaftaran</th>
            <th style='background-color:#f2f2f2;'>Nama Anak</th>
            <th style='background-color:#f2f2f2;'>Jenis Kelamin</th>
            <th style='background-color:#f2f2f2;'>Tanggal Lahir</th>
            <th style='background-color:#f2f2f2;'>Asal Sekolah</th>
            <th style='background-color:#f2f2f2;'>Nama Orang Tua</th>
            <th style='background-color:#f2f2f2;'>No. Telepon</th>
            <th style='background-color:#f2f2f2;'>Email</th>
            <th style='background-color:#f2f2f2;'>Alamat</th>
            <th style='background-color:#f2f2f2;'>Status</th>
            <th style='background-color:#f2f2f2;'>Tanggal Daftar</th>
          </tr>";
    echo "</thead>";
    echo "<tbody>";
    
    foreach ($registrations as $reg) {
        $gender = $reg['gender'] == 'L' ? 'Laki-laki' : 'Perempuan';
        echo "<tr>";
        echo "<td>" . htmlspecialchars($reg['registration_number']) . "</td>";
        echo "<td>" . htmlspecialchars($reg['child_name']) . "</td>";
        echo "<td>" . htmlspecialchars($gender) . "</td>";
        echo "<td>" . htmlspecialchars($reg['dob']) . "</td>";
        echo "<td>" . htmlspecialchars($reg['previous_school'] ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($reg['parent_name']) . "</td>";
        echo "<td>'" . htmlspecialchars($reg['parent_phone']) . "</td>"; // Prevent scientific notation
        echo "<td>" . htmlspecialchars($reg['email']) . "</td>";
        echo "<td>" . htmlspecialchars($reg['address']) . "</td>";
        echo "<td>" . htmlspecialchars(ucfirst($reg['status'])) . "</td>";
        echo "<td>" . htmlspecialchars($reg['created_at']) . "</td>";
        echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
    exit;
}

// Get Data for View
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

$total_records = $ppdb->count();
$total_pages = ceil($total_records / $per_page);

$registrations = $ppdb->getAll('', $per_page, $offset);

$page_title = 'Data PPDB';
include 'includes/admin_header.php';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Penerimaan Peserta Didik Baru</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola data pendaftaran siswa baru</p>
        </div>
        <a href="ppdb.php?action=export" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <i class="fas fa-file-excel mr-2"></i> Export Excel
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Pendaftar -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Total Pendaftar</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $stats['total'] ?></p>
                </div>
            </div>
        </div>

        <!-- Menunggu -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Menunggu</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $stats['pending'] ?></p>
                </div>
            </div>
        </div>

        <!-- Diterima -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Diterima</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $stats['approved'] ?></p>
                </div>
            </div>
        </div>

        <!-- Ditolak -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Ditolak</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $stats['rejected'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="rounded-md bg-green-50 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?php echo $success_message; ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
        <div class="rounded-md bg-red-50 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800"><?php echo $error_message; ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

    <!-- Registrations Table -->
    <div class="bg-white shadow overflow-hidden rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Data Pendaftar
            </h3>
        </div>
        <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Pendaftaran</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Anak</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asal Sekolah</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Orang Tua</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($registrations)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                <p>Belum ada data pendaftaran.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($registrations as $reg): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-primary-600"><?php echo htmlspecialchars($reg['registration_number']); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($reg['child_name']); ?></div>
                                    <div class="text-xs text-gray-500">
                                        <?php echo $reg['gender'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?>, 
                                        Lahir: <?php echo htmlspecialchars($reg['dob']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($reg['previous_school'] ?? '-'); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($reg['parent_name']); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($reg['parent_phone']); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($reg['email']); ?></div>
                                    <div class="text-xs text-gray-400 truncate max-w-xs" title="<?php echo htmlspecialchars($reg['address']); ?>"><?php echo htmlspecialchars($reg['address']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php 
                                            echo $reg['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                                ($reg['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); 
                                        ?>">
                                        <?php echo ucfirst($reg['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('d M Y H:i', strtotime($reg['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if ($reg['status'] === 'pending'): ?>
                                        <div class="flex space-x-2">
                                            <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menerima pendaftaran ini?');">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded-md text-xs">
                                                    Terima
                                                </button>
                                            </form>
                                            <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak pendaftaran ini?');">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded-md text-xs">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <form method="POST" onsubmit="return confirm('Kembalikan status ke Pending?');">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="text-gray-600 hover:text-gray-900 text-xs underline">
                                                Reset Status
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</a>
                        <?php endif; ?>
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</a>
                        <?php endif; ?>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to <span class="font-medium"><?php echo min($offset + $per_page, $total_records); ?></span> of <span class="font-medium"><?php echo $total_records; ?></span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="?page=<?php echo $i; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $page ? 'text-primary-600 bg-primary-50' : 'text-gray-500 hover:bg-gray-50'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
</div>

<?php include 'includes/admin_footer.php'; ?>