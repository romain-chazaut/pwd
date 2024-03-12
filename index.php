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

$router->map('GET', '/', function () {
    include_once 'View/shop.php';
    exit();
});

$router->map('GET', '/login', function () {
    include_once 'View/login.php';
    exit();
});

$router->map('GET', '/logout', function () {
    include_once 'View/logout.php';
    exit();
});

$router->map('POST', '/login', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['form-name']) && $_POST['form-name'] === 'login-form') {
            $authentication = new AuthenticationController();
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);

            if ($authentication->login($email, $password)) {
                header("Location: /pwd/shop");
            } else {
                header("Location: /pwd/login");
            }
            exit();
        }
    }
});

$router->map('GET', '/register', function () {
    include_once 'View/register.php';
    exit();
});

$router->map('POST', '/register', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['form-name']) && $_POST['form-name'] === 'register-form') {
            $authentication = new AuthenticationController();
            $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $confirmPassword = filter_input(INPUT_POST, 'confirm-password', FILTER_SANITIZE_STRING);

            if ($authentication->register($fullname, $email, $password, $confirmPassword)) {
                header("Location: /pwd/login");
            } else {
                header("Location: /pwd/register");
            }
            exit();
        }
    }
});

$router->map('GET', '/shop', function () {
    include_once 'View/shop.php';
    exit();
});

$router->map('POST', '/shop/product', function () {
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
            header('Location: /pwd/shop');
            exit();
        }
    }
});

$router->map('POST', '/shop/remove', function () {
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

$router->map('GET', '/cart', function () {
    include_once 'View/cart.php';
    exit();
});

$router->map('POST', '/cart/add', function () {
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
            header('Location: /pwd/shop');
            exit();
        }
    }
});

$router->map('POST', '/cart/edit', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['form-name']) && $_POST['form-name'] === 'edit-cart-product_quantity-form') {
            $cart = new CartController();
            $product_id = htmlspecialchars($_POST['product_id']);
            $cart_id = htmlspecialchars($_POST['cart_id']);
            $quantity = htmlspecialchars($_POST['quantity']);

            if ($cart->updateQuantity($product_id, $cart_id, $quantity)) {
                $_SESSION['updateResponse'] = "Quantité modifiée avec succès";
            } else {
                $_SESSION['updateResponse'] = "Erreur lors de la modification de la quantité";
            }

            header('Location: /pwd/cart');
            exit();
        }
    }
});

$router->map('POST', '/cart/remove', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['form-name']) && $_POST['form-name'] === 'remove-cart-product-form') {
            $cart = new CartController();
            $product_id = htmlspecialchars($_POST['product_id']);
            $cart_id = htmlspecialchars($_POST['cart_id']);

            if ($cart->deleteProduct($product_id, $cart_id)) {
                $_SESSION['removeResponse'] = "Produit supprimé avec succès";
            } else {
                $_SESSION['removeResponse'] = "Erreur lors de la suppression du produit";
            }

            header('Location: /pwd/cart');
            exit();
        }
    }
});

$router->map('GET', '/user/profile', function () {
    include_once 'View/profile.php';
    exit();
});

$router->map('POST', '/user/profile/edit', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['form-name']) && $_POST['form-name'] === 'profile-form') {
            $authentication = new AuthenticationController();
            $fullname = htmlspecialchars($_POST['fullname']);
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);
            $confirmPassword = htmlspecialchars($_POST["confirm-password"]);

            $authentication->updateProfile($fullname, $email, $password, $confirmPassword);
            header("Location: /pwd/user/profile");
            exit();
        }
    }
});

$router->map('GET', '/admin/users', function () {
    include_once 'View/admin/users.php';
    exit();
});

$router->map('POST', '/admin/user/remove', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['form-name']) && $_POST['form-name'] === 'remove-user-form') {
            $admin = new AdminController();
            $id_product = htmlspecialchars($_POST['id_product']);

            $removeResponse = $admin->removeUser($id_product);
            $_SESSION['removeResponse'] = $removeResponse;

            header('Location: /pwd/admin/users');
            exit();
        }
    }
});

$match = $router->match();

if (is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo "404 Not Found";
}




