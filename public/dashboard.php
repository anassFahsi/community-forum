<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$first_name = htmlspecialchars($_SESSION['first_name']);
?>

<?php require_once __DIR__ .'/../includes/header.php' ?>

<div class="max-w-md mx-auto bg-white p-8 rounded shadow mt-16 text-center">

    <h1 class="text-3xl font-bold text-gray-800 mb-4">
        Välkommen, <?php echo $first_name; ?>!
    </h1>

    <p class="text-gray-600 mb-8">
        Du är nu inloggad.
    </p>

    <a href="logout.php"
       class="inline-block bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700 transition">
        Logga ut
    </a>

</div>

<?php require_once __DIR__ .'/../includes/footer.php' ?>



