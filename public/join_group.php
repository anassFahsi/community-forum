<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id = $_SESSION['user_id'];
$group_id = $_GET['id'] ?? null;

if (!$group_id) {
    echo "<p>Ingen grupp angiven.</p>";
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM groups WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    echo "<p>Gruppen finns inte.</p>";
    exit;
}

$stmt = $pdo->prepare("
    SELECT id 
    FROM group_members 
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $group_id]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    header("Location: group.php?id=" . $group_id);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO group_members (user_id, group_id, role)
    VALUES (?, ?, 'member')
");
$stmt->execute([$user_id, $group_id]);

header('Location: group.php?id=' . $group_id);
exit;
