<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'models/User.php';

// Require admin or superadmin role
Auth::requireRole([Auth::ROLE_ADMIN, Auth::ROLE_SUPERADMIN]);

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

// Get current user info
$current_user = Auth::getCurrentUser();

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::blockWriteOperations(); // Block demo users
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $roleIn = isset($_POST['role']) ? strtolower(trim($_POST['role'])) : '';
            $allowedRoles = ['super_admin','admin','guru','staf','orang_tua','demo'];
            $cleanRole = in_array($roleIn, $allowedRoles) ? $roleIn : 'guru';
            $result = $userModel->create(
                $_POST['username'],
                $_POST['email'],
                $_POST['password'],
                $_POST['full_name'],
                $cleanRole,
                $current_user['id']
            );
            // Save subject to admin_users and teacher_profiles if provided
            if (($result['success'] ?? false)) {
                $targetId = $result['id'];
                $subject = $_POST['subject'] ?? '';
                $bio = $_POST['bio'] ?? '';
                $education = $_POST['education'] ?? '';
                $achievements = $_POST['achievements'] ?? '';
                $certificates = $_POST['certificates'] ?? '';
                $training = $_POST['training'] ?? '';
                $photoFilename = null;

                // Handle Photo Upload
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/uploads/teachers/';
                    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
                    
                    $fileInfo = pathinfo($_FILES['photo']['name']);
                    $extension = strtolower($fileInfo['extension']);
                    $allowed = ['jpg', 'jpeg', 'png'];
                    
                    if (in_array($extension, $allowed)) {
                        if ($_FILES['photo']['size'] <= 2 * 1024 * 1024) { // 2MB
                             $newFilename = $targetId . '.' . $extension;
                             $targetPath = $uploadDir . $newFilename;
                             
                             // Remove old files
                             array_map('unlink', glob($uploadDir . $targetId . '.*'));
                             
                             if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                                 $photoFilename = $newFilename;
                             }
                        }
                    }
                }

                if (!empty($subject)) {
                    try {
                        // Try update subject column in admin_users
                        $stmt = $db->prepare("UPDATE admin_users SET subject = ? WHERE id = ?");
                        $stmt->execute([$subject, $targetId]);
                    } catch (Exception $e) {}
                }
                
                try {
                    // Upsert into teacher_profiles
                    // First check if record exists to decide on photo_filename
                    $stmt = $db->prepare("INSERT INTO teacher_profiles (user_id, subject, bio, education, achievements, certificates, training, photo_filename) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE subject = VALUES(subject), bio = VALUES(bio), education = VALUES(education), achievements = VALUES(achievements), certificates = VALUES(certificates), training = VALUES(training)" . ($photoFilename ? ", photo_filename = VALUES(photo_filename)" : ""));
                    $params = [$targetId, $subject, $bio, $education, $achievements, $certificates, $training, $photoFilename];
                    $stmt->execute($params);
                } catch (Exception $e) {}
            }
            Auth::setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
            break;
            
        case 'update':
            $roleIn = isset($_POST['role']) ? strtolower(trim($_POST['role'])) : '';
            $allowedRoles = ['super_admin','admin','guru','staf','orang_tua','demo'];
            $cleanRole = in_array($roleIn, $allowedRoles) ? $roleIn : 'guru';
            $result = $userModel->update(
                $_POST['user_id'],
                $_POST['username'],
                $_POST['email'],
                $_POST['full_name'],
                $cleanRole,
                $_POST['status'],
                $current_user['id']
            );
            
            // Save additional info
            $targetId = $_POST['user_id'];
            $subject = $_POST['subject'] ?? '';
            $bio = $_POST['bio'] ?? '';
            $education = $_POST['education'] ?? '';
            $achievements = $_POST['achievements'] ?? '';
            $certificates = $_POST['certificates'] ?? '';
            $training = $_POST['training'] ?? '';
            $photoFilename = null;

            // Handle Photo Upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/uploads/teachers/';
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $fileInfo = pathinfo($_FILES['photo']['name']);
                $extension = strtolower($fileInfo['extension']);
                $allowed = ['jpg', 'jpeg', 'png'];
                
                if (in_array($extension, $allowed)) {
                    if ($_FILES['photo']['size'] <= 2 * 1024 * 1024) { // 2MB
                            $newFilename = $targetId . '.' . $extension;
                            $targetPath = $uploadDir . $newFilename;
                            
                            // Remove old files
                            array_map('unlink', glob($uploadDir . $targetId . '.*'));
                            
                            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                                $photoFilename = $newFilename;
                            }
                    }
                }
            }

            if (!empty($subject)) {
                try {
                    $stmt = $db->prepare("UPDATE admin_users SET subject = ? WHERE id = ?");
                    $stmt->execute([$subject, $targetId]);
                } catch (Exception $e) {}
            }

            try {
                // Upsert into teacher_profiles
                $query = "INSERT INTO teacher_profiles (user_id, subject, bio, education, achievements, certificates, training, photo_filename) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE subject = VALUES(subject), bio = VALUES(bio), education = VALUES(education), achievements = VALUES(achievements), certificates = VALUES(certificates), training = VALUES(training)";
                if ($photoFilename) {
                    $query .= ", photo_filename = VALUES(photo_filename)";
                }
                $stmt = $db->prepare($query);
                $stmt->execute([$targetId, $subject, $bio, $education, $achievements, $certificates, $training, $photoFilename]);
            } catch (Exception $e) {}

            Auth::setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
            break;
            
        case 'update_password':
            $result = $userModel->updatePassword(
                $_POST['user_id'],
                $_POST['new_password'],
                $current_user['id']
            );
            Auth::setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
            break;
            
        case 'delete':
            $result = $userModel->delete($_POST['user_id']);
            Auth::setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
            break;
            
        case 'update_status':
            $result = $userModel->updateStatus(
                $_POST['user_id'],
                $_POST['status'],
                $current_user['id']
            );
            Auth::setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
            break;
    }
    
    // Redirect to prevent form resubmission
    header('Location: users.php');
    exit;
}

