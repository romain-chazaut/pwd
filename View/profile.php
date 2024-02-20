<?php
require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirige l'utilisateur vers la page de connexion s'il n'est pas connecté
 */
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    if ($user->getState() == 0 || $user->getState() == '') {
        $_SESSION['error'] = "Connectez-vous pour accéder au profil";
        header('Location: login.php');
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

        <title>RAM SHOP - Profile</title>
        <link rel="icon" href="../public/assets/img/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="../public/assets/css/style.css">

        <script defer src="../public/assets/js/togglePassword.js"></script>
    </head>

    <body>
        <h1>Profile</h1>

        <form action="../src/Controller/AuthenticationController.php" method="post" class="profile-form" name="profile-form">
            <input type="hidden" name="form-name" value="profile-form">

            <label for="fullname">Fullname</label>
            <input type="text" name="fullname" id="fullname" placeholder="Fullname" value="<?php echo isset($_SESSION['user']) ? $_SESSION['user']->getFullname() : (isset($_SESSION['old_inputs']) ? $_SESSION['old_inputs']['email'] : ''); unset($_SESSION['old_inputs']) ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Email" value="<?php echo isset($_SESSION['user']) ? $_SESSION['user']->getEmail() : (isset($_SESSION['old_inputs']) ? $_SESSION['old_inputs']['email'] : ''); unset($_SESSION['old_inputs']) ?>" required>

            <label for="password">Password</label>
            <input class="password" type="password" name="password" id="password" placeholder="Password" value="<?php echo isset($_SESSION['user']) ? $_SESSION['user']->getPassword() : (isset($_SESSION['old_inputs']) ? $_SESSION['old_inputs']['email'] : ''); unset($_SESSION['old_inputs']) ?>" required>

            <label for="confirm-password">Confirm Password</label>
            <input class="password" type="password" name="confirm-password" id="confirm-password" placeholder="Confirm Password" value="<?php echo isset($_SESSION['user']) ? $_SESSION['user']->getPassword() : (isset($_SESSION['old_inputs']) ? $_SESSION['old_inputs']['email'] : ''); unset($_SESSION['old_inputs']) ?>" required>

            <button type="button" id="toggle-password">Show</button>

            <button type="submit">Apply Change</button>
        </form>

        <?php if (isset($_SESSION['errors'])) { ?>
            <div class="errors">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php } ?>

        <button class="home-button">
            <a href="../index.php">Home</a>
        </button>
    </body>
</html>
