

<?php require_once __DIR__ .'/../includes/header.php' ?>
    <h1 class="page-title">Logga in</h1>
    <form action="login_user.php" method="post" class="form">
        <div class="form-group">
            <label>E-post</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Lösenord</label>
            <input type="password" name="password" required>
        </div>

        <button class="btn">Logga in</button>
    </form>
<?php require_once __DIR__ .'/../includes/footer.php' ?>
    
