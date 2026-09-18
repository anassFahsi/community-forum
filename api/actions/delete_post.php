<?php
session_start();

/* User must be logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id       = $_SESSION['user_id'];
$post_id       = $_GET['id'] ?? null;
$discussion_id = $_GET['discussion_id'] ?? null;

if (!$post_id || !$discussion_id) {
    $message = "Felaktig förfrågan.";
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.id, p.user_id, d.group_id
    FROM posts p
    JOIN discussions d ON p.discussion_id = d.id
    WHERE p.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    $message = "Inlägget finns inte.";
    $backLink = "../public/discussion.php?id=" . $discussion_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

$stmt = $pdo->prepare("
    SELECT role
    FROM group_members
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $post['group_id']]);
$membership = $stmt->fetch(PDO::FETCH_ASSOC);

$is_admin = $membership && $membership['role'] === 'admin';

/* Only admin OR the post owner can delete */
if (!$is_admin && $post['user_id'] != $user_id) {
    $message = "Du har inte behörighet att ta bort detta inlägg.";
    $backLink = "../public/discussion.php?id=" . $discussion_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);

    header("Location: ../public/discussion.php?id=" . $discussion_id);
    exit;

} catch (PDOException $e) {
    $message = "Kunde inte ta bort inlägget.";
    $backLink = "../public/discussion.php?id=" . $discussion_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

