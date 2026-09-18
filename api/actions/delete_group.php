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
    $message = "Ingen grupp angiven.";
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, created_by FROM `groups` WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    $message = "Gruppen finns inte.";
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

if ($group['created_by'] != $user_id) {
    $message = "Du har inte behörighet att ta bort denna grupp.";
    $backLink = "../public/group.php?id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM `groups` WHERE id = ?");
    $stmt->execute([$group_id]);

    header("Location: ../public/groups.php?deleted=1");
    exit;

} catch (PDOException $e) {
    $message = "Kunde inte ta bort gruppen.";
    $backLink = "../public/group.php?id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}


