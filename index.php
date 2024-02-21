<?php
require_once __DIR__ . '/vendor/autoload.php';


$router = new AltoRouter();

$router->setBasePath('/pwd');

$router->map('GET', '/', function() {
    echo "Hello homepage";
});

// Route de la page de connexion
$router->map('GET', '/login', function() {
    // Assurez-vous que le chemin vers votre fichier de vue est correct.
    require __DIR__ . '/View/login.php';
});

// Route de la liste des produits
$router->map('GET', 'View/product', function() {
    echo "Hello products list";
});

// Route d'un produit spécifique par ID
$router->map('GET', 'View/product/[i:id]', function($id) {
    echo "Hello product with id : $id";
});

// Correspondance de la route actuelle et exécution du handler correspondant
$match = $router->match();

if(is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']); 
} else {
    // Si aucune route ne correspond, renvoyer une erreur 404
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo "404 Not Found";
}