<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id = $_SESSION['user_id'];

$discussion_id = $_POST['discussion_id'] ?? null;
$content = trim($_POST['content'] ?? '');

$errors = [];

if (!$discussion_id) {
    $errors[] = "Ingen diskussion angiven.";
}

if ($content === '') {
    $errors[] = "Inlägget får inte vara tomt.";
}

if (!empty($errors)) {
    foreach ($errors as $e) {
        echo "<p>$e</p>";
    }
    echo '<a href="../public/discussion.php?id=' . htmlspecialchars($discussion_id) . '">Tillbaka</a>';
    exit;
}

/* Fetch discussion */
$stmt = $pdo->prepare("
    SELECT id, group_id 
    FROM discussions 
    WHERE id = ?
");
$stmt->execute([$discussion_id]);
$discussion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$discussion) {
    echo "<p>Diskussionen finns inte.</p>";
    exit;
}

$group_id = $discussion['group_id'];

/* Check if the user is a member */
$stmt = $pdo->prepare("
    SELECT id 
    FROM group_members 
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $group_id]);
$membership = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$membership) {
    echo "<p>Du måste vara medlem i gruppen för att skriva ett inlägg.</p>";
    exit;
}

/* Insert post */
$stmt = $pdo->prepare("
    INSERT INTO posts (discussion_id, user_id, content)
    VALUES (?, ?, ?)
");
$stmt->execute([$discussion_id, $user_id, $content]);

header("Location: ../public/discussion.php?id=" . $discussion_id);
exit;
