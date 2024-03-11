<?php
namespace App\Controller;

use Dotenv\Dotenv;
use App\Model\User;

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * Class AdminController
 *
 * Gère le panel admin
 *
 * @package App\Controller
 */
class AdminController
{
    /**
     * Supprime un produit
     *
     * @param int $id_product
     * @return string|User
     */
    public function removeUser(int $id_product): string|User
    {
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            if ($user->getState() == 1 && $user->getRole()[0]== 'ROLE_ADMIN') {
                $user = new User();
                return $user->remove($id_product);
            } else {
                return "Vous n'êtes pas autorisé à supprimer un user";
            }
        } else {
            return "Vous n'êtes pas autorisé à supprimer un user";
        }
    }
}