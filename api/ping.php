<?php 
    require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();
try{
    $pdo->prepare("SELECT 1");
    echo "Ping ok";
}
catch(exception $e){
    echo "Ping Fail ".$e->getMessage();
}
