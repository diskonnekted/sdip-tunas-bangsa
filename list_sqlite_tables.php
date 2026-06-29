<?php
$sqlite = new PDO("sqlite:admin/config/database.sqlite");
$sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table'");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Tables in SQLite: \n";
foreach($tables as $table) {
    if ($table == 'sqlite_sequence') continue;
    echo "- $table\n";
}
?>
