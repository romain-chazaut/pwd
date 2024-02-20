<?php
require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirige l'utilisateur vers la page d'accueil s'il est déjà connecté
 */
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    if ($user->getState() == 1) {
        header('Location: ../index.php');
        exit();
    }
}
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" href="../public/assets/img/favicon.ico" type="image/x-icon">
        <title>Register</title>

        <link rel="stylesheet" href="../public/assets/css/style.css">

        <script defer src="../public/assets/js/togglePassword.js"></script>
    </head>
    <body>
        <div class="register-form_container">
            <h1>Register</h1>

            <form action="../src/Controller/AuthenticationController.php" method="post" class="register-form" name="register-form">
                <input type="hidden" name="form-name" value="register-form">

                <label for="fullname">Fullname</label>
                <input type="text" name="fullname" id="fullname" placeholder="Fullname" value="<?= (isset($_SESSION['old_inputs']) ? $_SESSION['old_inputs']['fullname'] : '') ?>" required>

                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Email" value="<?= (isset($_SESSION['old_inputs']) ? $_SESSION['old_inputs']['email'] : ''); unset($_SESSION['old_inputs']) ?>" required>

                <label for="password">Password</label>
                <input class="password" type="password" name="password" id="password" placeholder="Password" required>

                <label for="confirm-password">Confirm Password</label>
                <input class="password" type="password" name="confirm-password" id="confirm-password" placeholder="Confirm Password" required>

                <button type="button" id="toggle-password">Show</button>

                <button type="submit">Register</button>
            </form>

            <?php if (isset($_SESSION['errors'])) { ?>
                <div class="errors">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <p><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php } ?>

            <button class="login-button">
                <a href="login.php">Login</a>
            </button>
        </div>
    </body>
</html>
