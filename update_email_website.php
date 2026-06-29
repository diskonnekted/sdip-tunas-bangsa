<?php
require 'admin/config/database.php';
$db = new Database();
$conn = $db->getConnection();

$email = 'info@sdiptunasbangsa.sch.id';
$website = 'https://sdiptunasbangsa.sch.id';

// Check if a row exists
$stmt = $conn->query("SELECT id FROM school_settings LIMIT 1");
$row = $stmt->fetch();

if ($row) {
    $sql = "UPDATE school_settings SET 
            school_email = :email,
            school_website = :website
            WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':email' => $email,
        ':website' => $website,
        ':id' => $row['id']
    ]);
} else {
    // Insert if no row (unlikely since we just created one, but just in case)
    $sql = "INSERT INTO school_settings (school_email, school_website) VALUES (:email, :website)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':email' => $email,
        ':website' => $website
    ]);
}

echo "Database updated successfully.";
?>
