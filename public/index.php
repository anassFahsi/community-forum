<?php
session_start();
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Start</title>
</head>
<body>

<?php require_once __DIR__ .'/../includes/header.php' ?>
<div class="index-page">

    <h1 class="page-title">Välkommen till Community Forum</h1>

    <p class="index-text">
        Här kan du gå med i grupper, diskutera ämnen och träffa andra med samma intressen.
    </p>

    <?php if (!isset($_SESSION['user_id'])): ?>

        <div class="index-actions">
            <a href="login.php" class="btn btn-primary">Logga in</a>
            <a href="register.php" class="btn btn-secondary">Skapa konto</a>
        </div>

    <?php else: ?>

        <div class="index-actions">
            <a href="groups.php" class="btn btn-primary">Visa grupper</a>
        </div>

    <?php endif; ?>

</div>
<?php require_once __DIR__ .'/../includes/footer.php' ?>

