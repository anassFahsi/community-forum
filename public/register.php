

<?php require_once __DIR__ .'/../includes/header.php' ?>
    <h1 class="page-title">Registerera konto</h1>

    <form action="../actions/register_user.php" method="POST" class="form">
        <div class="form-group">
            <lable for="first_name" class="form-label">Förnamn</lable>
            <input type="text" id="first_name" name="first_name" class="form-input"    required>
        </div>

        <div class="form-group">
            <lable for="last_name" class="form-label">Efternamn</lable>
            <input type="text" id="last_name" name="last_name" class="form-input" required>
        </div>

        <div class="form-group">
            <lable for="email" class="form-label">E-post</lable>
            <input type="email" id="email" name="email" class="form-input" required>
        </div>

        <div class="form-group">
            <lable for="password" class="form-label">Lösenord</lable>
            <input type="password" id="password" name="password" class="form-input"required>
        </div>

        <button type="submit" class="btn btn-primary">Registrera</button>
        
         
    </form>
<?php require_once __DIR__ .'/../includes/footer.php' ?>