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

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100" >

<header class="bg-white shadow">
    <nav class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">

        <a href="index.php" class="text-xl font-semibold text-gray-800">
            Community Forum
        </a>

        <div class="flex gap-4">
            <a href="index.php" class="text-gray-700 hover:text-blue-600 transition">
                Start
            </a>

            <a href="groups.php" class="text-gray-700 hover:text-blue-600 transition">
                Grupper
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../actions/logout.php" class="text-red-600 hover:text-red-700 font-medium transition">
                    Logga ut
                </a>
            <?php else: ?>
                <a href="../public/login.php" class="text-gray-700 hover:text-blue-600 transition">
                    Logga in
                </a>

                <a href="../public/register.php" class="text-gray-700 hover:text-blue-600 transition">
                    Registrera
                </a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main class="max-w-5xl mx-auto px-4 py-6 flex-grow min-h-[76vh]">


