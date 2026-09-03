<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id  = $_SESSION['user_id'];
$group_id = $_GET['group_id'] ?? null;

if (!$group_id) {
    echo "<p>Ingen grupp angiven.</p>";
    exit;
}

$stmt = $pdo->prepare("
    SELECT role 
    FROM group_members 
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $group_id]);
$membership = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$membership || $membership['role'] !== 'admin') {
    echo "<p>Endast administratörer kan skapa inbjudningslänkar.</p>";
    exit;
}

// Skapa token (64 tecken)
$token = bin2hex(random_bytes(32));

// Giltig i 24 timmar
$expires_at = date("Y-m-d H:i:s", time() + 86400);


$stmt = $pdo->prepare("
    INSERT INTO invitation_links (group_id, token, expires_at)
    VALUES (?, ?, ?)
");
$stmt->execute([$group_id, $token, $expires_at]);


$invite_url = "http://localhost/community-forum/public/use_invite.php?token=" . $token;

echo "<p>Inbjudningslänk skapad!</p>";
echo "<p><a href='$invite_url'>$invite_url</a></p>";
echo "<a href='manage_members.php?group_id=$group_id'>Tillbaka</a>";
