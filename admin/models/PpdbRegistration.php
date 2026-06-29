<?php
class PpdbRegistration {
    private $conn;
    private $table_name = "ppdb_registrations";

    public function __construct($db) {
        $this->conn = $db;
        $this->createTable();
    }

    private function createTable() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            registration_number VARCHAR(50) NOT NULL UNIQUE,
            child_name VARCHAR(100) NOT NULL,
            dob DATE NOT NULL,
            gender ENUM('L', 'P') NOT NULL,
            previous_school VARCHAR(100),
            parent_name VARCHAR(100) NOT NULL,
            parent_phone VARCHAR(20) NOT NULL,
            email VARCHAR(150),
            address TEXT NOT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_created (created_at),
            INDEX idx_reg_num (registration_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        try {
            $this->conn->exec($query);
            
            // Migration check for potentially missing columns if table already existed
            $this->checkAndAddColumn('gender', "ENUM('L', 'P') NOT NULL DEFAULT 'L'");
            $this->checkAndAddColumn('previous_school', "VARCHAR(100)");
            
        } catch(PDOException $e) {
            error_log("Error creating/updating ppdb_registrations table: " . $e->getMessage());
        }
    }

    private function checkAndAddColumn($column, $definition) {
        try {
            $check = $this->conn->query("SHOW COLUMNS FROM " . $this->table_name . " LIKE '$column'");
            if ($check->rowCount() == 0) {
                $this->conn->exec("ALTER TABLE " . $this->table_name . " ADD COLUMN $column $definition");
            }
        } catch(PDOException $e) {
            // Ignore error
        }
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (registration_number, child_name, dob, gender, previous_school, parent_name, parent_phone, email, address) 
                  VALUES (:reg_num, :child_name, :dob, :gender, :prev_school, :parent_name, :phone, :email, :address)";

        try {
            $stmt = $this->conn->prepare($query);
            
            // Generate Registration Number (e.g., REG-YYYYMMDD-XXXX)
            $reg_num = 'REG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            
            // Sanitize and bind
            $stmt->bindValue(':reg_num', $reg_num);
            $stmt->bindValue(':child_name', htmlspecialchars(strip_tags($data['child_name'])));
            $stmt->bindValue(':dob', $data['dob']);
            $stmt->bindValue(':gender', $data['gender']);
            $stmt->bindValue(':prev_school', htmlspecialchars(strip_tags($data['previous_school'])));
            $stmt->bindValue(':parent_name', htmlspecialchars(strip_tags($data['parent_name'])));
            $stmt->bindValue(':phone', htmlspecialchars(strip_tags($data['parent_phone'])));
            $stmt->bindValue(':email', filter_var($data['email'], FILTER_SANITIZE_EMAIL));
            $stmt->bindValue(':address', htmlspecialchars(strip_tags($data['address'])));

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'registration_number' => $reg_num,
                    'message' => 'Pendaftaran berhasil'
                ];
            }
            return ['success' => false, 'message' => 'Gagal menyimpan data'];

        } catch(PDOException $e) {
            error_log("Error creating ppdb registration: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getAll($status = '', $limit = 50, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name;
        
        if ($status) {
            $query .= " WHERE status = :status";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        try {
            $stmt = $this->conn->prepare($query);
            if ($status) {
                $stmt->bindParam(':status', $status);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    public function count($status = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        if ($status) {
            $query .= " WHERE status = :status";
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            if ($status) {
                $stmt->bindParam(':status', $status);
            }
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch(PDOException $e) {
            return 0;
        }
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getStats() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
                  FROM " . $this->table_name;
        
        try {
            $stmt = $this->conn->query($query);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
        }
    }
}
?>