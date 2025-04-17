<?php

namespace classes;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    protected PDO $connexion;

    public function __construct()
    {
        $this->connexion = $this->getConnexion();
    }

    public function __destruct()
    {
        $this->getDisconnect();
    }

    protected function getConnexion(): PDO
    {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=Watchlist", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
        return $pdo;
    }

    protected function getDisconnect(): void
    {
        $this->connexion = null;
    }

    /* ===================================================================== Create user ===================================================================== */

    protected function passwordIsValid(string $password): bool
    {
        $regex = '/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])\S{8,}$/';
        if (preg_match($regex, $password) !== 1) {
            throw new \Exception("Invalid password : It must contain an uppercase letter, a digit, and a special character, and the length must be 8 characters.");
        }

        return true;
    }

    protected function usernameIsNotTaken(string $username): bool
    {
        $result = $this->showAllColumnValues("users", $username); //result is empty if the SQL request has no match.
        if (!empty($result)) {
            throw new \Exception("This username is already taken.");
        }

        return true;
    }

    protected function usernameIsValid(string $username): bool
    {
        $regex = '/^[A-Za-z][A-Za-z0-9_-]{2,}$/';
        if (preg_match($regex, $username) !== 1) {
            throw new \Exception("Invalid username : It must start with a letter and the length must be 3 characters.");
        }

        return true;
    }

    protected function realNameIsValid(string $realName): bool
    {
        $regex = '/^[\p{L}]+(?:-[\p{L}]+)*$/u';
        if (preg_match($regex, $realName) !== 1) {
            throw new \Exception("Invalid name format : It must be with only letter.");
        }

        return true;
    }

    protected function changeDateFormat(string $date): string
    {
        return date_format(date_create(str_replace('/', '-', $date)), 'Y-m-d');
    }

    protected function canCreateUser(string $firstname, string $lastname, string $username, string $password): bool
    {
        if ((!$this->realNameIsValid($firstname)) && (!$this->realNameIsValid($lastname))) {
            return false;
        }

        if (!$this->usernameIsNotTaken($username)) {
            return false;
        }

        if (!$this->usernameIsValid($username)) {
            return false;
        }

        if (!$this->passwordIsValid($password)) {
            return false;
        }

        return true;
    }

    public function changeUserToAdmin($username): void
    {
        $statement = $this->connexion->prepare("UPDATE users SET has_right = 1 WHERE username = :username");
        $statement->bindValue(":username", $username);
        $statement->execute();
    }

    public function userIsAdmin(string $username): bool
    {
        $statement = $this->connexion->prepare("SELECT has_right FROM users WHERE username = :username");
        $statement->bindValue(":username", $username);
        $statement->execute();

        return $statement->fetchColumn() == 1;
    }

    public function createUser(string $firstname, string $lastname, string $date, string $username, string $password): void
    {
        if (!$this->canCreateUser($firstname, $lastname, $username, $password)) {
            die();
        }

        $dataDate = $this->changeDateFormat($date);
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        $statement = $this->connexion->prepare("
            INSERT INTO users (firstname, lastname, date_of_birth, username, password, has_right) 
            VALUES (:firstname, :lastname, :date_of_birth,:username, :password, :hasRight)
            ");
        $statement->bindValue(':firstname', $firstname);
        $statement->bindValue(':lastname', $lastname);
        $statement->bindValue(':date_of_birth', $dataDate);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $hashPassword);
        $statement->bindValue(':hasRight', 0);
        $statement->execute();
    }

    /* ===================================================================== Log-In ===================================================================== */

    protected function usernameExist(string $username): bool
    {
        $statement = $this->connexion->prepare("SELECT username FROM users WHERE username = :username");
        $statement->bindValue(':username', $username);
        $statement->execute();

        return $statement->fetchColumn();
    }

    protected function getPasswordDatabase(string $username): string
    {
        if (!$this->usernameExist($username)) {
            throw new \Exception("Username does not exist.");
        }

        $statement = $this->connexion->prepare("SELECT password FROM users WHERE username = :username");
        $statement->bindValue(':username', $username);
        $statement->execute();

        return $statement->fetchColumn();
    }

    protected function passwordMatches(string $username, string $passwordSet): bool
    {
        $passwordHashed = $this->getPasswordDatabase($username);
        if (!password_verify($passwordHashed, $passwordSet)) {
            throw new \Exception("Invalid password.");
        }

        return true;
    }

    public function userCanLogin(string $username, string $password): bool
    {
        return $this->usernameExist($username) && $this->passwordMatches($username, $password);
    }

    /* ============================================================== Add movies to watchlist / add movies / delete movies ============================================================== */

    // This function may serve as a utility for the entire class.
    protected function getIdByValue(string $table, string $column, string $value): int
    {
        $statement = $this->connexion->prepare("SELECT id FROM :table WHERE :column = :value");
        $statement->bindValue(':table', $table);
        $statement->bindValue(':column', $column);
        $statement->bindValue(':value', $value);
        $statement->execute();
        $result = $statement->fetchColumn();
        if ($statement->fetchColumn() === false) {
            return 0;
        }

        return $result;
    }

    protected function movieExist(string $title): bool
    {
        $statement = $this->connexion->query("SELECT id FROM movies WHERE title = '$title'");
        if (empty($statement)) {
            throw new \Exception("Movie does not exist.");
        }

        return true;
    }

    protected function movieNotInWatchlist(string $title, string $username): bool
    {
        $movieId = $this->getIdByValue("movies", "title", $title);
        $userId = $this->getIdByValue("users", "username", $username);
        $statement = $this->connexion->query("
            SELECT movie_id 
            FROM watchlist 
            WHERE movie_id = $movieId 
            AND user_id = $userId
            ");

        if (!empty($statement)) {
            throw new \Exception("This movie is already in watchlist.");
        }

        return true;
    }

    protected function userCanAddMovieOnWatchlist(string $username, string $title): bool
    {
        if (!$this->movieExist($title)) {
            return false;
        }

        if (!$this->movieNotInWatchlist($username, $title)) {
            return false;
        }

        return true;
    }

    public function userAddMovieOnWatchlist(string $username, string $title): bool
    {
        if (!$this->userCanAddMovieOnWatchlist($username, $title)) {
            die();
        }

        $movieId = $this->getIdByValue("movies", "title", $title);
        $userId = $this->getIdByValue("users", "username", $username);

        $statement = $this->connexion->prepare("INSERT INTO watchlist (movie_id, user_id) VALUES (:title, :user)");
        $statement->bindValue(':title', $movieId);
        $statement->bindValue(':user', $userId);
        return $statement->execute();
    }

    public function userDeleteMovieOnWatchlist(string $username, string $title): void
    {
        $movieId = $this->getIdByValue("movies", "title", $title);
        $userId = $this->getIdByValue("users", "username", $username);

        $statement = $this->connexion->prepare("DELETE FROM watchlist WHERE movie_id = :movieid AND user_id = :userid");
        $statement->bindValue(':movieid', $movieId);
        $statement->bindValue(':userid', $userId);
        $statement->execute();
    }

    public function adminAddMovieOnDatabase(string $title, string $pictureUrl): bool
    {
        $statement = $this->connexion->prepare("INSERT INTO movies (title, picture_url) VALUES (:title, :pictureUrl)");
        $statement->bindValue(':title', $title);
        $statement->bindValue(':pictureUrl', $pictureUrl);
        return $statement->execute();
    }

    public function deleteMovie(string $title, string $username): void
    {
        $statement = $this->connexion->prepare("DELETE FROM movies WHERE title = :title");
        $statement->bindValue(':title', $title);
        $statement->execute();
    }

    /* ========================================================= Display watchlist / Movies / Tag / Type =========================================================== */

    public function displayAllTitleOrTag(string $table): array
    {
        $statement = $this->connexion->query("SELECT description FROM '$table'");
        return $statement->fetchAll(\PDO::FETCH_NUM);
    }

    public function displayMovie(string $value): array
    {
        $statement = $this->connexion->prepare("
            SELECT movies.title, movies.picture_url 
            FROM movies 
            WHERE id = :value
            ");
        $statement->bindValue(':value', $value);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_NUM);
    }

    public function showUserWatchlist(string $username): array
    {
        $userId = $this->getIdByValue("users", "username", $username);

        $statement = $this->connexion->prepare("
            SELECT movies.title, movies.picture_url 
            FROM movies 
            INNER JOIN watchlist ON movies.id = watchlist.movie_id 
            WHERE watchlist.user_id = :user_id
        ");
        $statement->bindValue(':user_id', $userId);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_NUM);
    }

    public function displaySpecificRequest(string $title, string $tag, string $type): array
    {
        $movieId = $this->getIdByValue("movies", "title", $title);
        $tagId = $this->getIdByValue("tag", "description", $tag);
        $typeId = $this->getIdByValue("type", "description", $type);

        $sql = "
        SELECT movies.title, movies.picture_url 
        FROM movies 
        INNER JOIN movie_tag ON movies.id = movie_tag.movie_id
        INNER JOIN movie_type ON movies.id = movie_type.movie_id
        WHERE 1=1
        ";

        if ($movieId != 0) {
            $sql .= " AND movies.ID LIKE :movieid";
        }

        if ($tagId != 0) {
            $sql .= " AND movie_tag.tag_ID LIKE :tagid";
        }

        if ($typeId != 0) {
            $sql .= " AND movie_type.type_ID LIKE :typeid";
        }

        $statement = $this->connexion->prepare($sql);
        $statement->bindValue(':movieid', $movieId);
        $statement->bindValue(':tagid', $tagId);
        $statement->bindValue(':typeid', $typeId);
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}