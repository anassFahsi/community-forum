<?php
require __DIR__ . '/../includes/db.php';

$pdo = getPDO();

$stmt = $pdo->query("SELECT 1");
echo "DB OK";
