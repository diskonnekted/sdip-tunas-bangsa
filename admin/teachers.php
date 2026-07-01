<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'models/User.php';

// Require admin or superadmin role
Auth::requireRole([Auth::ROLE_ADMIN, Auth::ROLE_SUPERADMIN]);

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

$current_user = Auth::getCurrentUser();
$page_title = 'Data Guru & Staf';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::blockWriteOperations();
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $role = $_POST['role'] === 'staf' ? 'staf' : 'guru';
        
        $result = $userModel->create(
            $_POST['username'],
            $_POST['email'],
            $_POST['password'],
            $_POST['full_name'],
            $role,
            $current_user['id']
        );
        
        if ($result['success']) {
            $userId = $result['id'];
            $subject = $_POST['subject'] ?? '';
            $bio = $_POST['bio'] ?? '';
            $tgl_lahir = !empty($_POST['tgl_lahir']) ? $_POST['tgl_lahir'] : null;
            $no_hp = $_POST['no_hp'] ?? '';
            $wali_kelas = $_POST['wali_kelas'] ?? '';
            
            // Insert into teacher_profiles
            $stmt = $db->prepare("INSERT INTO teacher_profiles (user_id, subject, bio, tgl_lahir, no_hp, wali_kelas) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $subject, $bio, $tgl_lahir, $no_hp, $wali_kelas]);
            
            Auth::setFlashMessage('success', 'Data Guru/Staf berhasil ditambahkan.');
        } else {
            Auth::setFlashMessage('error', $result['message']);
        }
        
        header('Location: teachers.php');
        exit;
    }
    
    if ($action === 'update') {
        $userId = $_POST['user_id'];
        $role = $_POST['role'] === 'staf' ? 'staf' : 'guru';
        $status = isset($_POST['status']) ? intval($_POST['status']) : 1;
        
        $result = $userModel->update(
            $userId,
            $_POST['username'],
            $_POST['email'],
            $_POST['full_name'],
            $role,
            $status,
            $current_user['id']
        );
        
        // Try update password if provided
        if (!empty($_POST['password'])) {
            $userModel->updatePassword($userId, $_POST['password'], $current_user['id']);
        }
        
        if ($result['success']) {
            $subject = $_POST['subject'] ?? '';
            $bio = $_POST['bio'] ?? '';
            $tgl_lahir = !empty($_POST['tgl_lahir']) ? $_POST['tgl_lahir'] : null;
            $no_hp = $_POST['no_hp'] ?? '';
            $wali_kelas = $_POST['wali_kelas'] ?? '';
            
            // Check if profile exists
            $check = $db->prepare("SELECT id FROM teacher_profiles WHERE user_id = ?");
            $check->execute([$userId]);
            if ($check->fetch()) {
                $stmt = $db->prepare("UPDATE teacher_profiles SET subject=?, bio=?, tgl_lahir=?, no_hp=?, wali_kelas=? WHERE user_id=?");
                $stmt->execute([$subject, $bio, $tgl_lahir, $no_hp, $wali_kelas, $userId]);
            } else {
                $stmt = $db->prepare("INSERT INTO teacher_profiles (user_id, subject, bio, tgl_lahir, no_hp, wali_kelas) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $subject, $bio, $tgl_lahir, $no_hp, $wali_kelas]);
            }
            
            Auth::setFlashMessage('success', 'Data Guru/Staf berhasil diperbarui.');
        } else {
            Auth::setFlashMessage('error', $result['message']);
        }
        
        header('Location: teachers.php');
        exit;
    }
    
    if ($action === 'delete') {
        $userId = $_POST['user_id'];
        
        // Also delete from teacher_profiles if exists
        $stmt = $db->prepare("DELETE FROM teacher_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        $result = $userModel->delete($userId);
        Auth::setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
        
        header('Location: teachers.php');
        exit;
    }
}

// Get teachers and staff
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';

$query = "SELECT u.*, tp.subject, tp.tgl_lahir, tp.no_hp, tp.wali_kelas, tp.photo_filename 
          FROM admin_users u 
          LEFT JOIN teacher_profiles tp ON u.id = tp.user_id 
          WHERE u.role IN ('guru', 'staf')";

$params = [];

if (!empty($search)) {
    $query .= " AND (u.full_name LIKE ? OR u.username LIKE ? OR tp.subject LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($role_filter)) {
    $query .= " AND u.role = ?";
    $params[] = $role_filter;
}

$query .= " ORDER BY u.full_name ASC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate list of classes from 1A to 6F
$existing_classes = [];
foreach (range(1, 6) as $grade) {
    foreach (range('A', 'F') as $section) {
        $existing_classes[] = $grade . $section;
    }
}

?>
<?php include 'includes/admin_header.php'; ?>

<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Data Guru & Staf</h2>
        <?php if (!Auth::isReadOnly()): ?>
        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center shadow-lg transform transition-transform hover:scale-105">
            <i class="fas fa-plus mr-2"></i> Tambah Guru/Staf
        </button>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" placeholder="Cari nama atau jabatan..." 
                       value="<?= htmlspecialchars($search) ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="role" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Role</option>
                    <option value="guru" <?= $role_filter === 'guru' ? 'selected' : '' ?>>Guru</option>
                    <option value="staf" <?= $role_filter === 'staf' ? 'selected' : '' ?>>Staf</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
            <a href="teachers.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-refresh mr-2"></i>Reset
            </a>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama & Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role & Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Lahir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Akun</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($teachers)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data guru atau staf yang ditemukan.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($teachers as $t): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center overflow-hidden">
                                        <?php if (!empty($t['photo_filename'])): ?>
                                            <img src="uploads/teachers/<?= htmlspecialchars($t['photo_filename']) ?>" class="h-10 w-10 object-cover">
                                        <?php else: ?>
                                            <i class="fas fa-user text-gray-400"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($t['full_name']) ?></div>
                                        <div class="text-xs text-gray-500">
                                            <i class="fab fa-whatsapp text-green-500 mr-1"></i> <?= htmlspecialchars($t['no_hp'] ?: '-') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $t['role'] === 'guru' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' ?>">
                                    <?= ucfirst($t['role']) ?>
                                </span>
                                <?php if (!empty($t['wali_kelas'])): ?>
                                <span class="ml-1 px-2 inline-flex text-[10px] leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    Wali Kelas: <?= htmlspecialchars($t['wali_kelas']) ?>
                                </span>
                                <?php endif; ?>
                                <div class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($t['subject'] ?: '-') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $t['tgl_lahir'] ? date('d/m/Y', strtotime($t['tgl_lahir'])) : '-' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($t['is_active']): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Nonaktif</span>
                                <?php endif; ?>
                                <div class="text-[10px] text-gray-400 mt-1">Username: <?= htmlspecialchars($t['username']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <?php if (!Auth::isReadOnly()): ?>
                                    <button onclick='editTeacher(<?= json_encode($t, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-green-500 hover:text-green-900 transition-colors bg-green-50 hover:bg-green-100 p-2 rounded-lg" title="Edit Guru/Staf">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteTeacher(<?= $t['id'] ?>)" class="text-red-500 hover:text-red-900 transition-colors bg-red-50 hover:bg-red-100 p-2 rounded-lg" title="Hapus Guru/Staf">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Guru/Staf -->
<div id="createModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-2xl w-full max-w-2xl my-8 transform transition-all">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 rounded-t-2xl flex justify-between items-center">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-user-plus mr-2"></i> Tambah Guru/Staf
            </h3>
            <button onclick="closeCreateModal()" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="teachers.php" method="POST" class="p-6">
            <input type="hidden" name="action" value="create">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Pribadi -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-700 border-b pb-2"><i class="fas fa-id-card mr-2 text-blue-500"></i>Data Pribadi & Kontak</h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="full_name" required
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir"
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No HP / WhatsApp</label>
                        <input type="text" name="no_hp" placeholder="Contoh: 08123456789"
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bio Singkat</label>
                        <textarea name="bio" rows="2" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>
                
                <!-- Data Akun & Jabatan -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-700 border-b pb-2"><i class="fas fa-user-shield mr-2 text-blue-500"></i>Data Akun & Jabatan</h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role / Peran</label>
                        <select name="role" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="guru">Guru</option>
                            <option value="staf">Staf TU / Pegawai</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan / Guru Mapel</label>
                        <input type="text" name="subject" placeholder="Contoh: Guru Matematika"
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div id="waliKelasContainer">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas (Opsional)</label>
                        <select name="wali_kelas" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Bukan Wali Kelas / Pilih Kelas --</option>
                            <?php foreach($existing_classes as $kls): ?>
                                <option value="<?= htmlspecialchars($kls) ?>"><?= htmlspecialchars($kls) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username Login</label>
                        <input type="text" name="username" required
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Login</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Aktif (Opsional)</label>
                        <input type="email" name="email" value="dummy@mail.com"
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end space-x-3 pt-6 border-t">
                <button type="button" onclick="closeCreateModal()" 
                        class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/30">
                    <i class="fas fa-save mr-2"></i> Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form Delete -->
<form id="deleteForm" action="teachers.php" method="POST" class="hidden">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="user_id" id="deleteUserId">
</form>

<script>
function openCreateModal() {
    // Reset form to Create mode
    document.querySelector('#createModal form').reset();
    document.querySelector('#createModal input[name="action"]').value = "create";
    document.querySelector('#createModal h3').innerHTML = '<i class="fas fa-user-plus mr-2"></i> Tambah Guru/Staf';
    document.querySelector('#createModal input[name="password"]').required = true;
    document.querySelector('#createModal input[name="password"]').placeholder = '';
    let statusContainer = document.getElementById('statusContainer');
    if (statusContainer) statusContainer.style.display = 'none';
    
    document.getElementById('createModal').classList.remove('hidden');
}

function editTeacher(t) {
    document.querySelector('#createModal form').reset();
    document.querySelector('#createModal input[name="action"]').value = "update";
    document.querySelector('#createModal h3').innerHTML = '<i class="fas fa-user-edit mr-2"></i> Edit Guru/Staf';
    
    if (!document.getElementById('editUserId')) {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_id';
        input.id = 'editUserId';
        document.querySelector('#createModal form').appendChild(input);
    }
    document.getElementById('editUserId').value = t.id;
    
    document.querySelector('#createModal input[name="full_name"]').value = t.full_name;
    document.querySelector('#createModal input[name="tgl_lahir"]').value = t.tgl_lahir || '';
    document.querySelector('#createModal input[name="no_hp"]').value = t.no_hp || '';
    document.querySelector('#createModal textarea[name="bio"]').value = t.bio || '';
    document.querySelector('#createModal select[name="role"]').value = t.role;
    document.querySelector('#createModal input[name="subject"]').value = t.subject || '';
    
    // Set wali_kelas select
    let waliKelasSelect = document.querySelector('#createModal select[name="wali_kelas"]');
    if (t.wali_kelas) {
        // Check if the option exists, if not, add it so the value can be set
        let optionExists = Array.from(waliKelasSelect.options).some(opt => opt.value === t.wali_kelas);
        if (!optionExists) {
            let newOption = new Option(t.wali_kelas, t.wali_kelas);
            waliKelasSelect.add(newOption);
        }
        waliKelasSelect.value = t.wali_kelas;
    } else {
        waliKelasSelect.value = '';
    }

    document.querySelector('#createModal input[name="username"]').value = t.username;
    document.querySelector('#createModal input[name="email"]').value = t.email || '';
    document.querySelector('#createModal input[name="password"]').required = false;
    document.querySelector('#createModal input[name="password"]').placeholder = 'Kosongkan jika tidak diubah';
    
    if (!document.getElementById('statusContainer')) {
        let statusHtml = `
        <div class="mt-4" id="statusContainer">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status Akun</label>
            <select name="status" id="statusSelect" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
        </div>`;
        document.querySelector('#createModal select[name="role"]').parentElement.insertAdjacentHTML('afterend', statusHtml);
    } else {
        document.getElementById('statusContainer').style.display = 'block';
    }
    document.getElementById('statusSelect').value = t.is_active;

    toggleWaliKelas();
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

function toggleWaliKelas() {
    let roleSelect = document.querySelector('#createModal select[name="role"]');
    let waliKelasContainer = document.getElementById('waliKelasContainer');
    if (roleSelect && waliKelasContainer) {
        if (roleSelect.value === 'staf') {
            waliKelasContainer.style.display = 'none';
            document.querySelector('#createModal select[name="wali_kelas"]').value = '';
        } else {
            waliKelasContainer.style.display = 'block';
        }
    }
}

// Attach event listener to role select to hide/show wali_kelas
document.addEventListener("DOMContentLoaded", function() {
    let roleSelect = document.querySelector('#createModal select[name="role"]');
    if (roleSelect) {
        roleSelect.addEventListener('change', toggleWaliKelas);
    }
});

function deleteTeacher(id) {
    if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
        document.getElementById('deleteUserId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>
