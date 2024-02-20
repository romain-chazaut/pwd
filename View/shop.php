<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Initialisation de Dotenv pour charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    if ($user->getState() == 0 || $user->getState() == '') {
        $_SESSION['error'] = "Connectez-vous pour accéder au profil";
        header('Location: login.php');
        exit();
    }
}

// Redirige l'utilisateur vers la page de connexion s'il n'est pas connecté

$productModel = new \App\Model\Product(); // Assurez-vous que cette classe existe
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$products = $productModel->findPaginated($page, 5); // 5 produits par page
$totalProducts = $productModel->count(); // Méthode pour compter le total des produits

$totalPages = ceil($totalProducts / 5);

$user_id = $_SESSION['user']->getId();
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" href="../public/assets/img/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="../public/assets/css/style.css">

        <title>RAM SHOP - Boutique</title>
    </head>

    <body>
        <h1>Nos produits</h1>
        <button class="home-button">
            <a href="../index.php">home</a>
            <a href="">Cart</a>
        </button>
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nom</th>
                    <th>Photos</th>
                    <th>Prix</th>
                    <th>Description</th>
                    <th>Quantité</th>
                    <th>Catégorie</th>
                    <th>Date de création</th>
                    <th>Date de mise à jour</th>
                    <th>Afficher le produit</th>
                    <th>Ajouter au Panier</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($product['name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($product['photos'] ?? '') ?></td>
                    <td><?= htmlspecialchars($product['price'] ?? '') ?></td>
                    <td><?= htmlspecialchars($product['description'] ?? '') ?></td>
                    <td><?= htmlspecialchars($product['quantity'] ?? '') ?></td>
                    <td><?= htmlspecialchars($product['category_id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($product['created_at'] ?? '') ?></td>
                    <td><?= htmlspecialchars($product['updated_at'] ?? '') ?></td>
                    <td>
                        <form action="../src/Controller/ShopController.php" method="post">
                            <input type="hidden" name="form-name" value="show-product-form">
                            <input type="hidden" name="id_product" value="<?= $product['id'] ?>">
                            <input type="hidden" name="product_type" value="<?php if ($product['category_id'] == 1){echo 'clothing';}  else{echo 'electronic';} ?>">
                            <button class="display-button" type="submit">Afficher</button>
                        </form>
                    </td>
                    <td>
                        <form action="../src/Controller/CartController.php" method="post">
                            <input type="hidden" name="form-name" value="add-cart-form">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit">Ajouter</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="paginations">
            <div class="pagination pagination-left">
                <!-- Bouton Précédent -->
                <a class="pagination-button <?= ($page > 1) ? 'active' : '' ?>" href="?page=<?php if ($page > 1) { echo $page - 1; } ?>" aria-label="Previous">Précédent</a>
            </div>

            <div class="pagination-number">
                <!-- Liens des Numéros de Page -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a class="number-button <?= ($i == $page) ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>

            <div class="pagination pagination-right">
                <!-- Bouton Suivant -->
                <a class="pagination-button <?= ($page < $totalPages) ? 'active' : '' ?>" href="?page=<?php if ($page < $totalPages) { echo $page + 1; } ?>" aria-label="Next">Suivant</a>
            </div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <p><?= $_SESSION['error'] ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div>
            <?php if (isset($_SESSION['product'])) {
                $product = $_SESSION['product'];
                echo "<h2>Produit</h2>";
                echo "<p>Id: " . $product->getId() . "</p>";
                echo "<p>Nom: " . $product->getName() . "</p>";
                echo "<p>Photos: " . $product->getPhotos() . "</p>";
                echo "<p>Prix: " . $product->getPrice() . "</p>";
                echo "<p>Description: " . $product->getDescription() . "</p>";
                echo "<p>Quantité: " . $product->getQuantity() . "</p>";
                echo "<p>Catégorie: " . $product->getCategory()->getName() . "</p>";
                echo "<p>Date de création: " . $product->getCreatedAt() . "</p>";
                echo "<p>Date de mise à jour: " . $product->getUpdatedAt() . "</p>";
                unset($_SESSION['product']);
            } ?>
        </div>
    </body>
</html>
