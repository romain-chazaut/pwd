<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" href="./public/assets/img/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="./public/assets/css/style.css">
        <title>Bienvenue</title>
    </head>

    <body>
        <h1>Bienvenue</h1>
        <div class="welcome-button">
          <button class="shop-button">
            <a href="View/shop.php">Shop</a>
        </button>

        <button class="profile-button">
            <a href="View/profile.php">Profile</a>
        </button>

        <button class="logout-button">
            <a href="View/logout.php">Logout</a>
        </button>  
        </div>
        

        <?php if (isset($_SESSION['success'])) { ?>
            <p><?= $_SESSION['success']?></p>
            <?php unset($_SESSION['success']); ?>
        <?php } ?>
    </body>
</html>
