<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

// Kontrollera att gruppen finns
$stmt = $pdo->prepare("SELECT id, name FROM groups WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    echo "<p>Gruppen finns inte.</p>";
    exit;
}

// Kontrollera att användaren är admin
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

// Hämta ansökan
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

if ($request['status'] !== 'pending') {
    echo "<p>Ansökan är redan hanterad.</p>";
    exit;
}

// Uppdatera ansökan till rejected
$stmt = $pdo->prepare("
    UPDATE group_join_requests
    SET status = 'rejected'
    WHERE id = ?
");
$stmt->execute([$request_id]);


header("Location: manage_members.php?group_id=" . $group_id);
exit;
