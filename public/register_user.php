<?php
require_once __DIR__ . '/../includes/db.php';

$pdo = getPDO();

$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$password   = $_POST['password'] ?? '';

$errors = [];

if ($first_name === '') {
    $errors[] = 'Förnamn är obligatoriskt.';
}

if ($last_name === '') {
    $errors[] = 'Efternamn är obligatoriskt.';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Ogiltig e-postadress.';
}

if (strlen($password) < 6) {
    $errors[] = 'Lösenordet måste vara minst 6 tecken.';
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, password_hash)
        VALUES (:first_name, :last_name, :email, :password_hash)
    ");

    $stmt->execute([
        ':first_name'    => $first_name,
        ':last_name'     => $last_name,
        ':email'         => $email,
        ':password_hash' => $password_hash,
    ]);

    header('Location: register_success.php');
    exit;

} catch (PDOException $e) {
    echo "Kunde inte skapa användare. Kanske finns e-posten redan?";
}
