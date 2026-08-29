<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logga in</title>
</head>
<body>
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

    
</body>
</html>