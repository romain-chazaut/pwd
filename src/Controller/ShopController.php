<?php
namespace App\Controller;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\Clothing;
use App\Model\Electronic;
use App\Model\Product;
use Dotenv\Dotenv;

if (!isset($_SESSION)) {
    session_start();
}

/**
 * Class ShopController
 *
 * Gère les actions liées à la boutique
 *
 * @package App\Controller
 */
class ShopController
{
    /**
     * @var Product
     */
    private $productModel;

    /**
     * ShopController constructor.
     *
     * @param Product $productModel
     */
    public function __construct(Product $productModel)
    {
        // Assurez-vous que Dotenv est nécessaire ici. Si votre application charge Dotenv ailleurs,
        // par exemple, dans un fichier bootstrap ou index.php, cette ligne peut être redondante.
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();

        $this->productModel = $productModel;
    }

    /**
     * Affiche la liste des produits
     *
     * @param int $page
     */
    public function index($page = 1)
    {
        // Validation du numéro de page
        if (!filter_var($page, FILTER_VALIDATE_INT) || $page < 1) {
            $page = 1;
        }

        // Supposons que findPaginated retourne un tableau de produits et potentiellement
        // des informations de pagination (comme le nombre total de pages).
        $paginationResult = $this->productModel->findPaginated($page);
        $products = $paginationResult['product'];
        $totalPages = $paginationResult['totalPages']; // Assurez-vous que votre modèle retourne cette information.

        // Passer les produits et les informations de pagination à la vue.
        // Vous pouvez également passer d'autres données nécessaires pour le rendu de la vue.
        require __DIR__ . '/../../views/shop.php';
    }

    /**
     * Affiche un produit
     *
     * @param int $id_product
     * @param string $product_type
     * @return mixed
     */
    public function showProduct(int $id_product, string $product_type)
    {
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            if ($user->getState() == 1) {
                if ($product_type == 'electronic') {
                    $electronic = new Electronic();
                    $result = $electronic->findOneById($id_product);
                    if ($result !== false) {
                        return $result;
                    } else {
                        return false;
                    }
                } elseif ($product_type == 'clothing') {
                    $clothing = new Clothing();
                    $result = $clothing->findOneById($id_product);
                    if ($result !== false) {
                        return $result;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                $_SESSION['error'] = "Connectez-vous pour accéder au profil";
                header('Location: ../../View/login.php');
                return false;
            }
        }else {
            $_SESSION['error'] = "Connectez-vous pour accéder au profil";
            header('Location: ../../View/login.php');
            return false;
        }
    }
}

/**
 * Vérifie s'il y a une requête POST
 * Instancie un objet ShopController
 * Appelle la méthode nécessaire en fonction de la valeur de $_POST['form-name']
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop = new ShopController(new Product());

    if (isset($_POST['form-name']) && $_POST['form-name'] === 'show-product-form') {
        $id_product = htmlspecialchars($_POST['id_product']);
        $product_type = htmlspecialchars($_POST['product_type']);

        $product = $shop->showProduct($id_product, $product_type);
        if ($product !== false) {
            $_SESSION['product'] = $product;
        } else {
            $_SESSION['error'] = 'Le produit n\'existe pas';
        }
        header('Location: ../../View/shop.php');
    }
}
