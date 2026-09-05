<?php
require_once __DIR__.'/../includes/db.php';

session_start();

$pdo = getPDO();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

/* Validate input */
$errors = [];
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Ogiltig e-postadress.';
}

if ($password === '') {
    $errors[] = 'Lösenord är obligatoriskt.';
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
    exit;
}

/* Fetch user by email */
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch($pdo::FETCH_ASSOC);

/* Check if user exists */
if (!$user) {
    echo "<p>Fel e-post eller lösenord.</p>";
    exit;
}

/* Verify password */
if (!password_verify($password, $user['password_hash'])) {
    echo "<p>Fel e-post eller lösenord.</p>";
    exit;
}

/* Store user session */
$_SESSION['user_id'] = $user['id'];
$_SESSION['first_name'] = $user['first_name'];

header('Location: ../public/dashboard.php');
exit;
