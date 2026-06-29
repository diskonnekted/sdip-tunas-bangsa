<?php
require 'admin/config/database.php';
$db = (new Database())->getConnection();

function showSchema($db, $table) {
    try {
        $stmt = $db->query("DESCRIBE $table");
        $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Table: $table\n";
        foreach($cols as $c) {
            echo "  " . $c['Field'] . " - " . $c['Type'] . "\n";
        }
    } catch(Exception $e) {
        echo "Table $table does not exist.\n";
    }
}

showSchema($db, 'admin_users');
showSchema($db, 'teacher_profiles');
showSchema($db, 'staff_profiles');
