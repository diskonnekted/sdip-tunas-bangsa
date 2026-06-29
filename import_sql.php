<?php
try {
    $conn = new PDO("mysql:host=localhost", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = file_get_contents(__DIR__ . '/admin/config/database_setup.sql');
    $conn->exec($sql);
    echo "SQL import successful.\n";
} catch(PDOException $e) {
    echo "Import failed: " . $e->getMessage();
}
?>
