<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$first_name = htmlspecialchars($_SESSION['first_name']);
?>



<?php require_once __DIR__ .'/../includes/header.php' ?>
<div class="dashboard">
    <h1 class="page-title">Välkommen, <?php echo $first_name; ?>!</h1>

    <p class="dashboard-text">Du är nu inloggad.</p>

    <a href="logout.php" class="btn btn-primary dashboard-logout-btn">Logga ut</a>
</div>
<?php require_once __DIR__ .'/../includes/footer.php' ?>


