<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use App\Controller\AdminController;
use App\Controller\AuthenticationController;
use App\Controller\CartController;
use App\Controller\ShopController;
use App\Model\Product;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = new AltoRouter();

$router->setBasePath('/pwd');

// Routes en GET
$router->map('GET', '/', function () {
    header('Location: View/shop.php');
    exit();
});

$router->map('GET', '/login', function () {
    header('Location: View/login.php');
    exit();
});

$router->map('GET', '/register', function () {
    header('Location: View/register.php');
    exit();
});

$router->map('GET', '/logout', function () {
    header('Location: View/logout.php');
    exit();
});

$router->map('GET', '/shop', function () {
    header('Location: View/shop.php');
    exit();
});

$router->map('GET', '/profile', function () {
    header('Location: View/profile.php');
    exit();
});

$router->map('GET', '/users', function () {
    header('Location: View/users.php');
    exit();
});


// Routes en POST
$router->map('POST', '/login', function () {
    /**
     * Vérifie s'il y a une requête POST
     * Instancie un objet AuthenticationController
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['form-name']) && $_POST['form-name'] === 'login-form') {
            $authentication = new AuthenticationController();
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);

            if ($authentication->login($email, $password)) {
                header("Location: View/shop.php");
            } else {
                header("Location: View/login.php");
            }
            exit;
        }
    }
});

$router->map('POST', '/register', function () {
    /**
     * Vérifie s'il y a une requête POST
     * Instancie un objet AuthenticationController
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['form-name']) && $_POST['form-name'] === 'register-form') {
            $authentication = new AuthenticationController();
            $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $confirmPassword = filter_input(INPUT_POST, 'confirm-password', FILTER_SANITIZE_STRING);

            if ($authentication->register($fullname, $email, $password, $confirmPassword)) {
                header("Location: View/login.php");
            } else {
                header("Location: View/register.php");
            }
            exit;
        }
    }
});

$router->map('POST', '/profile', function () {
    /**
     * Vérifie s'il y a une requête POST
     * Instancie un objet AuthenticationController
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['form-name']) && $_POST['form-name'] === 'profile-form') {
            $authentication = new AuthenticationController();
            $fullname = htmlspecialchars($_POST['fullname']);
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);
            $confirmPassword = htmlspecialchars($_POST["confirm-password"]);

            $authentication->updateProfile($fullname, $email, $password, $confirmPassword);
            header("Location: View/profile.php");
            exit;
        }
    }
});

$router->map('POST', '/user/remove', function () {
    /**
     * Vérifie s'il y a une requête POST
     * Instancie un objet AuthenticationController
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['form-name']) && $_POST['form-name'] === 'remove-user-form') {
            $admin = new AdminController();
            $id_product = htmlspecialchars($_POST['id_product']);

            $removeResponse = $admin->removeUser($id_product);
            $_SESSION['removeResponse'] = $removeResponse;

            header('Location: /pwd/users');
            exit();
        }
    }
});



$router->map('POST', '/shop', function () {
    /**
     * Vérifie s'il y a une requête POST
     * Instancie un objet ShopController
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['form-name']) && $_POST['form-name'] === 'show-product-form') {
            $shop = new ShopController(new Product());
            $id_product = htmlspecialchars($_POST['id_product']);
            $product_type = htmlspecialchars($_POST['product_type']);

            $product = $shop->showProduct($id_product, $product_type);
            if (gettype($product) !== 'string') {
                $_SESSION['product'] = $product;
            } else {
                $_SESSION['error'] = $product;
            }
            header('Location: View/shop.php');
            exit();
        }
    }
});

$router->map('POST', '/shop/remove', function () {
    /**
     * Vérifie s'il y a une requête POST
     * Instancie un objet ShopController
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['form-name']) && $_POST['form-name'] === 'remove-product-form') {
            $shop = new ShopController(new Product());
            $id_product = htmlspecialchars($_POST['id_product']);
            $product_type = htmlspecialchars($_POST['product_type']);


            $removeResponse = $shop->removeProduct($id_product, $product_type);
            $_SESSION['removeResponse'] = $removeResponse;

            header('Location: /pwd/shop');
            exit();
        }
    }
});

$router->map('POST', '/cart', function () {
    /**
     * Vérifie s'il y a une requête POST
     * Instancie un objet CartController
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['form-name']) && $_POST['form-name'] === 'add-cart-form') {
            $cart = new CartController();
            $product_id = htmlspecialchars($_POST['product_id']);

            if ($cart->addProductToCart($product_id)) {
                $_SESSION['success'] = "Produit ajouté au panier avec succès";
            } else {
                if (isset($_SESSION['user'])) {
                    $user = $_SESSION['user'];
                    if ($user->getState() == 1) {
                        $_SESSION['error'] = "Erreur lors de l'ajout du produit au panier";
                    } else {
                        $_SESSION['error'] = "Connectez-vous pour ajouter un produit au panier !";
                    }
                }
            }
            header('Location: View/shop.php');
            exit();
        }
    }
});

// Correspondance et exécution de la route
$match = $router->match();

if(is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']); 
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo "404 Not Found";
}
