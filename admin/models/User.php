<?php
class User {
    private $conn;
    private $table_name = "admin_users";
    private $hasTeacherProfiles = null;
    private $hasSubjectColumn = null;

    // User roles
    const ROLE_SUPERADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_GURU = 'guru';
    const ROLE_STAF = 'staf';

    public function __construct($db) {
        $this->conn = $db;
    }

    private function hasTeacherProfiles() {
        if ($this->hasTeacherProfiles === null) {
            try {
                $this->conn->query("SELECT 1 FROM teacher_profiles LIMIT 1");
                $this->hasTeacherProfiles = true;
            } catch (Exception $e) {
                $this->hasTeacherProfiles = false;
            }
        }
        return $this->hasTeacherProfiles;
    }

    private function hasSubjectColumn() {
        if ($this->hasSubjectColumn === null) {
            try {
                $stmt = $this->conn->query("SHOW COLUMNS FROM " . $this->table_name . " LIKE 'subject'");
                $this->hasSubjectColumn = $stmt && $stmt->rowCount() > 0;
            } catch (Exception $e) {
                $this->hasSubjectColumn = false;
            }
        }
        return $this->hasSubjectColumn;
    }

    public function create($username, $email, $password, $full_name, $role = self::ROLE_GURU, $created_by = null) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, password, full_name, role, is_active, created_at) 
                  VALUES (?, ?, ?, ?, ?, 1, NOW())";

        try {
            $stmt = $this->conn->prepare($query);
            
            // Sanitize input
            $username = htmlspecialchars(strip_tags(trim($username)));
            $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
            $full_name = htmlspecialchars(strip_tags(trim($full_name)));
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt->execute([
                $username,
                $email,
                $hashed_password,
                $full_name,
                $role
            ]);

            return [
                'success' => true,
                'id' => $this->conn->lastInsertId(),
                'message' => 'User created successfully'
            ];

        } catch(Exception $e) {
            error_log("Error creating user: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function authenticate($username, $password) {
        $query = "SELECT id, username, email, password, full_name, role, is_active 
                  FROM " . $this->table_name . " 
                  WHERE (username = ? OR email = ?) AND is_active = 1";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Update last login
                    $this->updateLastLogin($user['id']);
                    
                    unset($user['password']); // Remove password from return data
                    return [
                        'success' => true,
                        'user' => $user,
                        'message' => 'Login successful'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Invalid credentials'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid credentials'
                ];
            }

        } catch(PDOException $e) {
            error_log("Error authenticating user: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error'
            ];
        }
    }

    private function updateLastLogin($userId) {
        $query = "UPDATE " . $this->table_name . " SET last_login = NOW() WHERE id = ?";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);
        } catch(PDOException $e) {
            error_log("Error updating last login: " . $e->getMessage());
        }
    }

    public function getAll($role = '', $status = '', $limit = 50, $offset = 0, $search = '') {
        if ($this->hasTeacherProfiles()) {
            if ($this->hasSubjectColumn()) {
                $query = "SELECT au.id, au.username, au.email, au.full_name, au.role, au.is_active, 
                          CASE WHEN au.is_active = 1 THEN 'active' ELSE 'suspended' END as status,
                          au.last_login, au.created_at, COALESCE(tp.subject, au.subject) AS subject,
                          tp.photo_filename, tp.bio, tp.education, tp.achievements, tp.certificates, tp.training
                          FROM " . $this->table_name . " au LEFT JOIN teacher_profiles tp ON tp.user_id = au.id";
            } else {
                $query = "SELECT au.id, au.username, au.email, au.full_name, au.role, au.is_active, 
                          CASE WHEN au.is_active = 1 THEN 'active' ELSE 'suspended' END as status,
                          au.last_login, au.created_at, tp.subject AS subject,
                          tp.photo_filename, tp.bio, tp.education, tp.achievements, tp.certificates, tp.training
                          FROM " . $this->table_name . " au LEFT JOIN teacher_profiles tp ON tp.user_id = au.id";
            }
        } else {
            if ($this->hasSubjectColumn()) {
                $query = "SELECT id, username, email, full_name, role, is_active, 
                          CASE WHEN is_active = 1 THEN 'active' ELSE 'suspended' END as status,
                          last_login, created_at, subject, NULL as photo_filename, NULL as bio, NULL as education, NULL as achievements, NULL as certificates, NULL as training FROM " . $this->table_name;
            } else {
                $query = "SELECT id, username, email, full_name, role, is_active, 
                          CASE WHEN is_active = 1 THEN 'active' ELSE 'suspended' END as status,
                          last_login, created_at, NULL AS subject, NULL as photo_filename, NULL as bio, NULL as education, NULL as achievements, NULL as certificates, NULL as training FROM " . $this->table_name;
            }
        }
        $conditions = [];
        $params = [];

        if (!empty($role)) {
            $conditions[] = ($this->hasTeacherProfiles() ? "LOWER(au.role) = LOWER(?)" : "LOWER(role) = LOWER(?)");
            $params[] = $role;
        }

        if ($status !== '') {
            $conditions[] = ($this->hasTeacherProfiles() ? "au.is_active = ?" : "is_active = ?");
            $params[] = ($status === 'active' ? 1 : (($status === 'suspended' || $status === 'inactive') ? 0 : $status));
        }

        if (!empty($search)) {
            $conditions[] = ($this->hasTeacherProfiles() ? "(au.username LIKE ? OR au.email LIKE ? OR au.full_name LIKE ?)" : "(username LIKE ? OR email LIKE ? OR full_name LIKE ?)" );
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $limit = (int)$limit;
        $offset = (int)$offset;
        $query .= $this->hasTeacherProfiles() ? " ORDER BY au.created_at DESC LIMIT $limit OFFSET $offset" : " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        if ($this->hasTeacherProfiles()) {
            if ($this->hasSubjectColumn()) {
                $query = "SELECT au.id, au.username, au.email, au.full_name, au.role, au.is_active, 
                          CASE WHEN au.is_active = 1 THEN 'active' ELSE 'suspended' END as status,
                          au.last_login, au.created_at, COALESCE(tp.subject, au.subject) AS subject,
                          tp.photo_filename, tp.bio, tp.education, tp.achievements, tp.certificates, tp.training
                          FROM " . $this->table_name . " au LEFT JOIN teacher_profiles tp ON tp.user_id = au.id WHERE au.id = ?";
            } else {
                $query = "SELECT au.id, au.username, au.email, au.full_name, au.role, au.is_active, 
                          CASE WHEN au.is_active = 1 THEN 'active' ELSE 'suspended' END as status,
                          au.last_login, au.created_at, tp.subject AS subject,
                          tp.photo_filename, tp.bio, tp.education, tp.achievements, tp.certificates, tp.training
                          FROM " . $this->table_name . " au LEFT JOIN teacher_profiles tp ON tp.user_id = au.id WHERE au.id = ?";
            }
        } else {
            if ($this->hasSubjectColumn()) {
                $query = "SELECT id, username, email, full_name, role, is_active, 
                          CASE WHEN is_active = 1 THEN 'active' ELSE 'suspended' END as status,
                          last_login, created_at, subject, NULL as photo_filename, NULL as bio, NULL as education, NULL as achievements, NULL as certificates, NULL as training FROM " . $this->table_name . " WHERE id = ?";
            } else {
                $query = "SELECT id, username, email, full_name, role, is_active, 
                          CASE WHEN is_active = 1 THEN 'active' ELSE 'suspended' END as status,
                          last_login, created_at, NULL AS subject, NULL as photo_filename, NULL as bio, NULL as education, NULL as achievements, NULL as certificates, NULL as training FROM " . $this->table_name . " WHERE id = ?";
            }
        }
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return null;
        }
    }

    public function update($id, $username, $email, $full_name, $role, $status, $updated_by = null) {
        $query = "UPDATE " . $this->table_name . " 
                  SET username = ?, email = ?, full_name = ?, role = ?, is_active = ? 
                  WHERE id = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Sanitize
            $username = htmlspecialchars(strip_tags(trim($username)));
            $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
            $full_name = htmlspecialchars(strip_tags(trim($full_name)));
            
            // Convert status to integer
            $is_active = ($status === 'active' || $status == 1) ? 1 : 0;
            
            $stmt->execute([
                $username,
                $email,
                $full_name,
                $role,
                $is_active,
                $id
            ]);
            
            return ['success' => true, 'message' => 'User updated successfully'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function updatePassword($id, $password, $updated_by = null) {
        $query = "UPDATE " . $this->table_name . " SET password = ? WHERE id = ?";
        
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$hashed_password, $id]);
            
            return ['success' => true, 'message' => 'Password updated successfully'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateStatus($id, $status, $updated_by = null) {
        $query = "UPDATE " . $this->table_name . " SET is_active = ? WHERE id = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Convert status to integer
            $is_active = ($status === 'active' || $status == 1) ? 1 : 0;
            
            $stmt->execute([$is_active, $id]);
            
            return ['success' => true, 'message' => 'User status updated successfully'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getStatusBadge($status) {
        // Handle if status is integer or string from is_active
        if ($status === 1 || $status === '1' || $status === 'active') {
            return '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Active</span>';
        } else {
            return '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Suspended</span>';
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            
            return ['success' => true, 'message' => 'User deleted successfully'];
        } catch(PDOException $e) {
            error_log("Error authenticating user: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error'
            ];
        }
    }

    public function getStats() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN role = 'super_admin' THEN 1 ELSE 0 END) as superadmin,
                    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin,
                    SUM(CASE WHEN role = 'guru' THEN 1 ELSE 0 END) as guru,
                    SUM(CASE WHEN role = 'staf' THEN 1 ELSE 0 END) as staf
                  FROM " . $this->table_name;
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [
                'total' => 0,
                'active' => 0,
                'superadmin' => 0,
                'admin' => 0,
                'guru' => 0,
                'staf' => 0,
                'orang_tua' => 0
            ];
        }
    }
}
