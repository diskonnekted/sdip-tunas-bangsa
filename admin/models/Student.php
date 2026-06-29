<?php
class Student {
    private $conn;
    private $table_name = "siswa";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($search = '') {
        $query = "SELECT * FROM " . $this->table_name;
        $params = [];
        
        if (!empty($search)) {
            $query .= " WHERE nama_lengkap LIKE ? OR nis LIKE ? OR nisn LIKE ?";
            $search_param = "%$search%";
            $params = [$search_param, $search_param, $search_param];
        }
        
        $query .= " ORDER BY nama_lengkap ASC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting students: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting student: " . $e->getMessage());
            return false;
        }
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (nis, nisn, nama_lengkap, jenis_kelamin, tanggal_lahir, kelas) 
                 VALUES (:nis, :nisn, :nama_lengkap, :jenis_kelamin, :tanggal_lahir, :kelas)";
                 
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':nis' => $data['nis'] ?? null,
                ':nisn' => $data['nisn'],
                ':nama_lengkap' => $data['nama_lengkap'],
                ':jenis_kelamin' => $data['jenis_kelamin'],
                ':tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                ':kelas' => $data['kelas'] ?? null
            ]);
            
            return [
                'success' => true,
                'id' => $this->conn->lastInsertId(),
                'message' => 'Data siswa berhasil ditambahkan'
            ];
        } catch(PDOException $e) {
            error_log("Error creating student: " . $e->getMessage());
            $message = 'Gagal menambahkan data siswa.';
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $message = 'NISN sudah terdaftar.';
            }
            return [
                'success' => false,
                'message' => $message
            ];
        }
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                 SET nis = :nis, nisn = :nisn, nama_lengkap = :nama_lengkap, 
                     jenis_kelamin = :jenis_kelamin, tanggal_lahir = :tanggal_lahir, kelas = :kelas 
                 WHERE id = :id";
                 
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':id' => $id,
                ':nis' => $data['nis'] ?? null,
                ':nisn' => $data['nisn'],
                ':nama_lengkap' => $data['nama_lengkap'],
                ':jenis_kelamin' => $data['jenis_kelamin'],
                ':tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                ':kelas' => $data['kelas'] ?? null
            ]);
            
            return [
                'success' => true,
                'message' => 'Data siswa berhasil diupdate'
            ];
        } catch(PDOException $e) {
            error_log("Error updating student: " . $e->getMessage());
            $message = 'Gagal mengupdate data siswa.';
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $message = 'NISN sudah terdaftar pada siswa lain.';
            }
            return [
                'success' => false,
                'message' => $message
            ];
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return [
                'success' => true,
                'message' => 'Data siswa berhasil dihapus'
            ];
        } catch(PDOException $e) {
            error_log("Error deleting student: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal menghapus data siswa'
            ];
        }
    }
}
