<?php
require 'admin/config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Check if a row exists
$stmt = $conn->query("SELECT id FROM school_settings LIMIT 1");
$row = $stmt->fetch();

$address = 'Perum Kalisemi Indah No.9-11, Parakancanggah, Kec. Banjarnegara, Kab. Banjarnegara, Jawa Tengah 53412';
$phone = '(0286) 5985887';
$ig = 'https://www.instagram.com/sdip_tunasbangsa/?hl=en';
$fb = 'https://www.facebook.com/tunasbangsa.bna/';
$yt = 'https://www.youtube.com/channel/UCbN2M0duhpfnVoF8r_frDVw';

if ($row) {
    $sql = "UPDATE school_settings SET 
            school_address = :address,
            school_phone = :phone,
            instagram_url = :ig,
            facebook_url = :fb,
            youtube_url = :yt
            WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':address' => $address,
        ':phone' => $phone,
        ':ig' => $ig,
        ':fb' => $fb,
        ':yt' => $yt,
        ':id' => $row['id']
    ]);
} else {
    // Insert if no row
    $sql = "INSERT INTO school_settings (school_address, school_phone, instagram_url, facebook_url, youtube_url) 
            VALUES (:address, :phone, :ig, :fb, :yt)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':address' => $address,
        ':phone' => $phone,
        ':ig' => $ig,
        ':fb' => $fb,
        ':yt' => $yt
    ]);
}

echo "Database updated successfully.";
?>
