<?php
namespace App\Controller;

require_once '../../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Model\User;

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * Class AuthenticationController
 *
 * Gère l'authentification des utilisateurs
 *
 * @package App\Controller
 */
class AuthenticationController
{
    /**
     * Vérifie les informations d'inscription et de mise à jour d'un utilisateur
     * et retourne un tableau erreurs
     *
     * @param User $user
     * @param string $email
     * @param string $password
     * @param string $confirmPassword
     * @return array
     */
    public function verifyLoginCredentials(User $user, string $email, string $password, string $confirmPassword): array
    {
        $errors = [];

        if ($user->findOneByEmail($email) !== false && $user->findOneByEmail($email)->getId() !== $_SESSION['user']->getId()) {
            $errors[] = "Cet email est déjà utilisé. Veuillez en choisir un autre.";
        }

        $passwordPattern = "/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_-])[A-Za-z\d@$!%*?&_-]{8,}$/";
        if (!preg_match($passwordPattern, $password)) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.";
        }

        if ($password !== $confirmPassword) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        return $errors;
    }

    /**
     * Créer un objet User puis l'insère dans la base de données avec create()
     *
     * @param string $fullname
     * @param string $email
     * @param string $password
     * @param string $confirmPassword
     * @return bool
     */
    public function register(string $fullname, string $email, string $password, string $confirmPassword): bool
    {
        $user = new User();

        $errors = $this->verifyLoginCredentials($user, $email, $password, $confirmPassword);

        if (empty($errors)) {
            unset($_SESSION['errors']);
            $_SESSION['success'] = "Votre compte a bien été créé.";

            $user->setFullname($fullname);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setRole(['ROLE_USER']);

            $user->create();

            $_SESSION['user'] = $user;

            return true;
        }else {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_inputs'] = $_POST;

            return false;
        }
    }

    /**
     * Récupère un utilisateur avec son email puis vérifie si le mot de passe correspond
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login(string $email, string $password): bool
    {
        $user = new User();
        $user = $user->findOneByEmail($email);
        if ($user === false) {
            $_SESSION['error'] = "Les identifiants fournis ne correspondent à aucun utilisateur";
            $_SESSION['old_inputs'] = $_POST;

            return false;
        }else {
            $userpassword = $user->getPassword();
            if ($userpassword == $password) {
                $_SESSION['success'] = "Vous êtes connecté.";

                $user->connect();

                $_SESSION['user'] = $user;

                return true;
            } else {
                $_SESSION['error'] = "Les identifiants fournis ne correspondent à aucun utilisateur";
                $_SESSION['old_inputs'] = $_POST;

                return false;
            }
        }
    }

    /**
     * Créer un objet User puis le met à jour dans la base de données avec update()
     *
     * @param string $fullname
     * @param string $email
     * @param string $password
     * @param string $confirmPassword
     * @return bool
     */
    public function updateProfile(string $fullname, string $email, string $password, string $confirmPassword): bool
    {
        $user = new User();

        $errors = $this->verifyLoginCredentials($user, $email, $password, $confirmPassword);

        if (empty($errors)) {
            unset($_SESSION['errors']);

            $user->setFullname($fullname);
            $user->setEmail($email);
            $user->setPassword($password);

            $user->update();

            $_SESSION['user'] = $user;

            return true;
        }else {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_inputs'] = $_POST;

            return false;
        }
    }
}

/**
 * Vérifie s'il y a une requête POST
 * Instancie un objet AuthenticationController
 * Appelle la méthode nécessaire en fonction de la valeur de $_POST['form-name']
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authentication = new AuthenticationController();

    if (isset($_POST['form-name']) && $_POST['form-name'] === 'register-form') {
        $fullname = htmlspecialchars($_POST['fullname']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        $confirmPassword = htmlspecialchars($_POST["confirm-password"]);

        if ($authentication->register($fullname, $email, $password, $confirmPassword)) {
            header("Location: ../../View/login.php");
        }else {
            header("Location: ../../View/register.php");
        }
        exit;
    }


    if (isset($_POST['form-name']) && $_POST['form-name'] === 'login-form') {
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);

        if ($authentication->login($email, $password)) {
            header("Location: ../../index.php");
        }else {
            header("Location: ../../View/login.php");
        }
        exit;
    }

    if (isset($_POST['form-name']) && $_POST['form-name'] === 'profile-form') {
        $fullname = htmlspecialchars($_POST['fullname']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        $confirmPassword = htmlspecialchars($_POST["confirm-password"]);

        $authentication->updateProfile($fullname, $email, $password, $confirmPassword);
        header("Location: ../../View/profile.php");
        exit;
    }

}