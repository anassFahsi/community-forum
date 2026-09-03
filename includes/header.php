<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Community Forum</title>
</head>
<body>

<header class="site-header">
    <nav class="nav-bar">
        <a href="index.php" class="nav-link">Start</a>
        <a href="groups.php" class="nav-link">Grupper</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="nav-link">Logga ut</a>
        <?php else: ?>
            <a href="login.php" class="nav-link">Logga in</a>
            <a href="register.php" class="nav-link">Registrera</a>
        <?php endif; ?>
    </nav>
</header>

<main class="main-content">

