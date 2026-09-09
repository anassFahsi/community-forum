<?php require_once __DIR__ .'/../includes/header.php' ?>

<div class="max-w-md mx-auto bg-white p-8 rounded shadow mt-10">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        Registrera konto
    </h1>

    <form action="../actions/register_user.php" method="POST" class="space-y-5">

        <div>
            <label for="first_name" class="block text-gray-700 font-medium mb-1">
                Förnamn
            </label>
            <input type="text" id="first_name" name="first_name"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                   required>
        </div>

        <div>
            <label for="last_name" class="block text-gray-700 font-medium mb-1">
                Efternamn
            </label>
            <input type="text" id="last_name" name="last_name"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                   required>
        </div>

        <div>
            <label for="email" class="block text-gray-700 font-medium mb-1">
                E-post
            </label>
            <input type="email" id="email" name="email"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                   required>
        </div>

        <div>
            <label for="password" class="block text-gray-700 font-medium mb-1">
                Lösenord
            </label>
            <input type="password" id="password" name="password"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                   required>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
            Registrera
        </button>

    </form>

</div>

<?php require_once __DIR__ .'/../includes/footer.php' ?>
