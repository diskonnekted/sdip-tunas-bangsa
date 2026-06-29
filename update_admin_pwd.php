<?php
require 'admin/config/database.php';
$db = (new Database())->getConnection();
$new_hash = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $db->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
$stmt->execute([$new_hash]);
echo "Password updated successfully to admin123.\n";
?>
