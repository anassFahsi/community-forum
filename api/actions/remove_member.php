<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id   = $_SESSION['user_id'];
$member_id = $_GET['id'] ?? null;      
$group_id  = $_GET['group_id'] ?? null;

if (!$member_id || !$group_id) {
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

/* Verify that the user is an admin */
$stmt = $pdo->prepare("
    SELECT role 
    FROM group_members 
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $group_id]);
$membership = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$membership || $membership['role'] !== 'admin') {
    $message = "Endast administratörer kan ta bort medlemmar.";
    $backLink = "../public/group.php?id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

/* Fetch the member to be removed */
$stmt = $pdo->prepare("
    SELECT id, user_id, role
    FROM group_members
    WHERE id = ? AND group_id = ?
");
$stmt->execute([$member_id, $group_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    $message = "Medlemmen finns inte.";
    $backLink = "../public/manage_members.php?group_id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

/* Admin cannot remove themselves */
if ($member['user_id'] == $user_id) {
    $message = "Du kan inte ta bort dig själv från gruppen.";
    $backLink = "../public/manage_members.php?group_id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

/* Remove member */
$stmt = $pdo->prepare("
    DELETE FROM group_members
    WHERE id = ?
");
$stmt->execute([$member_id]);

header("Location: ../public/manage_members.php?group_id=" . $group_id);
exit;






