<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id  = $_SESSION['user_id'];
$group_id = $_POST['group_id'] ?? null;
$subject  = trim($_POST['subject'] ?? '');

$errors = [];

if (!$group_id) {
    $errors[] = "Ingen grupp angiven.";
}

if ($subject === '') {
    $errors[] = "Ämne är obligatoriskt.";
}

if (!empty($errors)) {
    $message = implode("<br>", $errors);
    $backLink = "../public/create_discussion.php?group_id=" . htmlspecialchars($group_id);
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
$membership = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$membership) {
    $message = "Du måste vara medlem i gruppen för att skapa en diskussion.";
    $backLink = "../public/group.php?id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO discussions (group_id, subject, created_by)
    VALUES (?, ?, ?)
");
$stmt->execute([$group_id, $subject, $user_id]);

$discussion_id = $pdo->lastInsertId();

header("Location: ../public/discussion.php?id=" . $discussion_id);
exit;


