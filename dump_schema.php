<?php
$sqlite = new PDO("sqlite:admin/config/database.sqlite");
$sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $sqlite->query("SELECT sql FROM sqlite_master WHERE type='table' AND name != 'sqlite_sequence'");
$schemas = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach($schemas as $sql) {
    echo $sql . ";\n\n";
}
?>
