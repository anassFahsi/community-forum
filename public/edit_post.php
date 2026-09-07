<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$post_id = $_GET['id'] ?? null;
$discussion_id = $_GET['discussion_id'] ?? null;

if (!$post_id || !$discussion_id) {
    echo "Felaktig förfrågan";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "Inlägget finns inte";
    exit;
}

$stmt = $pdo->prepare("
    SELECT role 
    FROM group_members 
    WHERE group_id = ? AND user_id = ?
");
$stmt->execute([$post['discussion_id'], $user_id]);
$membership = $stmt->fetch(PDO::FETCH_ASSOC);

$is_admin = $membership && $membership['role'] === 'admin';

if ($post['user_id'] !== $user_id && !$is_admin) {
    echo "Du har inte behörighet att redigera detta inlägg";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);

    if ($content === "") {
        echo "Innehållet får inte vara tomt.";
        exit;
    }

    $stmt = $pdo->prepare("UPDATE posts SET content = ? WHERE id = ?");
    $stmt->execute([$content, $post_id]);

    header("Location: discussion.php?id=" . $discussion_id);
    exit;
}
?>

<?php require_once __DIR__.'/../includes/header.php' ?>

<h2>Redigera inlägg</h2>

<form method="POST">
    <textarea name="content" rows="6" style="width:100%;"><?php echo htmlspecialchars($post['content']); ?></textarea>
    <br><br>
    <button type="submit" class="btn btn-secondary">Spara ändringar</button>
    <a href="discussion.php?id=<?php echo $discussion_id; ?>" class="btn-danger">Avbryt</a>
</form>

<?php require_once __DIR__.'/../includes/footer.php' ?>
