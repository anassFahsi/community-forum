<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$first_name = htmlspecialchars($_SESSION['first_name']);
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="dashboard">
    <h1 class="page-title">Välkommen, <?php echo $first_name; ?>!</h1>

    <p class="dashboard-text">Du är nu inloggad.</p>

    <a href="logout.php" class="btn btn-primary dashboard-logout-btn">Logga ut</a>
</div>

</body>
</html>

