<?php 

if(isset($_GET['route'])){
    $route=$_GET['route'];
    if($_SERVER['REQUEST_METHOD']==='POST'){
        if(empty($_POST) && !empty(file_get_contents('php://input'))){
            $_POST=json_decode(file_get_contents('php://input'),true) ?? [];
        }
    }

    if(strpos($route, 'public/')===0){
        $route=substr($route,7);
    }

    $route=preg_replace('/\.php$/','',$route);
    if($route===''||$route==='index'){
        unset($_GET['route']);
    }else{
        if(strpos($route, 'actions/')===0){
            $actionFile=dirname(__DIR__) .'/' .$route . '.php';
            if(file_exists($actionFile)){
                include $actionFile;
                exit;
            }
        }

        $publicFile= __DIR__ .'/' .$route .'.php';
        if(file_exists($publicFile)){
            include $publicFile;
            exit;
        }
        http_response_code(404);
        echo "<h3>Sidan hittades inte (404)</h3>";
        echo "<b>Letade efter filen på följande platser:</b><br>";
        echo "- ".htmlspecialchars(dirname(__DIR__) . '/'. $route .'php')."<br>";
        echo "- ".htmlspecialchars(__DIR__.'/' .$route.'.php')."<br>";
        exit;
    }
}










session_start();
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Start</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<?php require_once __DIR__ .'/../includes/header.php' ?>

<div class="max-w-3xl mx-auto text-center py-16 px-4">

    <h1 class="text-3xl font-bold text-gray-800 mb-4">
        Välkommen till Community Forum
    </h1>

    <p class="text-gray-600 text-lg mb-10">
        Här kan du gå med i grupper, diskutera ämnen och träffa andra med samma intressen.
    </p>

    <?php if (!isset($_SESSION['user_id'])): ?>

        <div class="flex justify-center gap-4">
            <a href="login.php"
               class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
               Logga in
            </a>

            <a href="register.php"
               class="bg-gray-700 text-white px-5 py-2 rounded hover:bg-gray-800 transition">
               Skapa konto
            </a>
        </div>

    <?php else: ?>

        <div class="flex justify-center">
            <a href="groups.php"
               class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
               Visa grupper
            </a>
        </div>

    <?php endif; ?>

</div>

<?php require_once __DIR__ .'/../includes/footer.php' ?>

</body>
</html>


