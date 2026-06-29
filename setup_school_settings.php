<?php
$db = new PDO('sqlite:admin/config/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE IF NOT EXISTS school_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    school_name TEXT,
    school_motto TEXT,
    school_description TEXT,
    school_logo TEXT,
    school_address TEXT,
    school_phone TEXT,
    school_email TEXT,
    school_website TEXT,
    school_latitude TEXT,
    school_longitude TEXT,
    principal_name TEXT,
    principal_photo TEXT,
    established_year TEXT,
    npsn TEXT,
    accreditation TEXT,
    facebook_url TEXT,
    instagram_url TEXT,
    youtube_url TEXT,
    twitter_url TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)');
$stmt = $db->query('SELECT COUNT(*) FROM school_settings');
$count = $stmt->fetchColumn();
if ($count == 0) {
    $db->exec("INSERT INTO school_settings (school_name, school_logo, accreditation) VALUES ('SDIP Tunas Bangsa', 'logo.png', 'A')");
} else {
    $db->exec("UPDATE school_settings SET school_logo = 'logo.png'");
}
echo 'Table created and logo set!';
?>
