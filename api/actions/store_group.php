<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ ."/../includes/db.php";
$pdo = getPDO();

$user_id     = $_SESSION['user_id'];
$name        = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$errors      = [];

if ($name === '') {
    $errors[] = "Gruppnamn är obligatoriskt.";
}

if ($description === '') {
    $errors[] = "Beskrivning är obligatorisk.";
}

if (!empty($errors)) {
    $message = implode("<br>", $errors);
    $backLink = "../public/create_group.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}

try {

    $stmt = $pdo->prepare("
        INSERT INTO `groups` (name, description, created_by)
        VALUES (:name, :description, :created_by)
    ");
    $stmt->execute([
        ':name'        => $name,
        ':description' => $description,
        ':created_by'  => $user_id
    ]);

    /* Get the newly created group ID */
    $group_id = $pdo->lastInsertId();

    /* Add creator as admin */
    $stmt = $pdo->prepare("
        INSERT INTO group_members (user_id, group_id, role)
        VALUES (:user_id, :group_id, 'admin')
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':group_id' => $group_id
    ]);

    header("Location: ../public/groups.php");
    exit;

} catch (PDOException $e) {

    $message = "Kunde inte skapa grupp. Försök senare.";
    $backLink = "../public/create_group.php";
    require __DIR__ . "/../includes/message.php";
    exit;
}






