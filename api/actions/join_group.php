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
    echo "<p>Ingen grupp angiven.</p>";
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM `groups` WHERE id = ?");
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
$existing_member = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_member) {
    header("Location: ../public/group.php?id=" . $group_id);
    exit;
}

/* Check if the user already has a join request */
$stmt = $pdo->prepare("
    SELECT id, status
    FROM group_join_requests
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $group_id]);
$existing_request = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_request) {
    echo "<p>Du har redan en ansökan med status: " . htmlspecialchars($existing_request['status']) . "</p>";
    echo '<a href="../public/groups.php">Tillbaka</a>';
    exit;
}

/* Create a new join request */
$stmt = $pdo->prepare("
    INSERT INTO group_join_requests (user_id, group_id, status)
    VALUES (?, ?, 'pending')
");
$stmt->execute([$user_id, $group_id]);

echo "<p>Din ansökan har skickats!</p>";
echo '<a href="../public/groups.php">Tillbaka</a>';
exit;


