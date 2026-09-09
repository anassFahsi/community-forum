<?php
require_once __DIR__ . '/../includes/db.php';

$pdo = getPDO();

$stmt = $pdo->query("SELECT NOW()");
echo "Database connected! Server time: ";
echo $stmt->fetchColumn();

