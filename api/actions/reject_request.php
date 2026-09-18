<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id    = $_SESSION['user_id'];
$request_id = $_GET['id'] ?? null;
$group_id   = $_GET['group_id'] ?? null;

if (!$request_id || !$group_id) {
    $message = "Felaktig förfrågan.";
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

$stmt = $pdo->prepare("SELECT id, name FROM `groups` WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    $message = "Gruppen finns inte.";
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

/* Verify admin */
$stmt = $pdo->prepare("
    SELECT role 
    FROM group_members 
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $group_id]);
$membership = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$membership || $membership['role'] !== 'admin') {
    $message = "Endast administratörer kan neka ansökningar.";
    $backLink = "../public/group.php?id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

/* Fetch request */
$stmt = $pdo->prepare("
    SELECT id, user_id, group_id, status
    FROM group_join_requests
    WHERE id = ? AND group_id = ?
");
$stmt->execute([$request_id, $group_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    $message = "Ansökan finns inte.";
    $backLink = "../public/manage_members.php?group_id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

if ($request['status'] !== 'pending') {
    $message = "Ansökan är redan hanterad.";
    $backLink = "../public/manage_members.php?group_id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

/* Reject request */
$stmt = $pdo->prepare("
    UPDATE group_join_requests
    SET status = 'rejected'
    WHERE id = ?
");
$stmt->execute([$request_id]);

header("Location: ../public/manage_members.php?group_id=" . $group_id);
exit;


