<?php
require_once '../vendor/autoload.php';

use App\Model\User;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 *  Déconnecte l'utilisateur s'il est connecté et redirige vers la page de connexion
 */

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    if ($user->getState() == 1) {
        $user->disconnect();
        header('Location: login.php');
        exit();
    }
}