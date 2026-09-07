<?php


session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id  = $_SESSION['user_id'];
$group_id = $_GET['group_id'] ?? null;

if (!$group_id) {
    echo "Ingen grupp angiven.";
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, created_by FROM groups WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    echo "Gruppen finns inte.";
    exit;
}

if ($group['created_by'] != $user_id) {
    echo "Du har inte behörighet att ta bort denna grupp.";
    exit;
}

$stmt = $pdo->prepare("DELETE FROM groups WHERE id = ?");
$stmt->execute([$group_id]);

header("Location: ../public/groups.php?deleted=1");
exit;

