<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id  = $_SESSION['user_id'];
$group_id = $_GET['group_id'] ?? null;

if (!$group_id) {
    echo "<p>Ingen grupp angiven.</p>";
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, name, created_by
    FROM `groups`
    WHERE id = ?
");
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

$is_admin = $membership && $membership['role'] === 'admin';

if (!$is_admin) {
    echo "<p>Du måste vara admin i gruppen för att hantera medlemmar.</p>";
    exit;
}

$stmt = $pdo->prepare("
    SELECT gm.id, gm.user_id, gm.role, gm.joined_at,
           u.first_name, u.last_name, u.email
    FROM group_members gm
    JOIN users u ON gm.user_id = u.id
    WHERE gm.group_id = ?
    ORDER BY gm.joined_at ASC
");
$stmt->execute([$group_id]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT r.id, r.user_id, r.created_at,
           u.first_name, u.last_name, u.email
    FROM group_join_requests r
    JOIN users u ON r.user_id = u.id
    WHERE r.group_id = ? AND r.status = 'pending'
");
$stmt->execute([$group_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once __DIR__ .'/../includes/header.php' ?>

<div class="max-w-5xl mx-auto mt-10">

    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        Hantera medlemmar i <?php echo htmlspecialchars($group['name']); ?>
    </h1>

    <a href="group.php?id=<?php echo $group_id; ?>"
       class="inline-block mb-8 bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 transition">
        Tillbaka till gruppen
    </a>

   
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Medlemmar</h2>

    <?php if (count($members) === 0): ?>
        <p class="text-gray-600 mb-8">Inga medlemmar ännu.</p>
    <?php else: ?>

        <div class="overflow-x-auto mb-10">
            <table class="min-w-full bg-white border border-gray-300 rounded shadow">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-700 font-medium">Namn</th>
                        <th class="px-4 py-2 text-left text-gray-700 font-medium">E‑post</th>
                        <th class="px-4 py-2 text-left text-gray-700 font-medium">Roll</th>
                        <th class="px-4 py-2 text-left text-gray-700 font-medium">Gick med</th>
                        <th class="px-4 py-2 text-left text-gray-700 font-medium">Åtgärder</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($members as $m): ?>
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                <?php echo htmlspecialchars($m['first_name'] . ' ' . $m['last_name']); ?>
                            </td>

                            <td class="px-4 py-2">
                                <?php echo htmlspecialchars($m['email']); ?>
                            </td>

                            <td class="px-4 py-2">
                                <?php echo htmlspecialchars($m['role']); ?>
                            </td>

                            <td class="px-4 py-2">
                                <?php echo htmlspecialchars($m['joined_at']); ?>
                            </td>

                            <td class="px-4 py-2">

                                <?php if ($m['user_id'] != $user_id): ?>

                                    <a href="../actions/remove_member.php?id=<?php echo $m['id']; ?>&group_id=<?php echo $group_id; ?>"
                                       class="text-red-600 hover:text-red-700 font-medium">
                                        Ta bort
                                    </a>

                                    <?php if ($m['role'] !== 'admin'): ?>
                                        <span class="mx-2 text-gray-400">|</span>

                                        <a href="../actions/make_admin.php?id=<?php echo $m['id']; ?>&group_id=<?php echo $group_id; ?>"
                                           class="text-blue-600 hover:text-blue-700 font-medium">
                                            Gör till admin
                                        </a>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <span class="text-gray-500">(Du)</span>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    <?php endif; ?>

    
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Ansökningar</h2>

    <?php if (count($requests) === 0): ?>
        <p class="text-gray-600 mb-8">Inga ansökningar.</p>
    <?php else: ?>

        <ul class="space-y-4 mb-10">
            <?php foreach ($requests as $req): ?>
                <li class="bg-white p-4 rounded shadow flex items-center justify-between">

                    <span class="text-gray-800 font-medium">
                        <?php echo htmlspecialchars($req['first_name'] . ' ' . $req['last_name']); ?>
                        (<?php echo htmlspecialchars($req['email']); ?>)
                    </span>

                    <div class="flex items-center gap-4">

                        <a href="../actions/approve_request.php?id=<?php echo $req['id']; ?>&group_id=<?php echo $group_id; ?>"
                           class="text-green-600 hover:text-green-700 font-medium">
                            Godkänn
                        </a>

                        <a href="../actions/reject_request.php?id=<?php echo $req['id']; ?>&group_id=<?php echo $group_id; ?>"
                           class="text-red-600 hover:text-red-700 font-medium">
                            Neka
                        </a>

                    </div>

                </li>
            <?php endforeach; ?>
        </ul>

    <?php endif; ?>

    
    <a href="../actions/create_invite.php?group_id=<?php echo $group_id; ?>"
       class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
        Skapa inbjudningslänk
    </a>

</div>

<?php require_once __DIR__ .'/../includes/footer.php' ?>



