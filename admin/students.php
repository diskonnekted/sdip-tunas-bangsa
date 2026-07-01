<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'models/Student.php';
require_once 'models/ParentModel.php';

// Require admin or superadmin role
Auth::requireRole([Auth::ROLE_ADMIN, Auth::ROLE_SUPERADMIN]);

$database = new Database();
$db = $database->getConnection();
$studentModel = new Student($db);
$parentModel = new ParentModel($db);

$current_user = Auth::getCurrentUser();
$page_title = 'Data Siswa & Wali';

// Handle Actions (Create/Edit/Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::blockWriteOperations();
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_student') {
        // Create student
        $studentData = [
            'nis' => $_POST['nis'] ?? '',
            'nisn' => $_POST['nisn'] ?? '',
            'nama_lengkap' => $_POST['nama_lengkap'] ?? '',
            'jenis_kelamin' => $_POST['jenis_kelamin'] ?? 'L',
            'tanggal_lahir' => $_POST['tanggal_lahir'] ?? null,
            'kelas' => $_POST['kelas'] ?? ''
        ];
        
        $result = $studentModel->create($studentData);
        if ($result['success']) {
            $studentId = $result['id'];
            // Create Parent Data
            $parentData = [
                'nama_ayah' => $_POST['nama_ayah'] ?? '',
                'nama_ibu' => $_POST['nama_ibu'] ?? '',
                'no_hp_ayah' => $_POST['no_hp_ayah'] ?? '',
                'no_hp_ibu' => $_POST['no_hp_ibu'] ?? '',
                'alamat_lengkap' => $_POST['alamat_lengkap'] ?? '',
                'siswa_id' => $studentId,
                'status_hubungan' => 'Wali',
                'password_default' => $_POST['nisn'] ?? ''
            ];
            
            // Only create parent if at least one phone number is provided
            if (!empty($parentData['no_hp_ayah']) || !empty($parentData['no_hp_ibu'])) {
                $pResult = $parentModel->create($parentData);
                if (!$pResult['success']) {
                    Auth::setFlashMessage('warning', 'Siswa berhasil ditambahkan, namun data orang tua gagal: ' . $pResult['message']);
                } else {
                    Auth::setFlashMessage('success', 'Data siswa dan orang tua berhasil ditambahkan.');
                }
            } else {
                Auth::setFlashMessage('success', 'Data siswa berhasil ditambahkan (Tanpa akun orang tua).');
            }
        } else {
            Auth::setFlashMessage('error', $result['message']);
        }
        
        header('Location: students.php');
        exit;
    }
    
    if ($action === 'update_kelas') {
        $id = $_POST['student_id'];
        $kelas = $_POST['kelas'];
        $student = $studentModel->getById($id);
        if ($student) {
            $student['kelas'] = $kelas;
            $result = $studentModel->update($id, $student);
            Auth::setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
        }
        header('Location: students.php');
        exit;
    }
    
    if ($action === 'delete_student') {
        $result = $studentModel->delete($_POST['student_id']);
        Auth::setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
        header('Location: students.php');
        exit;
    }
}

// Get Students
$search = $_GET['search'] ?? '';
$students = $studentModel->getAll($search);

?>
<?php include 'includes/admin_header.php'; ?>

