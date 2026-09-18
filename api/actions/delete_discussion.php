<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('location: ../public/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$group_id = $_GET['group_id'] ?? null;
$discussion_id = $_GET['id'] ?? null;

if (!$discussion_id || !$group_id) {
    $message = "Felaktig förfrågan.";
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM discussions WHERE group_id=? AND id=?");
$stmt->execute([$group_id, $discussion_id]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$existing) {
    $message = "Diskussionen finns inte.";
    $backLink = "../public/group.php?id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

$stmt = $pdo->prepare("SELECT role FROM group_members WHERE group_id=? AND user_id=?");
$stmt->execute([$group_id, $user_id]);
$membership = $stmt->fetch(PDO::FETCH_ASSOC);

$is_admin = $membership && $membership['role'] === 'admin';

if (!$is_admin && $existing['created_by'] !== $user_id) {
    $message = "Du har inte behörighet att ta bort diskussionen.";
    $backLink = "../public/group.php?id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM discussions WHERE id=?");
    $stmt->execute([$discussion_id]);

    header("location: ../public/group.php?id=$group_id");
    exit;

} catch (PDOException $e) {
    $message = "Kunde inte ta bort diskussionen.";
    $backLink = "../public/group.php?id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}


