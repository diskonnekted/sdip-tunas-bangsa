<?php
require 'admin/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->query('SELECT id, username, full_name, role FROM admin_users');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
