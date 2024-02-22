<?php

use App\Model\Category;

require_once __DIR__ . '/vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'./');
$dotenv->load();

$router = new AltoRouter();

// Assurez-vous que le basePath est correct
$router->setBasePath('/pwd');

$router->map('GET', '/', function() {
    echo "Hello homepage";
});

$router->map('GET', '/product', function() {
    echo "Hello product list";
});

// Assurez-vous de n'avoir cette route qu'une seule fois
$router->map('GET', '/category/[i:id]', function($id) {
    $category = new Category();
    $category = $category->findOneById($id);
    echo "Hello category " . $category->getName();
});

$router->map('POST', '/register/[a:fullname]/[**:email]/[**:password],[**:confirmPassword]', function ($fullname, $email, $password, $confirmPassword) {
    // Traitement des données d'inscription
    // Redirection ou affichage d'un message de succès/erreur
    $fullname = filter_input(INPUT_POST, $fullname, FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, $email, FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, $password, FILTER_SANITIZE_STRING);
    $confirmPassword = filter_input(INPUT_POST, $confirmPassword, FILTER_SANITIZE_STRING);

    $_SESSION['test'] = [
        'fullname' => $fullname,
        'email' => $email,
        'password' => $password,
        'confirmPassword' => $confirmPassword
    ];
    var_dump($fullname, $email, $password, $confirmPassword);
});



//$router->map('POST', '/register', function() {
//    // Traitement des données d'inscription
//    // Redirection ou affichage d'un message de succès/erreur
//    header('Location: /pwd/login');
//exit();
//});

$router->map('GET', '/login', function() {
    header('Location: View/login.php');
    exit();
});

$router->map('GET', '/shop', function() {
    header('Location: View/shop.php');
    exit();
});

// Correspondance et exécution de la route
$match = $router->match();

if(is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']); 
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo "404 Not Found";
}
