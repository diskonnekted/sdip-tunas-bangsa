<?php
require 'admin/config/database.php';

$db = new Database();
$conn = $db->getConnection();

$queries = [
    // 1. Table: siswa
    "CREATE TABLE IF NOT EXISTS siswa (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nis VARCHAR(50) NULL,
        nisn VARCHAR(50) NOT NULL UNIQUE,
        nama_lengkap VARCHAR(100) NOT NULL,
        jenis_kelamin ENUM('L', 'P') NOT NULL,
        tanggal_lahir DATE NULL,
        kelas VARCHAR(50) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    // 2. Table: orang_tua
    "CREATE TABLE IF NOT EXISTS orang_tua (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL, 
        nik VARCHAR(50) NULL,
        nama_ayah VARCHAR(100) NULL,
        nama_ibu VARCHAR(100) NULL,
        no_hp_ayah VARCHAR(20) NULL,
        no_hp_ibu VARCHAR(20) NULL,
        alamat_lengkap TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE SET NULL
    )",

    // 3. Table: orang_tua_siswa (Pivot)
    "CREATE TABLE IF NOT EXISTS orang_tua_siswa (
        id INT AUTO_INCREMENT PRIMARY KEY,
        orang_tua_id INT NOT NULL,
        siswa_id INT NOT NULL,
        status_hubungan ENUM('Ayah', 'Ibu', 'Wali') DEFAULT 'Wali',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (orang_tua_id) REFERENCES orang_tua(id) ON DELETE CASCADE,
        FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
        UNIQUE(orang_tua_id, siswa_id)
    )",

    // 4. Table: jurnal_7kaih
    "CREATE TABLE IF NOT EXISTS jurnal_7kaih (
        id INT AUTO_INCREMENT PRIMARY KEY,
        siswa_id INT NOT NULL,
        tanggal DATE NOT NULL,
        is_bangun_pagi TINYINT(1) DEFAULT 0,
        is_beribadah TINYINT(1) DEFAULT 0,
        is_berolahraga TINYINT(1) DEFAULT 0,
        is_makan_sehat TINYINT(1) DEFAULT 0,
        is_gemar_belajar TINYINT(1) DEFAULT 0,
        is_bermasyarakat TINYINT(1) DEFAULT 0,
        is_tidur_cepat TINYINT(1) DEFAULT 0,
        foto_bukti VARCHAR(255) NULL,
        catatan_orang_tua TEXT NULL,
        created_by INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
        FOREIGN KEY (created_by) REFERENCES orang_tua(id) ON DELETE SET NULL,
        UNIQUE(siswa_id, tanggal)
    )"
];

foreach ($queries as $index => $query) {
    try {
        $conn->exec($query);
        echo "Table " . ($index + 1) . " created successfully.\n";
    } catch (PDOException $e) {
        echo "Error creating table " . ($index + 1) . ": " . $e->getMessage() . "\n";
    }
}
