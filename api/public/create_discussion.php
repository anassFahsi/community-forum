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

$stmt = $pdo->prepare("SELECT id, name FROM `groups` WHERE id = ?");
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

<?php require_once __DIR__ .'/../includes/header.php' ?>

<div class="max-w-md mx-auto bg-white p-8 rounded shadow mt-16">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        Skapa diskussion i <?php echo htmlspecialchars($group['name']); ?>
    </h1>

    <form action="../actions/store_discussion.php" method="POST" class="space-y-5">

        <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">

        <div>
            <label for="subject" class="block text-gray-700 font-medium mb-1">
                Ämne
            </label>
            <input type="text" id="subject" name="subject"
                   class="w-full border border-gray-300 rounded px-3 py-2
                          focus:outline-none focus:ring focus:ring-blue-300"
                   required>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded
                       hover:bg-blue-700 transition">
            Skapa
        </button>

    </form>

    <a href="group.php?id=<?php echo $group_id; ?>"
       class="inline-block mt-6 bg-gray-700 text-white px-4 py-2 rounded
              hover:bg-gray-800 transition">
        Tillbaka
    </a>

</div>

<?php require_once __DIR__ .'/../includes/footer.php' ?>


