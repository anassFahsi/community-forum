<?php
session_start();

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$token = $_GET['token'] ?? null;

if (!$token) {
    echo "<p>Ingen token angiven.</p>";
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
    echo "<p>Ogiltig inbjudningslänk.</p>";
    exit;
}

/* Already used? */
if ($invite['used']) {
    echo "<p>Denna inbjudningslänk har redan använts.</p>";
    exit;
}

/* Expired? */
if (strtotime($invite['expires_at']) < time()) {
    echo "<p>Inbjudningslänken har gått ut.</p>";
    exit;
}

/* Must be logged in */
if (!isset($_SESSION['user_id'])) {
    echo "<p>Du måste logga in för att använda inbjudningslänken.</p>";
    echo "<a href='../public/login.php'>Logga in</a>";
    exit;
}

$user_id = $_SESSION['user_id'];
$group_id = $invite['group_id'];

/* Already a member? */
$stmt = $pdo->prepare("
    SELECT id FROM group_members
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $group_id]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    echo "<p>Du är redan medlem i gruppen.</p>";
    exit;
}

/* Add member directly */
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

/* Redirect to group page */
header("Location: ../public/group.php?id=" . $group_id);
exit;


