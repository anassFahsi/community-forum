<?php

require_once __DIR__ .'/../includes/db.php';

session_start();

if (!isset($_SESSION["user_id"])) {
    header("location:login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pdo = getPDO();

$stmt = $pdo->prepare("
    SELECT * FROM groups g
    JOIN group_members gm ON gm.group_id = g.id
    WHERE gm.user_id = ?
");
$stmt->execute([$user_id]);
$my_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT g.id, g.name, g.description
    FROM groups g
    WHERE g.id NOT IN (
        SELECT group_id FROM group_members WHERE user_id = ?
    )
");
$stmt->execute([$user_id]);
$other_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require_once __DIR__ .'/../includes/header.php' ?>

<div class="max-w-4xl mx-auto mt-10">

    
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Grupper</h1>

        <a href="create_group.php"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
           Skapa grupp
        </a>
    </div>

  
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Dina grupper</h2>

    <?php if (count($my_groups) === 0): ?>
        <p class="text-gray-600 mb-8">Du är inte medlem i några grupper ännu.</p>
    <?php else: ?>
        <ul class="space-y-4 mb-10">
            <?php foreach ($my_groups as $group): ?>
                <li class="bg-white p-5 rounded shadow">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <?php echo htmlspecialchars($group['name']); ?>
                    </h3>

                    <p class="text-gray-600 mt-1 mb-4">
                        <?php echo htmlspecialchars($group['description']); ?>
                    </p>

                    <a href="group.php?id=<?php echo $group['id']; ?>"
                       class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                       Gå till grupp
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>


    <h2 class="text-xl font-semibold text-gray-700 mb-4">Andra grupper</h2>

    <?php if (count($other_groups) === 0): ?>
        <p class="text-gray-600">Det finns inga fler grupper att gå med i.</p>
    <?php else: ?>
        <ul class="space-y-4">
            <?php foreach ($other_groups as $group): ?>
                <li class="bg-white p-5 rounded shadow">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <?php echo htmlspecialchars($group['name']); ?>
                    </h3>

                    <p class="text-gray-600 mt-1 mb-4">
                        <?php echo htmlspecialchars($group['description']); ?>
                    </p>

                    <a href="../actions/join_group.php?id=<?php echo $group['id']; ?>"
                       class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                       Gå med i gruppen
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</div>


<?php require_once __DIR__ .'/../includes/footer.php' ?>



    
    
