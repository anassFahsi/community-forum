<?php
session_start();

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$token = $_GET['token'] ?? null;

if (!$token) {
    $message = "Ingen token angiven.";
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

/* Fetch invitation */
$stmt = $pdo->prepare("
    SELECT id, group_id, expires_at, used
    FROM invitation_links
    WHERE token = ?
");
$stmt->execute([$token]);
$invite = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invite) {
    $message = "Ogiltig inbjudningslänk.";
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

/* Already used? */
if ($invite['used']) {
    $message = "Denna inbjudningslänk har redan använts.";
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

/* Expired? */
if (strtotime($invite['expires_at']) < time()) {
    $message = "Inbjudningslänken har gått ut.";
    $backLink = "../public/groups.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

/* Must be logged in */
if (!isset($_SESSION['user_id'])) {
    $message = "Du måste logga in för att använda inbjudningslänken.";
    $backLink = "../public/login.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

$user_id  = $_SESSION['user_id'];
$group_id = $invite['group_id'];

/* Already member? */
$stmt = $pdo->prepare("
    SELECT id FROM group_members
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $group_id]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    $message = "Du är redan medlem i gruppen.";
    $backLink = "../public/group.php?id=" . $group_id;
    require __DIR__ . "/../includes/message.php";
    exit;
}

/* Add member */
$stmt = $pdo->prepare("
    INSERT INTO group_members (user_id, group_id, role)
    VALUES (?, ?, 'member')
");
$stmt->execute([$user_id, $group_id]);

/* Mark invitation as used */
$stmt = $pdo->prepare("
    UPDATE invitation_links
    SET used = TRUE
    WHERE id = ?
");
$stmt->execute([$invite['id']]);

header("Location: ../public/group.php?id=" . $group_id);
exit;


