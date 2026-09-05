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
    FROM groups
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
    SELECT r.id, r.user_id, r.created_at, u.first_name, u.last_name, u.email
    FROM group_join_requests r
    JOIN users u ON r.user_id = u.id
    WHERE r.group_id = ? AND r.status = 'pending'
");
$stmt->execute([$group_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require_once __DIR__ .'/../includes/header.php'?>
<div class="manage-members-page container">

    <h1 class="page-title">
        Hantera medlemmar i <?php echo htmlspecialchars($group['name']); ?>
    </h1>

    <a href="group.php?id=<?php echo $group_id; ?>" class="btn btn-secondary back-btn">
        Tillbaka till gruppen
    </a>

    <h2 class="section-title">Medlemmar</h2>

    <?php if (count($members) === 0): ?>
        <p class="empty-message">Inga medlemmar ännu.</p>
    <?php else: ?>
        <table class="table members-table">
            <thead>
                <tr>
                    <th>Namn</th>
                    <th>E-post</th>
                    <th>Roll</th>
                    <th>Gick med</th>
                    <th>Åtgärder</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $m): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($m['first_name'] . ' ' . $m['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($m['email']); ?></td>
                        <td><?php echo htmlspecialchars($m['role']); ?></td>
                        <td><?php echo htmlspecialchars($m['joined_at']); ?></td>
                        <td>
                            <?php if ($m['user_id'] != $user_id): ?>

                                <a href="../actions/remove_member.php?id=<?php echo $m['id']; ?>&group_id=<?php echo $group_id; ?>" 
                                   class="action-link remove-link">
                                    Ta bort
                                </a>

                                <?php if ($m['role'] !== 'admin'): ?>
                                    |
                                    <a href="../acions/make_admin.php?id=<?php echo $m['id']; ?>&group_id=<?php echo $group_id; ?>" 
                                       class="action-link admin-link">
                                        Gör till admin
                                    </a>
                                <?php endif; ?>

                            <?php else: ?>
                                <span class="you-label">(Du)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>


    <h2 class="section-title">Ansökningar</h2>

    <?php if (count($requests) === 0): ?>
        <p class="empty-message">Inga ansökningar.</p>
    <?php else: ?>
        <ul class="request-list">
            <?php foreach ($requests as $req): ?>
                <li class="request-item">
                    <span class="request-user">
                        <?php echo htmlspecialchars($req['first_name'] . ' ' . $req['last_name']); ?>
                        (<?php echo htmlspecialchars($req['email']); ?>)
                    </span>

                    <a href="../actions/approve_request.php?id=<?php echo $req['id']; ?>&group_id=<?php echo $group_id; ?>" 
                       class="action-link approve-link">
                        Godkänn
                    </a>
                    |
                    <a href="../actions/reject_request.php?id=<?php echo $req['id']; ?>&group_id=<?php echo $group_id; ?>" 
                       class="action-link reject-link">
                        Neka
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a href="actions/create_invite.php?group_id=<?php echo $group_id; ?>" class="btn  btn-primary">
     Skapa inbjudningslänk
   </a>


</div>
<?php require_once __DIR__ .'/../includes/footer.php' ?>

