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
    echo "<p>Felaktig förfrågan.</p>";
    exit;
}

/* Check if the group exists */
$stmt = $pdo->prepare("SELECT id, name FROM groups WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    echo "<p>Gruppen finns inte.</p>";
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
    echo "<p>Endast administratörer kan ta bort medlemmar.</p>";
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
    echo "<p>Medlemmen finns inte.</p>";
    exit;
}

/* Admin cannot remove themselves */
if ($member['user_id'] == $user_id) {
    echo "<p>Du kan inte ta bort dig själv från gruppen.</p>";
    exit;
}

/* Remove the member */
$stmt = $pdo->prepare("
    DELETE FROM group_members
    WHERE id = ?
");
$stmt->execute([$member_id]);

/* Redirect back to member management */
header("Location: ../public/manage_members.php?group_id=" . $group_id);
exit;





