<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id = $_SESSION['user_id'];
$group_id = $_GET['group_id'] ?? null;

if (!$group_id) {
    echo "<p>Ingen grupp angiven.</p>";
    exit;
}

$stmt = $pdo->prepare("SELECT id, name FROM groups WHERE id = ?");
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
$membership = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$membership) {
    echo "<p>Du måste vara medlem i gruppen för att skapa en diskussion.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Skapa diskussion</title>
</head>
<body>

<div class="create-discussion-page">

    <h1>Skapa diskussion i <?php echo htmlspecialchars($group['name']); ?></h1>

    <form action="store_discussion.php" method="POST" class="form">

        <input class="form-label" type="hidden" name="group_id" value="<?php echo $group_id; ?>">

        <label class="form-label" for="subject" class="form-label">Ämne:</label><br>
        <input class="form-input" type="text" id="subject" name="subject" required><br><br>

        <button type="submit" class="btn btn-primary">Skapa</button>
    </form>

    <br>
    <a href="group.php?id=<?php echo $group_id; ?>" class="btn btn-secondary">Tillbaka</a>

</div>

</body>
</html>
