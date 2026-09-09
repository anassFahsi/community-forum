<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id       = $_SESSION['user_id'];
$post_id       = $_GET['id'] ?? null;
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


$stmt = $pdo->prepare("SELECT group_id FROM discussions WHERE id = ?");
$stmt->execute([$discussion_id]);
$discussion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$discussion) {
    echo "Diskussionen finns inte";
    exit;
}

$group_id = $discussion['group_id'];

$stmt = $pdo->prepare("
    SELECT role 
    FROM group_members 
    WHERE group_id = ? AND user_id = ?
");
$stmt->execute([$group_id, $user_id]);
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

<div class="max-w-2xl mx-auto bg-white p-8 rounded shadow mt-16">

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Redigera inlägg</h2>

    <form method="POST" class="space-y-5">

        <textarea name="content" rows="6"
                  class="w-full border border-gray-300 rounded px-3 py-2
                         focus:outline-none focus:ring focus:ring-blue-300
                         break-words overflow-wrap-anywhere"
                  required><?php echo htmlspecialchars($post['content']); ?></textarea>

        <div class="flex gap-4">
            <button type="submit"
                    class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 transition">
                Spara ändringar
            </button>

            <a href="discussion.php?id=<?php echo $discussion_id; ?>"
               class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                Avbryt
            </a>
        </div>

    </form>

</div>

<?php require_once __DIR__.'/../includes/footer.php' ?>


