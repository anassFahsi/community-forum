<?php
require_once __DIR__.'/../includes/db.php';

session_start();

$pdo = getPDO();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Ogiltig e-postadress.';
}

if ($password === '') {
    $errors[] = 'Lösenord är obligatoriskt.';
}

if (!empty($errors)) {
    $message = implode("<br>", $errors);
    $backLink = "../public/login.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch($pdo::FETCH_ASSOC);

if (!$user) {
    $message = "Fel e-post eller lösenord.";
    $backLink = "../public/login.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    $message = "Fel e-post eller lösenord.";
    $backLink = "../public/login.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['first_name'] = $user['first_name'];

header('Location: ../public/dashboard.php');
exit;

