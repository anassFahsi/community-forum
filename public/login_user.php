<?php
require_once __DIR__.'/../includes/db.php';

session_start();

$pdo=getPDO();

$email=trim($_POST['email']??'' );
$password=$_POST['password']??'' ;

$errors=[];
if($email===''||!filter_var($email,FILTER_VALIDATE_EMAIL)){
    $errors[]= 'Ogiltig e-postadress.';
}

if ($password==='') {
    $errors[] = 'Lösenordet är obligatoriskt.';
}
if(!empty($errors)){
    foreach($errors as $error){
        echo "<p>$error</p>";
    }
    exit;
}


$stmt=$pdo->prepare("SELECT * FROM users WHERE email=:email limit 1");
$stmt->execute([':email'=>$email]);
$user=$stmt->fetch($pdo::FETCH_ASSOC);

if(!$user){
    echo "<p>Fel e-post eller lösenord.</p>";
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    echo "<p>Fel e-post eller lösenord.</p>";
    exit;
}

$_SESSION['user_id']=$user['id'];
$_SESSION['first_name']=$user['first_name'];

header('location:dashboard.php');
exit;
