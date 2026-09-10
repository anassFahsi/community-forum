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
    SELECT role
    FROM group_members
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $group_id]);
$membership = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$membership) {
    echo "<p>Du måste vara medlem i gruppen för att se diskussionen.</p>";
    exit;
}

$is_admin = $membership && $membership['role'] === 'admin';

$stmt = $pdo->prepare("
    SELECT p.id, p.content, p.created_at, p.user_id,
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

<div class="max-w-4xl mx-auto mt-10">

    <h1 class="text-3xl font-bold text-gray-800 mb-2">
        <?php echo htmlspecialchars($discussion['subject']); ?>
    </h1>

    <p class="text-gray-600 mb-6">
        Startad av <?php echo htmlspecialchars($discussion['first_name'] . ' ' . $discussion['last_name']); ?>
        den <?php echo htmlspecialchars($discussion['created_at']); ?>
    </p>

    <h2 class="text-xl font-semibold text-gray-700 mb-4">Inlägg</h2>

    <?php if (count($posts) === 0): ?>
        <p class="text-gray-600 mb-6">Inga inlägg ännu.</p>
    <?php else: ?>
        <ul class="space-y-4 mb-10">
            <?php foreach ($posts as $p): ?>
                <li class="bg-white p-4 rounded shadow">

                    <p class="mt-0 text-gray-800 whitespace-pre-line break-words overflow-wrap-anywhere">

                        <?php echo nl2br(htmlspecialchars($p['content'])); ?>
                    </p>

                    <p class="text-gray-600 mt-2 mb-4">
                        Av <?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?>
                        den <?php echo htmlspecialchars($p['created_at']); ?>
                    </p>

                    <?php if ($p['user_id'] == $user_id || $is_admin): ?>

                        <a href="../public/edit_post.php?id=<?php echo $p['id']; ?>&discussion_id=<?php echo $discussion_id; ?>"
                           class="inline-block bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 transition mr-2">
                           Redigera
                        </a>

                        <a href="#"
                           class="inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition"
                           onclick="openDeleteModal('../actions/delete_post.php?id=<?php echo $p['id']; ?>&discussion_id=<?php echo $discussion_id; ?>'); return false;">
                           Ta bort
                        </a>

                    <?php endif; ?>

                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h3 class="text-xl font-semibold text-gray-700 mb-4">Skriv ett inlägg</h3>

    <form action="../actions/store_post.php" method="POST" class="space-y-4 mb-10">
        <input type="hidden" name="discussion_id" value="<?php echo $discussion_id; ?>">

        <textarea name="content" rows="4"
                  class="w-full border border-gray-300 rounded px-3 py-2
                         focus:outline-none focus:ring focus:ring-blue-300"
                  required></textarea>

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Skicka
        </button>
    </form>

    <a href="group.php?id=<?php echo $group_id; ?>"
       class="inline-block bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 transition">
        Tillbaka till gruppen
    </a>

</div>

<div id="deleteModal"
     class="fixed inset-0 bg-black/60 hidden items-center justify-center">

    <div class="bg-white p-6 rounded shadow max-w-sm w-full">
        <h3 class="text-xl font-semibold mb-3">Ta bort inlägg</h3>

        <p class="text-gray-700 mb-6">
            Är du säker på att du vill ta bort detta inlägg?
        </p>

        <div class="flex justify-end gap-3">
            <button id="cancelDelete"
                    class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 transition">
                Avbryt
            </button>

            <a id="confirmDelete"
               href="#"
               class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
               Ta bort
            </a>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(deleteUrl) {
        const modal = document.getElementById('deleteModal');
        const confirmBtn = document.getElementById('confirmDelete');

        confirmBtn.href = deleteUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    document.getElementById('cancelDelete').onclick = function() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };
</script>

<?php require_once __DIR__ .'/../includes/footer.php' ?>




