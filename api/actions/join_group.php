<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id  = $_SESSION['user_id'];
$group_id = $_GET['id'] ?? null;

if (!$group_id) {
    $message = "Ingen grupp angiven.";
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM `groups` WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    $message = "Gruppen finns inte.";
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

$stmt = $pdo->prepare("
    SELECT id 
    FROM group_members 
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $group_id]);
$existing_member = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_member) {
    $message = "Du är redan medlem i denna grupp.";
    $backLink = "../public/group.php?id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, status
    FROM group_join_requests
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $group_id]);
$existing_request = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_request) {
    $message = "Du har redan en ansökan med status: " . htmlspecialchars($existing_request['status']);
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO group_join_requests (user_id, group_id, status)
    VALUES (?, ?, 'pending')
");
$stmt->execute([$user_id, $group_id]);

$message = "Din ansökan har skickats!";
$backLink = "../public/groups.php";
require __DIR__ . "/../includes/message.php";
exit;




