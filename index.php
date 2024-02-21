<?php
require_once __DIR__ . '/vendor/autoload.php';

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
$router->map('GET', '/product/[i:id]', function($id) {
    echo "Hello product with id : $id";
});

$router->map('GET', '/register', function() {
    require __DIR__ . '/View/register.php';
    // Supprimez le message si vous ne voulez afficher que la page de formulaire
});

$router->map('POST', '/register', function() {
    // Traitement des données d'inscription
    // Redirection ou affichage d'un message de succès/erreur
    header('Location: /pwd/login');
exit();
});

$router->map('GET', '/login', function() {
    require __DIR__ . '/View/login.php';
});

$router->map('GET', '/shop', function() {
    echo "Welcome to our shop!";
    require __DIR__ . '/View/shop.php';
});

// Correspondance et exécution de la route
$match = $router->match();

if(is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']); 
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo "404 Not Found";
}
