<?php
class ParentModel {
    private $conn;
    private $table_name = "orang_tua";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT ot.*, au.username, au.email 
                  FROM " . $this->table_name . " ot
                  LEFT JOIN admin_users au ON ot.user_id = au.id
                  ORDER BY ot.nama_ayah ASC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting parents: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        $query = "SELECT ot.*, au.username, au.email 
                  FROM " . $this->table_name . " ot
                  LEFT JOIN admin_users au ON ot.user_id = au.id
                  WHERE ot.id = ?";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting parent: " . $e->getMessage());
            return false;
        }
    }

    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function create($data) {
        // Create user account first
        try {
            $this->conn->beginTransaction();

            $username = $data['no_hp_ayah'] ?: $data['no_hp_ibu']; // Default to father's phone or mother's
            if (empty($username)) {
                throw new Exception("Nomor HP wajib diisi untuk login.");
            }

            // Using NISN as password if provided, otherwise default
            $password = !empty($data['password_default']) ? password_hash($data['password_default'], PASSWORD_DEFAULT) : password_hash('123456', PASSWORD_DEFAULT);
            $nama = $data['nama_ayah'] ?: $data['nama_ibu'];

            $dummyEmail = 'parent_' . uniqid() . '@example.com';
            $userQuery = "INSERT INTO admin_users (username, email, password, full_name, role, is_active) 
                          VALUES (:username, :email, :password, :full_name, 'orang_tua', 1)";
            $userStmt = $this->conn->prepare($userQuery);
            $userStmt->execute([
                ':username' => $username,
                ':email' => $dummyEmail,
                ':password' => $password,
                ':full_name' => $nama
            ]);
            
            $userId = $this->conn->lastInsertId();

            // Create parent record
            $query = "INSERT INTO " . $this->table_name . " 
                     (user_id, nik, nama_ayah, nama_ibu, no_hp_ayah, no_hp_ibu, alamat_lengkap) 
                     VALUES (:user_id, :nik, :nama_ayah, :nama_ibu, :no_hp_ayah, :no_hp_ibu, :alamat_lengkap)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':user_id' => $userId,
                ':nik' => $data['nik'] ?? null,
                ':nama_ayah' => $data['nama_ayah'] ?? null,
                ':nama_ibu' => $data['nama_ibu'] ?? null,
                ':no_hp_ayah' => $data['no_hp_ayah'] ?? null,
                ':no_hp_ibu' => $data['no_hp_ibu'] ?? null,
                ':alamat_lengkap' => $data['alamat_lengkap'] ?? null
            ]);

            $parentId = $this->conn->lastInsertId();

            // Link to student if student_id provided
            if (!empty($data['siswa_id'])) {
                $pivotQuery = "INSERT INTO orang_tua_siswa (orang_tua_id, siswa_id, status_hubungan) 
                               VALUES (:parent_id, :student_id, :status)";
                $pivotStmt = $this->conn->prepare($pivotQuery);
                $pivotStmt->execute([
                    ':parent_id' => $parentId,
                    ':student_id' => $data['siswa_id'],
                    ':status' => $data['status_hubungan'] ?? 'Wali'
                ]);
            }

            $this->conn->commit();

            return [
                'success' => true,
                'id' => $parentId,
                'message' => 'Data orang tua berhasil ditambahkan dan akun berhasil dibuat.'
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error creating parent: " . $e->getMessage());
            $msg = 'Gagal menambahkan data orang tua.';
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $msg = 'Nomor HP sudah terdaftar sebagai pengguna.';
            }
            return [
                'success' => false,
                'message' => $msg
            ];
        }
    }

    public function getChildren($parent_id) {
        $query = "SELECT s.*, ots.status_hubungan 
                  FROM siswa s
                  JOIN orang_tua_siswa ots ON s.id = ots.siswa_id
                  WHERE ots.orang_tua_id = ?";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$parent_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    public function getParentsByStudent($student_id) {
        $query = "SELECT ot.*, ots.status_hubungan, au.username 
                  FROM " . $this->table_name . " ot
                  JOIN orang_tua_siswa ots ON ot.id = ots.orang_tua_id
                  LEFT JOIN admin_users au ON ot.user_id = au.id
                  WHERE ots.siswa_id = ?";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$student_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    public function linkStudent($parent_id, $student_id, $status = 'Wali') {
        $query = "INSERT IGNORE INTO orang_tua_siswa (orang_tua_id, siswa_id, status_hubungan) 
                  VALUES (?, ?, ?)";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$parent_id, $student_id, $status]);
            return ['success' => true];
        } catch(PDOException $e) {
            return ['success' => false];
        }
    }
}