<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Data Siswa & Wali (7KAIH)</h2>
        <?php if (!Auth::isReadOnly()): ?>
        <button onclick="openCreateModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center shadow-lg transform transition-transform hover:scale-105">
            <i class="fas fa-plus mr-2"></i> Tambah Siswa Baru
        </button>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[250px]">
                <input type="text" name="search" placeholder="Cari NISN atau Nama Siswa..." 
                       value="<?= htmlspecialchars($search) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded-lg shadow-md transition-colors">
                <i class="fas fa-search mr-2"></i> Cari
            </button>
            <?php if(!empty($search)): ?>
            <a href="students.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg transition-colors flex items-center">
                <i class="fas fa-times mr-2"></i> Reset
            </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Data Orang Tua / Wali</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($students as $student): 
                        $parents = $parentModel->getParentsByStudent($student['id']);
                    ?>
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($student['nama_lengkap']) ?></div>
                                    <div class="text-sm text-gray-500">NISN: <?= htmlspecialchars($student['nisn']) ?></div>
                                    <div class="text-xs text-gray-400"><?= $student['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <?= htmlspecialchars($student['kelas'] ?: 'Belum diisi') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php if (empty($parents)): ?>
                                <span class="text-sm text-yellow-600"><i class="fas fa-exclamation-triangle mr-1"></i> Belum terhubung</span>
                            <?php else: ?>
                                <?php foreach ($parents as $p): ?>
                                    <div class="text-sm text-gray-900 mb-1">
                                        <i class="fas fa-user-friends text-gray-400 mr-1"></i>
                                        <?php 
                                            $nAyah = $p['nama_ayah'] ?: '-';
                                            $nIbu = $p['nama_ibu'] ?: '-';
                                            echo "Ayah: <b>$nAyah</b> | Ibu: <b>$nIbu</b>";
                                        ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-mobile-alt text-gray-400 mr-1"></i> 
                                        <?= htmlspecialchars($p['no_hp_ayah'] ?: $p['no_hp_ibu'] ?: 'Tidak ada No HP') ?>
                                        <span class="ml-2 px-2 py-0.5 rounded text-[10px] bg-green-100 text-green-800">Login: <?= htmlspecialchars($p['username']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-3">
                                <a href="jurnal_siswa.php?id=<?= $student['id'] ?>" class="text-blue-500 hover:text-blue-900 transition-colors bg-blue-50 hover:bg-blue-100 p-2 rounded-lg" title="Lihat Jurnal 7KAIH">
                                    <i class="fas fa-book-open"></i> Jurnal 7KAIH
                                </a>
                                <?php if (!Auth::isReadOnly()): ?>
                                <button onclick="editKelas(<?= $student['id'] ?>, '<?= htmlspecialchars($student['kelas']) ?>')" class="text-green-500 hover:text-green-900 transition-colors bg-green-50 hover:bg-green-100 p-2 rounded-lg" title="Ubah Kelas">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteStudent(<?= $student['id'] ?>)" class="text-red-500 hover:text-red-900 transition-colors bg-red-50 hover:bg-red-100 p-2 rounded-lg" title="Hapus Siswa">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada data siswa ditemukan.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Siswa & Wali -->
<div id="createModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 my-8 relative overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 to-gray-900 px-6 py-4 flex justify-between items-center text-white">
            <h3 class="text-xl font-bold"><i class="fas fa-user-plus mr-2"></i>Tambah Data Siswa & Wali</h3>
            <button onclick="closeCreateModal()" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="students.php" method="POST" class="p-6">
            <input type="hidden" name="action" value="create_student">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Data Siswa -->
                <div>
                    <h4 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">
                        <i class="fas fa-user-graduate text-red-600 mr-2"></i>Data Siswa
                    </h4>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Siswa *</label>
                            <input type="text" name="nama_lengkap" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NISN *</label>
                                <input type="text" name="nisn" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <p class="text-xs text-gray-500 mt-1">Digunakan sebagai password awal wali.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIS Lokal</label>
                                <input type="text" name="nis"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin *</label>
                                <select name="jenis_kelamin" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                                <select name="kelas" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                    <option value="">Pilih Kelas</option>
                                    <?php 
                                        for ($i = 1; $i <= 6; $i++) {
                                            foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $j) {
                                                echo "<option value='{$i}{$j}'>{$i}{$j}</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Orang Tua -->
                <div>
                    <h4 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">
                        <i class="fas fa-users text-red-600 mr-2"></i>Data Orang Tua / Wali
                    </h4>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label>
                                <input type="text" name="nama_ayah"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP Ayah (WhatsApp)</label>
                                <input type="text" name="no_hp_ayah" placeholder="08..."
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label>
                                <input type="text" name="nama_ibu"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP Ibu (WhatsApp)</label>
                                <input type="text" name="no_hp_ibu" placeholder="08..."
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                            <textarea name="alamat_lengkap" rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                            <p class="text-xs text-yellow-800">
                                <i class="fas fa-info-circle mr-1"></i> Akun Login Orang Tua akan otomatis dibuat. <strong>Username:</strong> No. HP (Ayah/Ibu), <strong>Password:</strong> NISN Siswa.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeCreateModal()" 
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-md transition-colors">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form Delete Siswa -->
<form id="deleteForm" action="students.php" method="POST" class="hidden">
    <input type="hidden" name="action" value="delete_student">
    <input type="hidden" name="student_id" id="deleteStudentId">
</form>

<!-- Form Update Kelas -->
<form id="editKelasForm" action="students.php" method="POST" class="hidden">
    <input type="hidden" name="action" value="update_kelas">
    <input type="hidden" name="student_id" id="editKelasId">
    <input type="hidden" name="kelas" id="editKelasName">
</form>

<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

function deleteStudent(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data siswa ini? Seluruh jurnal harian 7KAIH miliknya juga akan ikut terhapus!')) {
        document.getElementById('deleteStudentId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

function editKelas(id, currentKelas) {
    let newKelas = prompt('Masukkan nama kelas (misal: 1A, 2B):', currentKelas);
    if (newKelas !== null && newKelas.trim() !== '') {
        document.getElementById('editKelasId').value = id;
        document.getElementById('editKelasName').value = newKelas;
        document.getElementById('editKelasForm').submit();
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>
