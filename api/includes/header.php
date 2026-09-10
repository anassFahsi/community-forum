<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db.php';

$notifications = [];

if (isset($_SESSION['user_id'])) {
    $pdo = getPDO();
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        SELECT message 
        FROM notifications
        WHERE user_id = ? AND is_read = 0
        ORDER BY created_at DESC
    ");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($notifications)) {
        $stmt = $pdo->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
    }
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Community Forum</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<?php if (!empty($notifications)): ?>
    <div class="max-w-full sm:max-w-5xl mx-auto px-4 mt-4">
        <div class="p-4 mb-4 text-base sm:text-sm text-green-800 rounded-lg bg-green-50 shadow">
            <?php foreach ($notifications as $note): ?>
                <p><?php echo htmlspecialchars($note['message']); ?></p>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<header class="bg-white shadow">
    <nav class="w-full max-w-full sm:flex sm:justify-between sm:max-w-5xl mx-auto px-4 sm:px-6 py-4">

        <div class="flex items-center justify-between sm:justify-between">
            <a href="index.php" class="text-lg sm:text-xl font-semibold text-gray-800">
                Community Forum
            </a>

            <button id="menuBtn" class="sm:hidden text-gray-700 hover:text-blue-600">
                <svg id="menuIcon" class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path id="menuIconPath" stroke-linecap="round" stroke-linejoin="round"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <div id="menu"
             class="hidden flex  flex-col sm:flex sm:flex-row sm:justify-end gap-3 sm:gap-6 text-base sm:text-lg mt-4 sm:mt-0">

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

<script>
    const btn = document.getElementById('menuBtn');
    const menu = document.getElementById('menu');
    const icon = document.getElementById('menuIconPath');

    btn.addEventListener('click', () => {
        const isHidden = menu.classList.contains('hidden');

        if (isHidden) {
            menu.classList.remove('hidden');

            icon.setAttribute("d", "M6 18L18 6M6 6l12 12");
        } else {
            menu.classList.add('hidden');

            icon.setAttribute("d", "M4 6h16M4 12h16M4 18h16");
        }
    });

    menu.addEventListener('click', () => {
        menu.classList.add('hidden');
        icon.setAttribute("d", "M4 6h16M4 12h16M4 18h16");
    });
</script>



<main class="max-w-full sm:max-w-5xl mx-auto px-4 sm:px-6 py-6 flex-grow min-h-[76vh]">

