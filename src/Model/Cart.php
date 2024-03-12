<?php
namespace App\Model;

/**
 * Class Cart
 *
 * Image de la table cart de la base de données
 *
 * @package App\Model
 */
class Cart
{
    /**
     * @var int|null
     */
    private int|null $id = null;

    /**
     * @var int|null
     */
    private int|null $user_id = null;

    /**
     * @var int|null
     */
    private int|null $price = null;

    /**
     * Cart constructor.
     *
     * @param int|null $id
     * @param int|null $user_id
     * @param int|null $price
     */
    public function __construct(int|null $id = null, int|null $user_id = null, int|null $price = null)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->price = $price;
    }

    /**
     * Récupère un panier en fonction de l'id de l'utilisateur
     *
     * @param int $user_id
     * @return bool|Cart
     */
    public function findOneByUserId(int $user_id): bool|Cart
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->execute();

        $cart = $stmt->fetch();

        if ($cart) {
            $this->id = $cart['id'];
            $this->user_id = $cart['user_id'];
            $this->price = $cart['price'];
            return $this;
        }
        return false;
    }

    public function getProductsByCartId(int $cart_id): array
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $stmt = $pdo->prepare('SELECT * FROM product_cart WHERE cart_id = :cart_id');
        $stmt->bindValue(':cart_id', $cart_id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getCartProductQuantity(int $product_id, int $cart_id): int
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

        $stmt = $pdo->prepare("SELECT quantity FROM product_cart WHERE product_id = :product_id AND cart_id = :cart_id");
        $stmt->bindValue(':product_id', $product_id, \PDO::PARAM_INT); // Supposons que product_id soit un entier
        $stmt->bindValue(':cart_id', $cart_id, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchColumn();

        if ($result === false) {
            return false;
        } else {
            return $result;
        }

    }

    public function getPriceByCartId(int $cart_id): float
    {
        $products = $this->getProductsByCartId($cart_id);
        $price = 0;

        foreach ($products as $product) {
            $product_id = $product['product_id'];
            $product = $this->getProductById($product_id);
            if ($this->getCartProductQuantity($product_id, $cart_id) > 1) {
                $price += $product['price'] * $this->getCartProductQuantity($product_id, $cart_id);
            } else {
                $price += $product['price'];
            }
        }

        $this->setPrice($price);
        $this->update();

        return $price;
    }



    /**
     * Récupère la catégorie d'un produit en fonction de son id
     *
     * @param int $product_id
     * @return string|false
     */
    public function getCategoryByProductId(int $product_id): string|false
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

        $stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id');
        $stmt->bindValue(':id', $product_id, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            return false;
        }
        return $result['category_id'];
    }

    /**
     * Récupère un produit en fonction de son id
     *
     * @param int $product_id
     * @return array
     */
    public function getProductById(int $product_id): array
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id');
        $stmt->bindValue(':id', $product_id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Enregistre un panier dans la base de données
     *
     * @return bool
     */
    public function save(): bool
    {
        try {
            $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

            $stmt = $pdo->prepare("INSERT INTO cart (user_id, price) VALUES (:user_id, :price)");
            $stmt->bindValue(':user_id', $this->user_id, \PDO::PARAM_INT);
            $stmt->bindValue(':price', $this->price, \PDO::PARAM_INT);
            $stmt->execute();
            $this->id = $pdo->lastInsertId();

            return true; // Succès de l'opération d'insertion
        } catch (\PDOException $e) {
            // En cas d'erreur, affichage du message d'erreur
            echo "Erreur : " . $e->getMessage();
            return false; // Échec de l'opération d'insertion
        }
    }

    /**
     * Met à jour un panier dans la base de données
     *
     * @return bool
     */
    public function update(): bool
    {
        try {
            $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

            $stmt = $pdo->prepare("UPDATE cart SET price = :price WHERE id = :id");
            $stmt->bindValue(':price', $this->price, \PDO::PARAM_INT);
            $stmt->bindValue(':id', $this->id, \PDO::PARAM_INT);
            $stmt->execute();

            return true; // Succès de l'opération de mise à jour
        } catch (\PDOException $e) {
            // En cas d'erreur, affichage du message d'erreur
            echo "Erreur : " . $e->getMessage();
            return false; // Échec de l'opération de mise à jour
        }
    }

    public function updateQuantity(int $product_id, int $cart_id, int $quantity): bool
    {
        try {
            $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

            $stmt = $pdo->prepare("UPDATE product_cart SET quantity = :quantity WHERE product_id = :product_id AND cart_id = :cart_id");
            $stmt->bindValue(':quantity', $quantity, \PDO::PARAM_INT);
            $stmt->bindValue(':product_id', $product_id, \PDO::PARAM_INT);
            $stmt->bindValue(':cart_id', $cart_id, \PDO::PARAM_INT);
            $stmt->execute();

            return true; // Succès de l'opération de mise à jour
        } catch (\PDOException $e) {
            // En cas d'erreur, affichage du message d'erreur
            echo "Erreur : " . $e->getMessage();
            return false; // Échec de l'opération de mise à jour
        }
    }

    /**
     * Ajoute un produit au panier
     *
     * @param int $product_id
     * @param int $cart_id
     * @return bool
     */
    public function addProductToCart(int $product_id, int $cart_id): bool
    {
        try {
            $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

            $stmt = $pdo->prepare("INSERT INTO product_cart (product_id, cart_id, quantity) VALUES (:product_id, :cart_id, :quantity)");
            $stmt->bindValue(':product_id', $product_id, \PDO::PARAM_INT);
            $stmt->bindValue(':cart_id', $cart_id, \PDO::PARAM_INT);
            $stmt->bindValue(':quantity', 1, \PDO::PARAM_INT);
            $stmt->execute();

            return true; // Succès de l'opération d'insertion
        } catch (\PDOException $e) {
            // En cas d'erreur, affichage du message d'erreur
            echo "Erreur : " . $e->getMessage();
            return false; // Échec de l'opération d'insertion
        }
    }

    public function removeProductFromCart(int $product_id, int $cart_id): bool
    {
        try {
            $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

            $stmt = $pdo->prepare("DELETE FROM product_cart WHERE product_id = :product_id AND cart_id = :cart_id");
            $stmt->bindValue(':product_id', $product_id, \PDO::PARAM_INT);
            $stmt->bindValue(':cart_id', $cart_id, \PDO::PARAM_INT);
            $stmt->execute();

            return true; // Succès de l'opération de suppression
        } catch (\PDOException $e) {
            // En cas d'erreur, affichage du message d'erreur
            echo "Erreur : " . $e->getMessage();
            return false; // Échec de l'opération de suppression
        }
    }



    public function getId(): int|null
    {
        return $this->id;
    }
    public function getUserId(): int|null
    {
        return $this->user_id;
    }
    public function getPrice(): int|null
    {
        return $this->price;
    }

    public function setId(?int $id): Cart
    {
        $this->id = $id;
        return $this;
    }
    public function setUserId(?int $user_id): Cart
    {
        $this->user_id = $user_id;
        return $this;
    }
    public function setPrice(?int $price): Cart
    {
        $this->price = $price;
        return $this;
    }
}


