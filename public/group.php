<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id = $_SESSION['user_id'];
$group_id = $_GET['id'];

if (!$group_id) {
    echo "<p>Ingen grupp angiven.</p>";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM groups WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    echo "<p>Gruppen finns inte.</p>";
    exit;
}

$stmt = $pdo->prepare("
    SELECT role 
    FROM group_members 
    WHERE user_id = ? AND group_id = ?
");
$stmt->execute([$user_id, $group_id]);
$membership = $stmt->fetch(PDO::FETCH_ASSOC);

$is_member = $membership !== false;
$is_admin = $is_member && $membership['role'] === 'admin';

$stmt = $pdo->prepare("
    SELECT d.id, d.subject, d.created_at, u.first_name, u.last_name
    FROM discussions d
    JOIN users u ON d.created_by = u.id
    WHERE d.group_id = ?
    ORDER BY d.created_at DESC
");
$stmt->execute([$group_id]);
$discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once __DIR__ .'/../includes/header.php' ?>

<div class="max-w-4xl mx-auto mt-10">

    <h1 class="text-3xl font-bold text-gray-800 mb-2">
        <?php echo htmlspecialchars($group['name']); ?>
    </h1>

    <p class="text-gray-600 mb-6">
        <?php echo htmlspecialchars($group['description']); ?>
    </p>

    <?php if (!$is_member): ?>
        <p class="text-red-600 font-medium mb-4">Du är inte medlem i denna grupp.</p>

        <a href="join_group.php?id=<?php echo $group_id; ?>"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
           Gå med i gruppen
        </a>

    <?php else: ?>

        <p class="text-green-700 font-medium mb-4">Du är medlem i denna grupp.</p>

        <?php if ($is_admin): ?>
            <a href="manage_members.php?group_id=<?php echo $group_id; ?>"
               class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 transition mr-2">
               Hantera medlemmar
            </a>
        <?php endif; ?>

        <a href="create_discussion.php?group_id=<?php echo $group_id; ?>"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition mr-2">
           Skapa diskussion
        </a>

        <?php if ($group['created_by'] == $user_id): ?>
            <button 
                onclick="openGroupDeleteModal()" 
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                Ta bort grupp
            </button>
        <?php endif; ?>

        <h2 class="text-xl font-semibold text-gray-700 mt-10 mb-4">Diskussioner</h2>

        <?php if (count($discussions) === 0): ?>
            <p class="text-gray-600">Inga diskussioner ännu.</p>

        <?php else: ?>
            <ul class="space-y-4">
                <?php foreach ($discussions as $d): ?>
                    <li class="bg-white p-5 rounded shadow">

                        <a href="discussion.php?id=<?php echo $d['id']; ?>"
                           class="text-lg font-semibold text-blue-600 hover:underline">
                           <?php echo htmlspecialchars($d['subject']); ?>
                        </a>

                        <p class="text-gray-600 mt-1 mb-4">
                            Skapad av <?php echo htmlspecialchars($d['first_name'].' '.$d['last_name']); ?>
                            den <?php echo htmlspecialchars($d['created_at']); ?>
                        </p>

                        <a href="#"
                           class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition"
                           onclick="openDeleteModal('../actions/delete_discussion.php?id=<?php echo $d['id']?>&group_id=<?php echo $group_id;?>'); return false;">
                           Ta bort
                        </a>

                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    <?php endif; ?>

    <a href="groups.php"
       class="inline-block mt-10 bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 transition">
       Tillbaka
    </a>

</div>

<!-- Delete Discussion Modal -->
<div id="deleteModal"
     class="fixed inset-0 bg-black/60 hidden items-center justify-center">

    <div class="bg-white p-6 rounded shadow max-w-sm w-full">
        <h3 class="text-xl font-semibold mb-3">Ta bort diskussion</h3>

        <p class="text-gray-700 mb-6">
            Är du säker på att du vill ta bort denna diskussion?
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

<!-- Delete Group Modal -->
<div id="deleteGroupModal"
     class="fixed inset-0 bg-black/60 hidden items-center justify-center">

    <div class="bg-white p-6 rounded shadow max-w-sm w-full">
        <h3 class="text-xl font-semibold mb-3">Ta bort grupp</h3>

        <p class="text-gray-700 mb-6">
            Är du säker på att du vill ta bort denna grupp? 
            Alla diskussioner, inlägg och medlemmar tas bort permanent.
        </p>

        <div class="flex justify-end gap-3">
            <button onclick="closeGroupDeleteModal()"
                    class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 transition">
                Avbryt
            </button>

            <a href="../actions/delete_group.php?group_id=<?php echo $group_id; ?>"
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

    function openGroupDeleteModal() {
        const modal = document.getElementById('deleteGroupModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeGroupDeleteModal() {
        const modal = document.getElementById('deleteGroupModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>

<?php require_once __DIR__ .'/../includes/footer.php' ?>



