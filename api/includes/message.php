<?php require_once __DIR__ . '/header.php'; ?>

<div class="max-w-lg mx-auto bg-white shadow p-8 mt-20 rounded text-center">

    <p class="text-gray-700 text-lg mb-6">
        <?= htmlspecialchars($message) ?>
    </p>

    <?php if (!empty($extraHtml)): ?>
        <div class="text-blue-600 mb-6">
            <?= $extraHtml ?> 
        </div>
    <?php endif; ?>

    <a href="<?= htmlspecialchars($backLink) ?>"
       class="inline-block bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
        Tillbaka
    </a>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

