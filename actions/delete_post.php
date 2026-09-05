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
    echo "<p>Felaktig förfrågan.</p>";
    exit;
}

/* Fetch post */
$stmt = $pdo->prepare("
    SELECT p.id, p.user_id, d.group_id
    FROM posts p
    JOIN discussions d ON p.discussion_id = d.id
    WHERE p.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "<p>Inlägget finns inte.</p>";
    exit;
}

/* Check if user is admin in the group */
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
    echo "<p>Du har inte behörighet att ta bort detta inlägg.</p>";
    exit;
}

/* Delete post */
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$post_id]);

/* Redirect back to discussion */
header("Location: ../public/discussion.php?id=" . $discussion_id);
exit;
