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
        <title>RAM SHOP - Login</title>

        <link rel="stylesheet" href="../public/assets/css/style.css">

        <script defer src="../public/assets/js/togglePassword.js"></script>
    </head>

    <body>
        <h1>Login</h1>

        <form action="../src/Controller/AuthenticationController.php" method="post" name="login-form">
            <input type="hidden" name="form-name" value="login-form">

            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Email" value="<?php echo isset($_SESSION['user']) ? $_SESSION['user']->getEmail() : (isset($_SESSION['old_inputs']) ? $_SESSION['old_inputs']['email'] : ''); unset($_SESSION['old_inputs']) ?>" required>

            <label for="password">Password</label>
            <input class="password" type="password" name="password" id="password" placeholder="Password" required>

            <button type="button" id="toggle-password">Show</button>

            <button type="submit">Login</button>
        </form>

        <?php if (isset($_SESSION['error'])) { ?>
            <p><?= $_SESSION['error'] ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php } ?>

        <button class="register-button">
            <a href="register.php">Register</a>
        </button>
    </body>
</html>
