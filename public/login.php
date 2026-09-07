<?php require_once __DIR__ .'/../includes/header.php' ?>

<div class="max-w-md mx-auto bg-white p-8 rounded shadow mt-16">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        Logga in
    </h1>

    <form action="../actions/login_user.php" method="post" class="space-y-5">

        <div>
            <label class="block text-gray-700 font-medium mb-1">
                E-post
            </label>
            <input type="email" name="email"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                   required>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-1">
                Lösenord
            </label>
            <input type="password" name="password"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                   required>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
            Logga in
        </button>

    </form>

</div>

<?php require_once __DIR__ .'/../includes/footer.php' ?>

    
