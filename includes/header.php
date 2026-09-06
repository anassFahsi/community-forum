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
   <link rel="stylesheet" href="../assets/styles/layout.css?v=<?php echo time(); ?>">

    <link rel="stylesheet" href="../assets/styles/layout.css">
     <link rel="stylesheet" href="../assets/styles/base.css">
    <link rel="stylesheet" href="../assets/styles/forms.css">
    <link rel="stylesheet" href="../assets/styles/pages.css">
</head>
<body>

<header class="site-header">
    <nav class="nav-bar">
        <a href="index.php" class="nav-link">Start</a>
        <a href="groups.php" class="nav-link">Grupper</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../actions/logout.php" class="nav-link">Logga ut</a>
        <?php else: ?>
            <a href="../public/login.php" class="nav-link">Logga in</a>
            <a href="../public/register.php" class="nav-link">Registrera</a>
        <?php endif; ?>
    </nav>
</header>

<main class="main-content">

