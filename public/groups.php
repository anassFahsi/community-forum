<?php

require_once __DIR__ .'/../includes/db.php';

session_start();

if(!isset($_SESSION["user_id"])){
    header("location:login.php");
    exit;
}
$user_id=$_SESSION['user_id'];

$pdo=getPDO();

$stmt = $pdo->prepare("
    SELECT g.id, g.name, g.description
    FROM groups g
    JOIN group_members gm ON gm.group_id = g.id
    WHERE gm.user_id = ?
");
$stmt->execute([$user_id]);
$my_groups=$stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt=$pdo->prepare("SELECT  g.id, g.name, g.description FROM groups g WHERE g.id NOT IN (SELECT group_id FROM group_members WHERE user_id=?)");
$stmt->execute([$user_id]);
$other_groups=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<?php require_once __DIR__ .'/../includes/header.php' ?>
<div class="groups-page">

    <h1 class="page-title">Grupper</h1>

    <a href="create_group.php" class="btn btn-primary">Skapa grupp</a>

    <h2 class="section-title">Dina grupper</h2>

    <?php if (count($my_groups) === 0): ?>
        <p class="info-text">Du är inte medlem i några grupper ännu.</p>
    <?php else: ?>
        <ul class="group-list">
            <?php foreach ($my_groups as $group): ?>
                <li class="group-item">
                    <h3 class="group-name"><?php echo htmlspecialchars($group['name']); ?></h3>
                    <p class="group-description"><?php echo htmlspecialchars($group['description']); ?></p>
                    <a href="group.php?id=<?php echo $group['id']; ?>" class="btn btn-primary">Gå till grupp</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2 class="section-title">Andra grupper</h2>

    <?php if (count($other_groups) === 0): ?>
        <p class="info-text">Det finns inga fler grupper att gå med i.</p>
    <?php else: ?>
        <ul class="group-list">
            <?php foreach ($other_groups as $group): ?>
                <li class="group-item">
                    <h3 class="group-name"><?php echo htmlspecialchars($group['name']); ?></h3>
                    <p class="group-description"><?php echo htmlspecialchars($group['description']); ?></p>
                    <a href="join_group.php?id=<?php echo $group['id']; ?>" class="btn btn-primary">Gå med i gruppen</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</div>
<?php require_once __DIR__ .'/../includes/footer.php' ?>



    
    
