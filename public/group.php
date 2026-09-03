<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

$user_id = $_SESSION['user_id'];

$group_id = $_GET['id'] ;

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
<div class="group-page">

    <h1 class="page-title"><?php echo htmlspecialchars($group['name']); ?></h1>
    <p class="group-description"><?php echo htmlspecialchars($group['description']); ?></p>


    <?php if (!$is_member): ?>
        <p class="info-text">Du är inte medlem i denna grupp.</p>
        <a href="join_group.php?id=<?php echo $group_id; ?>" class="btn btn-primary">Gå med i gruppen</a>
    <?php else: ?>
        <p class="info-text">Du är medlem i denna grupp.</p>
        <?php if ($is_admin): ?>
          <a href="manage_members.php?group_id=<?php echo $group_id; ?>"    class="btn btn-secondary">
          Hantera medlemmar
         </a>
       <?php endif; ?>


        <a href="create_discussion.php?group_id=<?php echo $group_id; ?>" class="btn btn-primary">
            Skapa diskussion
        </a>

        <h2 class="section-title">Diskussioner</h2>

        <?php if (count($discussions) === 0): ?>
            <p class="info-text">Inga diskussioner ännu.</p>
        <?php else: ?>
            <ul class="discussion-list">
                <?php foreach ($discussions as $d): ?>
                    <li class="discussion-item">
                        <a href="discussion.php?id=<?php echo $d['id']; ?>" class="discussion-link">
                            <?php echo htmlspecialchars($d['subject']); ?>
                        </a>
                        <p class="discussion-meta">
                            Skapad av <?php echo htmlspecialchars($d['first_name'].' '.$d['last_name']); ?>
                            den <?php echo htmlspecialchars($d['created_at']); ?>
                        </p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    <?php endif; ?>

    <a href="groups.php" class="btn btn-secondary">Tillbaka</a>

</div>
<?php require_once __DIR__ .'/../includes/footer.php' ?>

