<?php
use App\Model\User;
/**
 *  Déconnecte l'utilisateur s'il est connecté et redirige vers la page de connexion
 */

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    if ($user->getState() == 1) {
        $user->disconnect();
        header('Location: /pwd/login');
        exit();
    }
}