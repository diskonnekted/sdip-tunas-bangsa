<?php
require 'admin/config/database.php';
require 'admin/models/Student.php';
require 'admin/models/ParentModel.php';

$db = new Database();
$conn = $db->getConnection();

// Clear tables first
$conn->exec("SET FOREIGN_KEY_CHECKS = 0");
$conn->exec("TRUNCATE TABLE siswa");
$conn->exec("TRUNCATE TABLE orang_tua");
$conn->exec("TRUNCATE TABLE orang_tua_siswa");
$conn->exec("TRUNCATE TABLE jurnal_7kaih");
$conn->exec("DELETE FROM admin_users WHERE role = 'orang_tua'");
$conn->exec("SET FOREIGN_KEY_CHECKS = 1");

$studentModel = new Student($conn);
$parentModel = new ParentModel($conn);

$dummyData = [
    [
        'student' => [
            'nis' => '23001',
            'nisn' => '0101234567',
            'nama_lengkap' => 'Ahmad Budi Santoso',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2010-05-15',
            'kelas' => '1A'
        ],
        'parent' => [
            'nama_ayah' => 'Budi Raharjo',
            'nama_ibu' => 'Siti Aminah',
            'no_hp_ayah' => '081234567890',
            'no_hp_ibu' => '081234567891',
            'alamat_lengkap' => 'Jl. Merdeka No. 10, Jakarta',
            'status_hubungan' => 'Ayah',
            'password_default' => '0101234567'
        ]
    ],
    [
        'student' => [
            'nis' => '23002',
            'nisn' => '0107654321',
            'nama_lengkap' => 'Siti Nurhaliza',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '2010-08-20',
            'kelas' => '1B'
        ],
        'parent' => [
            'nama_ayah' => 'Ahmad Faisal',
            'nama_ibu' => 'Dewi Lestari',
            'no_hp_ayah' => '082111222333',
            'no_hp_ibu' => '082111222444',
            'alamat_lengkap' => 'Jl. Sudirman No. 45, Jakarta',
            'status_hubungan' => 'Ibu',
            'password_default' => '0107654321'
        ]
    ],
    [
        'student' => [
            'nis' => '22015',
            'nisn' => '0098887776',
            'nama_lengkap' => 'Dimas Anggara',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2009-02-10',
            'kelas' => '2A'
        ],
        'parent' => [
            'nama_ayah' => 'Rudy Heryanto',
            'nama_ibu' => 'Maya Wulan',
            'no_hp_ayah' => '085799887766',
            'no_hp_ibu' => '',
            'alamat_lengkap' => 'Jl. Pahlawan Blok C, Jakarta',
            'status_hubungan' => 'Wali',
            'password_default' => '0098887776'
        ]
    ]
];

$successCount = 0;

foreach ($dummyData as $data) {
    // 1. Create Student
    $sRes = $studentModel->create($data['student']);
    if ($sRes['success']) {
        $studentId = $sRes['id'];
        
        // 2. Create Parent & Link
        $pData = $data['parent'];
        $pData['siswa_id'] = $studentId;
        
        $pRes = $parentModel->create($pData);
        if ($pRes['success']) {
            $successCount++;
            echo "Successfully seeded: " . $data['student']['nama_lengkap'] . " and Parent: " . ($pData['nama_ayah'] ?: $pData['nama_ibu']) . "\n";
        } else {
            echo "Failed Parent: " . $pRes['message'] . "\n";
        }
    } else {
        echo "Failed Student: " . $sRes['message'] . "\n";
    }
}

echo "\nSeeding complete. $successCount dummy records created.\n";
