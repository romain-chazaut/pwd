<?php
use App\Model\Cart;

// Redirige l'utilisateur vers la page de connexion s'il n'est pas connecté
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    if ($user->getState() == 0 || $user->getState() == '') {
        $_SESSION['error'] = "Connectez-vous pour accéder au profil";
        header('Location: login.php');
        exit();
    }
}

$user = $_SESSION['user'];
$cart = new Cart();
$cart_id = $cart->findOneByUserId($user->getId())->getId();
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <link rel="stylesheet" href="public/assets/css/style.css">

        <title>RAM SHOP</title>
    </head>

    <body>
        <h1>Mon Panier</h1>

        <button class="home-button">
            <a href="/pwd/user/profile">Profile</a>
        </button>
        <button class="home-button">
            <a href="/pwd/shop">Shop</a>
        </button>

        <?php if (isset($_SESSION['user'])) {
            if ($_SESSION['user']->getRole()[0] === 'ROLE_ADMIN'): ?>
                <button class="home-button">
                    <a href="/pwd/admin/users">Utilisateurs</a>
                </button>
            <?php endif;
        } ?>
        <table>
            <thead>
            <tr>
                <th>Id</th>
                <th>Nom</th>
                <th>Photos</th>
                <th>Prix</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Supprimer du panier</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($user->getCart() as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($product['name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($product['photos'] ?? '') ?></td>
                    <td><?= htmlspecialchars($product['price'] ?? '') ?></td>
                    <td><?= htmlspecialchars($product['description'] ?? '') ?></td>
                    <td>
                        <form action="/pwd/cart/edit" method="post">
                            <input type="hidden" name="form-name" value="edit-cart-product_quantity-form">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="cart_id" value="<?= $cart_id ?>">
                            <input type="number" name="quantity" value="<?= $cart->getCartProductQuantity($product['id'], $cart_id) ?>">
                            <button type="submit">Modifier</button>
                        </form>
                    </td>


                    <td>
                        <form action="/pwd/cart/remove" method="post">
                            <input type="hidden" name="form-name" value="remove-cart-product-form">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="cart_id" value="<?= $cart_id ?>">
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <p>Prix total : <?= $cart->getPriceByCartId($cart_id) ?> €</p>

        <?php if (isset($_SESSION['error'])): ?>
            <p><?= $_SESSION['error'] ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </body>
</html>
