<?php
require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

try {
    $pdo->query("INSERT INTO `groups` (id,name, description, created_by) VALUES (50,'Test', 'Test', 1)");
    echo "INSERT OK";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
