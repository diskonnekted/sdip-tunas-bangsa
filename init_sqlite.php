<?php
$db_file = __DIR__ . '/admin/config/database.sqlite';
$sql_file = __DIR__ . '/admin/config/database_setup_sqlite.sql';

if (file_exists($db_file)) {
    unlink($db_file);
}

try {
    $db = new PDO("sqlite:" . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = file_get_contents($sql_file);
    $db->exec($sql);
    echo "SQLite DB created successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
