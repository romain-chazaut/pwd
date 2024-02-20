<?php
namespace App\Model;

/**
 * Class User
 *
 * Gère les utilisateurs
 *
 * @package App\Model
 */
class User
{
    /**
     * @var int|null L'id de l'utilisateur
     */
    private ?int $id = null;

    /**
     * @var string|null Le nom complet de l'utilisateur
     */
    private ?string $fullname = null;

    /**
     * @var string|null L'email de l'utilisateur
     */
    private ?string $email = null;

    /**
     * @var string|null Le mot de passe de l'utilisateur
     */
    private ?string $password = null;

    /**
     * @var array Les rôles de l'utilisateur
     */
    private array $role = [];

    /**
     * @var bool L'état de connexion de l'utilisateur
     */
    private bool $state = false;

    /**
     * User constructor.
     *
     * @param int|null $id
     * @param string|null $fullname
     * @param string|null $email
     * @param string|null $password
     * @param array $role
     * @param bool $state
     */
    public function __construct
    (
        ?int $id = null,
        ?string $fullname = null, ?
        string $email = null,
        ?string $password = null,
        array $role = [],
        bool $state = false
    )
    {
        $this->id = $id;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->state = $state;
    }

    /**
     * Récupère un utilisateur avec son id puis créer un objet User avec les données récupérées
     *
     * @param int $id
     * @return false|User
     */
    public function findOneById(int $id): false|User{
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $stmt = $pdo->prepare('SELECT * from user where id = :id');
        $stmt->bindParam(':id', $id,\PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result == false) {
            return false;
        }

        return new User(
            $result['id'],
            $result['fullname'],
            $result['email'],
            $result['password'],
            json_decode($result['role'], true)
        );
    }

    /**
     * Récupère un utilisateur avec son email puis crée un objet User avec les données récupérées
     *
     * @param string $email
     * @return false|User
     */
    function findOneByEmail(string $email) : false|User {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        return new User(
            $result['id'],
            $result['fullname'],
            $result['email'],
            $result['password'],
            json_decode($result['role'], true)
        );
    }

    /**
     * Récupère tous les utilisateurs puis crée un tableau d'objets User
     *
     * @return array
     */
    public function findAll(): array
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

        $statement = $pdo->prepare('SELECT * FROM user');
        $statement->execute();

        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $users = [];

        foreach ($results as $result) {
            $users[] = new User(
                $result['id'],
                $result['fullname'],
                $result['email'],
                $result['password'],
                json_decode($result['role'], true)
            );
        }

        return $users;
    }

    /**
     * Enregistre un nouvel utilisateur dans la base de données et le connect()
     *
     * @return User
     */
    public function create(): User
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

        $stmt = $pdo->prepare("INSERT INTO user (fullname, email, password, role) VALUES (:fullname, :email, :password, :role)");

        $stmt->bindValue(':fullname', $this->getFullname());
        $stmt->bindValue(':email', $this->getEmail());
        $stmt->bindValue(':password', $this->getPassword());
        $stmt->bindValue(':role', json_encode($this->getRole()));

        $stmt->execute();

        $this->setId((int)$pdo->lastInsertId());

        return $this;
    }

    /**
     * Met à jour les données de l'utilisateur dans la base de données
     *
     * @return User
     */
    public function update(): User
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

        $stmt = $pdo->prepare("UPDATE user SET fullname = :fullname, email = :email, password = :password, role = :role WHERE email = :email");

        $stmt->bindValue(':fullname', $this->getFullname());
        $stmt->bindValue(':email', $this->getEmail());
        $stmt->bindValue(':password', $this->getPassword());
        $stmt->bindValue(':role', json_encode($this->setRole(['ROLE_USER'])));

        $stmt->execute();

        $this->connect();
        $this->setId($this->getIdByEmail($_SESSION['user']->getEmail()));

        return $this;
    }

    /**
     * Récupère l'id d'un utilisateur avec son email
     *
     * @param string $email
     * @return int|bool
     */
    public function getIdByEmail(string $email): int|bool
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

        $stmt = $pdo->prepare("SELECT id FROM user WHERE email = :email");
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            return false;
        } else {
            return $result['id'];
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getFullname(): ?string
    {
        return $this->fullname;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function getRole(): array
    {
        return $this->role;
    }
    public function getState(): bool
    {
        return $this->state;
    }

    public function setId(?int $id): User
    {
        $this->id = $id;
        return $this;
    }
    public function setFullname(?string $fullname): User
    {
        $this->fullname = $fullname;
        return $this;
    }
    public function setEmail(?string $email): User
    {
        $this->email = $email;
        return $this;
    }
    public function setPassword(?string $password): User
    {
        $this->password = $password;
        return $this;
    }
    public function setRole(array $role): User
    {
        $this->role = $role;
        return $this;
    }

    public function connect(): User
    {
        $this->state = true;
        return $this;
    }
    public function disconnect(): User
    {
        $this->state = false;
        return $this;
    }
}