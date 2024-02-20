<?php

namespace App\Model;

use App\Model\Abstract\AbstractProduct;
use PDO;
use PDOException;

class Product extends AbstractProduct
{
//    private \PDO $pdo;
//
//    public function __construct()
//    {
//        parent::__construct();
//
//        try {
//            $this->pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
//            $this->
//        } catch (PDOException $e) {
//            // Dans une application réelle, vous pourriez vouloir logger cette erreur
//            // et afficher un message d'erreur générique à l'utilisateur.
//            error_log('Erreur de connexion à la base de données : ' . $e->getMessage());
//            die('Une erreur est survenue lors de la connexion à la base de données.');
//        }
//    }

    public function findPaginated(int $page, int $limit = 5): array
    {
        $offset = ($page - 1) * $limit;
        
        try {
            $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("SELECT * FROM product LIMIT :limit OFFSET :offset");
            // BindParam attend une référence de variable, donc utilisez bindValue ici
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des produits : ' . $e->getMessage());
            return []; // Retourne un tableau vide en cas d'erreur
        }
    }

    public function count(): int
    {
        try {
            $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->query("SELECT COUNT(*) FROM product");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Erreur lors du comptage des produits : ' . $e->getMessage());
            return 0; // Retourne 0 en cas d'erreur
        }
    }
}
