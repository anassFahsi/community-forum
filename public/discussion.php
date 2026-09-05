<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id = $_SESSION['user_id'];
$discussion_id = $_GET['id'] ?? null;

if (!$discussion_id) {
    echo "<p>Ingen diskussion angiven.</p>";
    exit;
}

$stmt = $pdo->prepare("
    SELECT d.id, d.subject, d.group_id, d.created_at,
           u.first_name, u.last_name
    FROM discussions d
    JOIN users u ON d.created_by = u.id
    WHERE d.id = ?
");
$stmt->execute([$discussion_id]);
$discussion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$discussion) {
    echo "<p>Diskussionen finns inte.</p>";
    exit;
}

$group_id = $discussion['group_id'];

$stmt = $pdo->prepare("
    SELECT id 
    FROM group_members 
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $group_id]);
$membership = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$membership) {
    echo "<p>Du måste vara medlem i gruppen för att se diskussionen.</p>";
    exit;
}


$stmt = $pdo->prepare("
    SELECT p.id, p.content, p.created_at,
           u.first_name, u.last_name
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.discussion_id = ?
    ORDER BY p.created_at ASC
");
$stmt->execute([$discussion_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<?php require_once __DIR__ .'/../includes/header.php' ?>
<div class="discussion-page">

    <h1><?php echo htmlspecialchars($discussion['subject']); ?></h1>

    <p class="discussion-meta">
        Startad av <?php echo htmlspecialchars($discussion['first_name'] . ' ' . $discussion['last_name']); ?>
        den <?php echo htmlspecialchars($discussion['created_at']); ?>
    </p>

    <h2>Inlägg</h2>

    <?php if (count($posts) === 0): ?>
        <p>Inga inlägg ännu.</p>
    <?php else: ?>
        <ul class="post-list">
            <?php foreach ($posts as $p): ?>
                <li class="post-item">
                    <p><?php echo nl2br(htmlspecialchars($p['content'])); ?></p>
                    <p class="post-meta">
                        Av <?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?>
                        den <?php echo htmlspecialchars($p['created_at']); ?>
                    </p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h3>Skriv ett inlägg</h3>

    <form action="../actions/store_post.php" method="POST">
        <input type="hidden" name="discussion_id" value="<?php echo $discussion_id; ?>">

        <textarea name="content" rows="4" cols="50" required></textarea><br><br>

        <button type="submit" class="btn btn-primary">Skicka</button>
    </form>

    <br>
    <a href="group.php?id=<?php echo $group_id; ?>" class="btn btn-secondary">Tillbaka till gruppen</a>

</div>
<?php require_once __DIR__ .'/../includes/footer.php' ?>



