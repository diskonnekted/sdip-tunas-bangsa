<?php
class Jurnal7Kaih {
    private $conn;
    private $table_name = "jurnal_7kaih";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getJurnalSiswa($siswa_id, $bulan = null, $tahun = null) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE siswa_id = ?";
        $params = [$siswa_id];
        
        if ($bulan && $tahun) {
            $query .= " AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?";
            $params[] = $bulan;
            $params[] = $tahun;
        }
        
        $query .= " ORDER BY tanggal DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    public function getByTanggal($siswa_id, $tanggal) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE siswa_id = ? AND tanggal = ?";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$siswa_id, $tanggal]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function simpanJurnal($data) {
        // Check if exists
        $existing = $this->getByTanggal($data['siswa_id'], $data['tanggal']);
        
        if ($existing) {
            $query = "UPDATE " . $this->table_name . " 
                      SET is_bangun_pagi = :is_bangun_pagi,
                          is_beribadah = :is_beribadah,
                          is_berolahraga = :is_berolahraga,
                          is_makan_sehat = :is_makan_sehat,
                          is_gemar_belajar = :is_gemar_belajar,
                          is_bermasyarakat = :is_bermasyarakat,
                          is_tidur_cepat = :is_tidur_cepat,
                          foto_bukti = COALESCE(:foto_bukti, foto_bukti),
                          catatan_orang_tua = :catatan,
                          created_by = :created_by
                      WHERE id = :id";
            $params = [
                ':id' => $existing['id'],
                ':is_bangun_pagi' => $data['is_bangun_pagi'] ?? 0,
                ':is_beribadah' => $data['is_beribadah'] ?? 0,
                ':is_berolahraga' => $data['is_berolahraga'] ?? 0,
                ':is_makan_sehat' => $data['is_makan_sehat'] ?? 0,
                ':is_gemar_belajar' => $data['is_gemar_belajar'] ?? 0,
                ':is_bermasyarakat' => $data['is_bermasyarakat'] ?? 0,
                ':is_tidur_cepat' => $data['is_tidur_cepat'] ?? 0,
                ':foto_bukti' => $data['foto_bukti'] ?? null,
                ':catatan' => $data['catatan'] ?? null,
                ':created_by' => $data['created_by'] ?? null
            ];
        } else {
            $query = "INSERT INTO " . $this->table_name . " 
                      (siswa_id, tanggal, is_bangun_pagi, is_beribadah, is_berolahraga, is_makan_sehat, is_gemar_belajar, is_bermasyarakat, is_tidur_cepat, foto_bukti, catatan_orang_tua, created_by)
                      VALUES 
                      (:siswa_id, :tanggal, :is_bangun_pagi, :is_beribadah, :is_berolahraga, :is_makan_sehat, :is_gemar_belajar, :is_bermasyarakat, :is_tidur_cepat, :foto_bukti, :catatan, :created_by)";
            $params = [
                ':siswa_id' => $data['siswa_id'],
                ':tanggal' => $data['tanggal'],
                ':is_bangun_pagi' => $data['is_bangun_pagi'] ?? 0,
                ':is_beribadah' => $data['is_beribadah'] ?? 0,
                ':is_berolahraga' => $data['is_berolahraga'] ?? 0,
                ':is_makan_sehat' => $data['is_makan_sehat'] ?? 0,
                ':is_gemar_belajar' => $data['is_gemar_belajar'] ?? 0,
                ':is_bermasyarakat' => $data['is_bermasyarakat'] ?? 0,
                ':is_tidur_cepat' => $data['is_tidur_cepat'] ?? 0,
                ':foto_bukti' => $data['foto_bukti'] ?? null,
                ':catatan' => $data['catatan'] ?? null,
                ':created_by' => $data['created_by'] ?? null
            ];
        }

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return [
                'success' => true,
                'message' => 'Jurnal berhasil disimpan.'
            ];
        } catch(PDOException $e) {
            error_log("Error saving jurnal: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal menyimpan jurnal.'
            ];
        }
    }
}
