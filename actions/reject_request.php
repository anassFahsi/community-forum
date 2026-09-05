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
    echo "<p>Endast administratörer kan neka ansökningar.</p>";
    exit;
}

/* Fetch the join request */
$stmt = $pdo->prepare("
    SELECT id, user_id, group_id, status
    FROM group_join_requests
    WHERE id = ? AND group_id = ?
");
$stmt->execute([$request_id, $group_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    echo "<p>Ansökan finns inte.</p>";
    exit;
}

/* Request already processed? */
if ($request['status'] !== 'pending') {
    echo "<p>Ansökan är redan hanterad.</p>";
    exit;
}

/* Update request status to rejected */
$stmt = $pdo->prepare("
    UPDATE group_join_requests
    SET status = 'rejected'
    WHERE id = ?
");
$stmt->execute([$request_id]);

/* Redirect back to member management */
header("Location: ../public/manage_members.php?group_id=" . $group_id);
exit;

