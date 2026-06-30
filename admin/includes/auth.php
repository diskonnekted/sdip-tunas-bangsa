<?php
// Authentication middleware untuk role-based access control
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class Auth {
    // User roles
    const ROLE_SUPERADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_GURU = 'guru';
    const ROLE_STAF = 'staf';
    const ROLE_ORANG_TUA = 'orang_tua';
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function getCurrentUser() {
        if (!self::isLoggedIn()) {
            return null;
        }

        // Get basic user data from session
        $user_data = [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['user_username'] ?? null,
            'name' => $_SESSION['user_name'] ?? null,
            'role' => $_SESSION['user_role'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'last_login' => null
        ];

        // Try to get last_login from database if user_id exists
        if ($user_data['id']) {
            try {
                require_once __DIR__ . '/../config/database.php';
                $database = new Database();
                $db = $database->getConnection();
                
                $query = "SELECT last_login FROM users WHERE id = ? LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->execute([$user_data['id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result) {
                    $user_data['last_login'] = $result['last_login'];
                }
            } catch (Exception $e) {
                // If there's an error, just log it and continue with null last_login
                error_log('Error getting last_login: ' . $e->getMessage());
            }
        }

        return $user_data;
    }

    public static function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }

    public static function requireLogin($redirect_url = 'login.php') {
        if (!self::isLoggedIn()) {
            header('Location: ' . $redirect_url);
            exit;
        }
    }

    public static function requireRole($required_roles, $redirect_url = 'index.php') {
        self::requireLogin();
        
        $current_role = self::getUserRole();
        
        // Convert single role to array
        if (!is_array($required_roles)) {
            $required_roles = [$required_roles];
        }
        
        if (!in_array($current_role, $required_roles)) {
            // Log unauthorized access attempt
            error_log("Unauthorized access attempt by user " . $_SESSION['user_username'] . " (role: $current_role) to " . $_SERVER['REQUEST_URI']);
            
            // Set flash message
            $_SESSION['flash_error'] = 'Anda tidak memiliki akses ke halaman tersebut.';
            header('Location: ' . $redirect_url);
            exit;
        }
    }

    public static function canManageUsers() {
        $role = self::getUserRole();
        return in_array($role, [self::ROLE_SUPERADMIN, self::ROLE_ADMIN]);
    }

    public static function canEditContent() {
        $role = self::getUserRole();
        return in_array($role, [self::ROLE_SUPERADMIN, self::ROLE_ADMIN, self::ROLE_GURU]);
    }

    public static function isReadOnly() {
        return false; // No more demo role
    }

    public static function canDeleteContent() {
        $role = self::getUserRole();
        return in_array($role, [self::ROLE_SUPERADMIN, self::ROLE_ADMIN]);
    }

    public static function canViewSettings() {
        $role = self::getUserRole();
        return in_array($role, [self::ROLE_SUPERADMIN, self::ROLE_ADMIN]);
    }

    public static function canManageMessages() {
        $role = self::getUserRole();
        return in_array($role, [self::ROLE_SUPERADMIN, self::ROLE_ADMIN, self::ROLE_GURU]);
    }

    public static function logout() {
        // Clear all session data
        $_SESSION = array();
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Clear remember cookie
        if (isset($_COOKIE['admin_remember'])) {
            setcookie('admin_remember', '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
    }

    public static function getRoleBadge($role) {
        $role = strtolower(trim((string)$role));
        if ($role === 'superadmin') $role = 'super_admin';
        if ($role === '') $role = 'guru';
        $badges = [
            'super_admin' => '<span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Super Admin</span>',
            'admin' => '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Admin</span>',
            'guru' => '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Guru</span>',
            'staf' => '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Staf</span>',
            'orang_tua' => '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Wali Murid</span>'
        ];

        return $badges[$role] ?? '<span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Unknown</span>';
    }

    public static function getFlashMessage($type = '') {
        $message = null;
        
        if ($type) {
            $key = 'flash_' . $type;
            if (isset($_SESSION[$key])) {
                $message = $_SESSION[$key];
                unset($_SESSION[$key]);
            }
        } else {
            // Get any flash message
            $flash_keys = ['flash_success', 'flash_error', 'flash_warning', 'flash_info'];
            foreach ($flash_keys as $key) {
                if (isset($_SESSION[$key])) {
                    $message = ['type' => str_replace('flash_', '', $key), 'message' => $_SESSION[$key]];
                    unset($_SESSION[$key]);
                    break;
                }
            }
        }
        
        return $message;
    }

    public static function setFlashMessage($type, $message) {
        $_SESSION['flash_' . $type] = $message;
    }

    // Check if current request is for a read operation
    public static function isReadOperation() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? '';
        
        // GET requests are generally read operations
        if ($method === 'GET') {
            return true;
        }
        
        // Specific read actions
        $read_actions = ['view', 'search', 'filter', 'export'];
        if (in_array($action, $read_actions)) {
            return true;
        }
        
        return false;
    }
}

// Helper function to check if user has permission for specific action
function hasPermission($action) {
    $role = Auth::getUserRole();
    
    switch ($action) {
        case 'manage_users':
            return Auth::canManageUsers();
        case 'edit_content':
            return Auth::canEditContent();
        case 'delete_content':
            return Auth::canDeleteContent();
        case 'view_settings':
            return Auth::canViewSettings();
        case 'manage_messages':
            return Auth::canManageMessages();
        default:
            return false;
    }
}
?>
