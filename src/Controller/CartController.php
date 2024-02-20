<?php
namespace App\Controller;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\Clothing;
use App\Model\Electronic;
use App\Model\Cart;
use App\Model\User;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Class CartController
 *
 * Gère les actions liées au panier
 *
 * @package App\Controller
 */
class CartController
{
    /**
     * Ajoute un produit au panier
     * Récupère la catégorie avec l'id du produit
     * Créer un objet product en fonction de la catégorie
     * Vérifie si l'user à un panier puis le mets à jour ou le crée avec le prix du produit
     * Appelle la fonction addProductToCart() avec l'id du produit et l'id du panier
     *
     * @param int $product_id
     * @return bool
     */
    public function addProductToCart(int $product_id): bool
    {
        $user = $_SESSION['user'];
        $user_id = $user->getId();

        if (!$user->getState()) {
            $_SESSION['error'] = "Connectez-vous pour ajouter un produit au panier !";
            header('Location: ../../View/login.php');
            exit();
        }

        $cart = new Cart();
        $product = $cart->getCategoryByProductId($product_id);
        // Récupération du produit
        if ($product == 1) {
            $product = new Clothing();
            $product = $product->findOneById($product_id);
        } else if ($product == 2) {
            $product = new Electronic();
            $product = $product->findOneById($product_id);
        }

        if ($cart->findOneByUserId($user_id) !== false) {
            $cart = $cart->findOneByUserId($user_id);
            $cart->setPrice($cart->getPrice() + $product->getPrice());
            $cart->update();
        }else{
            $cart->setUserId($user_id)->setPrice($product->getPrice());
            $cart->save();
        }
        if ($cart->addProductToCart($product_id, $cart->getId())) {
            return true;
        } else {
            return false;
        }
    }

}

/**
 * Vérifie s'il y a une requête POST
 * Instancie un objet CartController
 * Appelle la méthode nécessaire en fonction de la valeur de $_POST['form-name']
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart = new CartController();

    if (isset($_POST['form-name']) && $_POST['form-name'] === 'add-cart-form') {
        $product_id = htmlspecialchars($_POST['product_id']);

        if ($cart->addProductToCart($product_id)) {
            $_SESSION['success'] = "Produit ajouté au panier avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout du produit au panier";
        }
        header('Location: ../../View/shop.php');
    }
}
