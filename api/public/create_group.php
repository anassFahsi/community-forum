<?php require_once __DIR__ .'/../includes/header.php' ?>

<div class="max-w-md mx-auto bg-white p-8 rounded shadow mt-16">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        Skapa ny grupp
    </h1>

    <form action="../actions/store_group.php" method="POST" class="space-y-5">

        <div>
            <label for="name" class="block text-gray-700 font-medium mb-1">
                Gruppnamn
            </label>
            <input type="text" id="name" name="name"
                   class="w-full border border-gray-300 rounded px-3 py-2
                          focus:outline-none focus:ring focus:ring-blue-300"
                   required>
        </div>

        <div>
            <label for="description" class="block text-gray-700 font-medium mb-1">
                Beskrivning
            </label>
            <textarea id="description" name="description" rows="4"
                      class="w-full border border-gray-300 rounded px-3 py-2
                             focus:outline-none focus:ring focus:ring-blue-300"
                      required></textarea>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded
                       hover:bg-blue-700 transition">
            Skapa grupp
        </button>

    </form>

</div>

<?php require_once __DIR__ .'/../includes/footer.php' ?>

