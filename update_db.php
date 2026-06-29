<?php
require 'admin/config/database.php';
$db = (new Database())->getConnection();

// Check if school_address exists
$stmt = $db->prepare("SELECT id FROM school_settings WHERE setting_key = 'school_address'");
$stmt->execute();
if ($stmt->fetch()) {
    $db->query("UPDATE school_settings SET setting_value = 'Perum Kalisemi Indah No.9-11, Parakancanggah, Kec. Banjarnegara, Kab. Banjarnegara, Jawa Tengah 53412' WHERE setting_key = 'school_address'");
} else {
    $db->query("INSERT INTO school_settings (setting_key, setting_value, setting_type, description) VALUES ('school_address', 'Perum Kalisemi Indah No.9-11, Parakancanggah, Kec. Banjarnegara, Kab. Banjarnegara, Jawa Tengah 53412', 'text', 'Alamat sekolah')");
}

// Check if school_phone exists
$stmt = $db->prepare("SELECT id FROM school_settings WHERE setting_key = 'school_phone'");
$stmt->execute();
if ($stmt->fetch()) {
    $db->query("UPDATE school_settings SET setting_value = '(0286) 5985887' WHERE setting_key = 'school_phone'");
} else {
    $db->query("INSERT INTO school_settings (setting_key, setting_value, setting_type, description) VALUES ('school_phone', '(0286) 5985887', 'text', 'Nomor telepon sekolah')");
}

echo "Database updated successfully.";
?>
