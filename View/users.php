<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\User;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$users = new User();
$users = $users->findAll();
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
        <h1>Utilisateurs</h1>

        <button class="home-button">
            <a href="/pwd/profile">Profile</a>
        </button>

        <button class="home-button">
            <a href="/pwd/shop">Shop</a>
        </button>

        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Fullname</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Supprimer un user</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user->getId() ?? '') ?></td>
                    <td><?= htmlspecialchars($user->getFullname() ?? '') ?></td>
                    <td><?= htmlspecialchars($user->getEmail() ?? '') ?></td>
                    <td><?= htmlspecialchars($user->getPassword() ?? '') ?></td>
                    <td><?= htmlspecialchars($user->getRole()[0] ?? '') ?></td>
                    <td>
                        <form action="/pwd/user/remove" method="post">
                            <input type="hidden" name="form-name" value="remove-user-form">
                            <input type="hidden" name="id_product" value="<?= $user->getId() ?>">
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (isset($_SESSION['error'])): ?>
            <p><?= $_SESSION['error'] ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['removeResponse'])): ?>
            <p><?= $_SESSION['removeResponse'] ?></p>
            <?php unset($_SESSION['removeResponse']); ?>
        <?php endif; ?>
    </body>
</html>