// Handle GET requests for AJAX
if (isset($_GET['action']) && $_GET['action'] === 'get_user' && isset($_GET['id'])) {
    $user = $userModel->getById($_GET['id']);
    header('Content-Type: application/json');
    echo json_encode($user);
    exit;
}

// Get filters
$role_filter = $_GET['role'] ?? '';
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Get users
$users = $userModel->getAll($role_filter, $status_filter, $limit, $offset, $search);

// Get user statistics
$stats = $userModel->getStats();

// Set page title for admin header
$page_title = 'User Management';
?>
<?php include 'includes/admin_header.php'; ?>

    <!-- Header and Stats -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">User Management</h2>
            <?php if (!Auth::isReadOnly()): ?>
            <button onclick="openCreateModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>Tambah User
            </button>
            <?php endif; ?>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['total'] ?></p>
                    </div>
                    <i class="fas fa-users text-green-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Admin</p>
                        <p class="text-2xl font-bold text-green-600"><?= ($stats['admin'] ?? 0) + ($stats['superadmin'] ?? 0) ?></p>
                    </div>
                    <i class="fas fa-user-shield text-green-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Guru</p>
                        <p class="text-2xl font-bold text-green-600"><?= $stats['guru'] ?></p>
                    </div>
                    <i class="fas fa-chalkboard-teacher text-green-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Demo</p>
                        <p class="text-2xl font-bold text-gray-600"><?= $stats['demo'] ?? 0 ?></p>
                    </div>
                    <i class="fas fa-eye text-gray-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" placeholder="Cari username, email, atau nama..." 
                       value="<?= htmlspecialchars($search) ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <select name="role" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Role</option>
                    <option value="super_admin" <?= $role_filter === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                    <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="guru" <?= $role_filter === 'guru' ? 'selected' : '' ?>>Guru</option>
                    <option value="demo" <?= $role_filter === 'demo' ? 'selected' : '' ?>>Demo</option>
                </select>
            </div>
            <div>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Status</option>
                    <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="suspended" <?= $status_filter === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
            <a href="users.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-refresh mr-2"></i>Reset
            </a>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <?php if (!Auth::isReadOnly()): ?>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($users as $user): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['full_name']) ?></div>
                            <div class="text-sm text-gray-500">@<?= htmlspecialchars($user['username']) ?></div>
                            <div class="text-xs text-gray-400"><?= htmlspecialchars($user['email']) ?></div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?= Auth::getRoleBadge($user['role']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?= $userModel->getStatusBadge($user['status']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Never' ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                    </td>
                    <?php if (!Auth::isReadOnly()): ?>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button onclick="openInfoModal(<?= $user['id'] ?>)" class="text-green-600 hover:text-green-900" title="Detail Info">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <button onclick="openEditModal(<?= $user['id'] ?>)" class="text-green-600 hover:text-green-900" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="openPasswordModal(<?= $user['id'] ?>)" class="text-yellow-600 hover:text-yellow-900">
                                <i class="fas fa-key"></i>
                            </button>
                            <?php if ($user['id'] != $current_user['id']): ?>
                            <button onclick="toggleStatus(<?= $user['id'] ?>, '<?= $user['status'] ?>')" class="text-green-600 hover:text-green-900">
                                <i class="fas <?= $user['status'] === 'active' ? 'fa-ban' : 'fa-check' ?>"></i>
                            </button>
                            <button onclick="confirmDelete(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (empty($users)): ?>
    <div class="text-center py-8">
        <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-500">Tidak ada user ditemukan</p>
    </div>
    <?php endif; ?>

<!-- Modals akan ditambahkan dengan JavaScript -->
<?php include 'includes/user_modals.php'; ?>

<script>
        // Auto hide flash messages
        setTimeout(() => {
            const flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                flashMessage.style.display = 'none';
            }
        }, 5000);

        // Modal functions
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function openEditModal(userId) {
            fetch(`users.php?action=get_user&id=${userId}`, { credentials: 'include' })
                .then(r => {
                    const ct = r.headers.get('content-type') || '';
                    if (!r.ok) throw new Error('http');
                    if (!ct.includes('application/json')) throw new Error('not-json');
                    return r.json();
                })
                .then(user => {
                    if (!user || !user.id) return;
                    document.getElementById('edit_user_id').value = user.id || '';
                    document.getElementById('edit_username').value = user.username || '';
                    document.getElementById('edit_email').value = user.email || '';
                    document.getElementById('edit_full_name').value = user.full_name || '';
                    document.getElementById('edit_role').value = user.role || 'demo';
                    var st = user.status;
                    if (st !== 'active' && st !== 'inactive' && st !== 'suspended') {
                        st = (user.is_active == 1 ? 'active' : 'suspended');
                    }
                    document.getElementById('edit_status').value = st;
                    
                    // Populate optional fields
                    const subjectField = document.getElementById('edit_subject');
                    if (subjectField) subjectField.value = user.subject || '';
                    
                    const bioField = document.getElementById('edit_bio');
                    if (bioField) bioField.value = user.bio || '';

                    const educationField = document.getElementById('edit_education');
                    if (educationField) educationField.value = user.education || '';

                    const achievementsField = document.getElementById('edit_achievements');
                    if (achievementsField) achievementsField.value = user.achievements || '';

                    const certificatesField = document.getElementById('edit_certificates');
                    if (certificatesField) certificatesField.value = user.certificates || '';

                    const trainingField = document.getElementById('edit_training');
                    if (trainingField) trainingField.value = user.training || '';
                    
                    document.getElementById('editModal').classList.remove('hidden');
                })
                .catch((err) => {
                    alert('Gagal memuat data user.');
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openPasswordModal(userId) {
            document.getElementById('password_user_id').value = userId;
            document.getElementById('passwordModal').classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
        }

        function openInfoModal(userId) {
            fetch(`users.php?action=get_user&id=${userId}`, { credentials: 'include' })
                .then(r => {
                    const ct = r.headers.get('content-type') || '';
                    if (!r.ok) throw new Error('http');
                    if (!ct.includes('application/json')) throw new Error('not-json');
                    return r.json();
                })
                .then(user => {
                    if (!user || !user.id) return;
                    
                    // Populate Basic Info
                    document.getElementById('info_username').textContent = '@' + user.username;
                    document.getElementById('info_full_name').textContent = user.full_name;
                    document.getElementById('info_email').textContent = user.email;
                    
                    // Role Badge
                    const roleEl = document.getElementById('info_role');
                    roleEl.textContent = user.role.toUpperCase();
                    roleEl.className = 'px-2 py-1 text-xs font-semibold rounded-full ' + getRoleColorClass(user.role);
                    
                    // Status Badge
                    const statusEl = document.getElementById('info_status');
                    var st = user.status;
                    if (st !== 'active' && st !== 'inactive' && st !== 'suspended') {
                        st = (user.is_active == 1 ? 'active' : 'suspended');
                    }
                    statusEl.textContent = st.charAt(0).toUpperCase() + st.slice(1);
                    statusEl.className = 'px-2 py-1 text-xs font-semibold rounded-full ' + (st === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');

                    // Photo
                    const photoEl = document.getElementById('info_photo');
                    if (user.photo_filename) {
                        photoEl.src = 'uploads/teachers/' + user.photo_filename;
                    } else {
                        photoEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.full_name)}&background=random`;
                    }

                    // Populate Additional Info
                    document.getElementById('info_subject').textContent = user.subject || '-';
                    document.getElementById('info_bio').textContent = user.bio || '-';
                    document.getElementById('info_education').textContent = user.education || '-';
                    document.getElementById('info_achievements').textContent = user.achievements || '-';
                    document.getElementById('info_certificates').textContent = user.certificates || '-';
                    document.getElementById('info_training').textContent = user.training || '-';
                    
                    document.getElementById('infoModal').classList.remove('hidden');
                })
                .catch((err) => {
                    console.error(err);
                    alert('Gagal memuat data user.');
                });
        }

        function closeInfoModal() {
            document.getElementById('infoModal').classList.add('hidden');
        }

        function getRoleColorClass(role) {
            switch(role) {
                case 'super_admin': return 'bg-purple-100 text-purple-800';
                case 'admin': return 'bg-green-100 text-green-800';
                case 'guru': return 'bg-green-100 text-green-800';
                case 'staf': return 'bg-yellow-100 text-yellow-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        function confirmDelete(userId, username) {
            if (confirm(`Yakin ingin menghapus user "${username}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function toggleStatus(userId, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'suspended' : 'active';
            const action = newStatus === 'active' ? 'mengaktifkan' : 'menangguhkan';
            
            if (confirm(`Yakin ingin ${action} user ini?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="user_id" value="${userId}">
                    <input type="hidden" name="status" value="${newStatus}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
</script>

<?php include 'includes/admin_footer.php'; ?>
