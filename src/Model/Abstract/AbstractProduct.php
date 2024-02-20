<?php

namespace App\Model\Abstract;

use App\Model\Category;
use DateTime;
use Exception;

/**
 * Class AbstractProduct
 * @package App\Model\Abstract
 */
abstract class AbstractProduct
{

    /**
     * @var int|null
     */
    protected ?int $id = null;

    /**
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * @var array|null|string
     */
    protected null|array|string $photos = null;

    /**
     * @var int|null
     */
    protected ?int $price = null;

    /**
     * @var string|null
     */
    protected ?string $description = null;

    /**
     * @var int|null
     */
    protected ?int $quantity = null;

    /**
     * @var int|null
     */
    protected ?int $category_id = null;

    /**
     * @var DateTime|null
     */
    protected ?\DateTime $createdAt = null;

    /**
     * @var DateTime|null
     */
    protected ?\DateTime $updatedAt = null;

    /**
     * AbstractProduct constructor.
     *
     * @param int|null $id
     * @param string|null $name
     * @param array|null $photos
     * @param int|null $price
     * @param string|null $description
     * @param int|null $quantity
     * @param int|null $category_id
     * @param DateTime|null $createdAt
     * @param DateTime|null $updatedAt
     */
    public function __construct(
        ?int       $id = null,
        ?string    $name = null,
        ?array     $photos = null,
        ?int       $price = null,
        ?string    $description = null,
        ?int       $quantity = null,
        ?int       $category_id = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->photos = $photos;
        $this->price = $price;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->category_id = $category_id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param int $id
     * @return static|false
     * @throws Exception
     */
    public function findOneById(int $id): static|false
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $sql = "SELECT * FROM product WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $product = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($product) {
            return new static(
                $product['id'],
                $product['name'],
                json_decode($product['photos']),
                $product['price'],
                $product['description'],
                $product['quantity'],
                $product['category_id'],
                new \DateTime($product['created_at']),
                $product['updated_at'] ? (new \DateTime($product['updated_at'])) : null
            );
        }

        return false;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function findAll(): array
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $sql = "SELECT * FROM product";
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $products = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $results = [];
        foreach ($products as $product) {
            $results[] = new static(
                $product['id'],
                $product['name'],
                json_decode($product['photos']),
                $product['price'],
                $product['description'],
                $product['quantity'],
                $product['category_id'],
                new \DateTime($product['created_at']),
                $product['updated_at'] ? (new \DateTime($product['updated_at'])) : null
            );
        }

        return $results;
    }

    /**
     * @return static
     * @throws Exception
     */
    public function create(): static
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $sql = "INSERT INTO product (name, photos, price, description, quantity, category_id, created_at, updated_at) VALUES (:name, :photos, :price, :description, :quantity, :category_id, :created_at, :updated_at)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':name', $this->name);
        $statement->bindValue(':photos', json_encode($this->photos));
        $statement->bindValue(':price', $this->price);
        $statement->bindValue(':description', $this->description);
        $statement->bindValue(':quantity', $this->quantity);
        $statement->bindValue(':category_id', $this->category_id);
        $statement->bindValue(':created_at', $this->createdAt->format('Y-m-d H:i:s'));
        $statement->bindValue(':updated_at', $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null);
        $statement->execute();
        $this->id = $pdo->lastInsertId();
        return $this;
    }

    /**
     * @return static
     * @throws Exception
     */
    public function update(): static
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $sql = "UPDATE product SET name = :name, photos = :photos, price = :price, description = :description, quantity = :quantity, category_id = :category_id, updated_at = :updated_at WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':id', $this->id);
        $statement->bindValue(':name', $this->name);
        $statement->bindValue(':photos', json_encode($this->photos));
        $statement->bindValue(':price', $this->price);
        $statement->bindValue(':description', $this->description);
        $statement->bindValue(':quantity', $this->quantity);
        $statement->bindValue(':category_id', $this->category_id);
        $statement->bindValue(':updated_at', (new \DateTime())->format('Y-m-d H:i:s'));
        $statement->execute();
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): AbstractProduct
    {
        $this->id = $id;
        return $this;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(?string $name): AbstractProduct
    {
        $this->name = $name;
        return $this;
    }
    public function getPhotos(): ?string
    {
        if (is_string($this->photos)) {
            return $this->photos;
        } elseif (is_array($this->photos)){
            return implode(', ', $this->photos);
        } else {
            return 'Pas de photos';
        }
    }
    public function setPhotos(?array $photos): AbstractProduct
    {
        $this->photos = $photos;
        return $this;
    }
    public function getPrice(): ?int
    {
        return $this->price;
    }
    public function setPrice(?int $price): AbstractProduct
    {
        $this->price = $price;
        return $this;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): AbstractProduct
    {
        $this->description = $description;
        return $this;
    }
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }
    public function setQuantity(?int $quantity): AbstractProduct
    {
        $this->quantity = $quantity;
        return $this;
    }
    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }
    public function setCategoryId(?int $category_id): AbstractProduct
    {
        $this->category_id = $category_id;
        return $this;
    }
    public function getCreatedAt(): string|DateTime
    {
        if ($this->createdAt instanceof \DateTime) {
            return $this->createdAt->format('Y-m-d H:i:s');
        } else {
            return 'ya problème chef';
        }
    }
    public function setCreatedAt(?\DateTime $createdAt): AbstractProduct
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    public function getUpdatedAt(): string|DateTime
    {
        if ($this->updatedAt instanceof \DateTime) {
            return $this->updatedAt->format('Y-m-d H:i:s');
        } else {
            return 'Pas de date de mise à jour';
        }
    }
    public function setUpdatedAt(?\DateTime $updatedAt): AbstractProduct
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    public function getCategory(): Category|false
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $sql = "SELECT * FROM category WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':id', $this->category_id);
        $statement->execute();
        $category = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($category) {
            return new Category(
                $category['id'],
                $category['name'],
                $category['description'],
                new \DateTime($category['created_at']),
                $category['updated_at'] ? (new \DateTime($category['updated_at'])) : null
            );
        }

        return false;
    }
}