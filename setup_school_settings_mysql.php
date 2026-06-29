<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=sd_integra_iv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE TABLE IF NOT EXISTS school_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
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
    );";
    $conn->exec($sql);
    
    // Insert initial logo setting if empty
    $stmt = $conn->query("SELECT COUNT(*) FROM school_settings");
    if ($stmt->fetchColumn() == 0) {
        $conn->exec("INSERT INTO school_settings (school_name, school_logo) VALUES ('SDIP Tunas Bangsa', 'logo.png')");
    }
    
    echo "school_settings table created successfully.\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
