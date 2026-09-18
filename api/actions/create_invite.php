<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id  = $_SESSION['user_id'];
$group_id = $_GET['group_id'] ?? null;

if (!$group_id) {
    $message = "Ingen grupp angiven.";
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
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
    $message = "Endast administratörer kan skapa en inbjudningslänk.";
    $backLink = "../public/group.php?id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

/* Generate token (64 characters) */
$token = bin2hex(random_bytes(32));

$expires_at = date("Y-m-d H:i:s", time() + 86400);

$stmt = $pdo->prepare("
    INSERT INTO invitation_links (group_id, token, expires_at)
    VALUES (?, ?, ?)
");
$stmt->execute([$group_id, $token, $expires_at]);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

$invite_url = $protocol . $host . "/actions/use_invite.php?token=" . $token;

$message = "Inbjudningslänk är skapad!";
$extraHtml = "<a href='$invite_url' class='text-blue-600 underline break-all'>$invite_url</a>";
$backLink = "../public/manage_members.php?group_id=$group_id";

require __DIR__ . "/../includes/message.php";
exit;



