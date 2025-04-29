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
            $pdo = new PDO("mysql:host=localhost;port=4455;dbname=Watchlist", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
        return $pdo;
    }

    protected function getDisconnect(): void
    {
        unset($this->connexion);
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

    protected function showAllColumnValues(string $table, string $column,string $values): array
    {
        $statement = $this->connexion->prepare("SELECT * FROM $table WHERE $column = :values");
        $statement->BindValue(":values", $values);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function usernameIsNotTaken(string $username): bool
    {
        $result = $this->showAllColumnValues("users", "username", $username); //result is empty if the SQL request has no match.
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

    public function adminGetUser(): array
    {
        $statement = $this->connexion->query("SELECT username, has_right FROM users");
        return $statement->fetchAll();
    }

    public function changeUserRights($username, $isAdmin): void
    {
        $statement = $this->connexion->prepare("UPDATE users SET has_right = :is_admin WHERE username = :username");
        $statement->bindValue(":username", $username);
        $statement->bindValue(":is_admin", $isAdmin, PDO::PARAM_INT);
        $statement->execute();
    }

    public function userIsAdmin(string $username): bool
    {
        $statement = $this->connexion->prepare("SELECT has_right FROM users WHERE username = :username");
        $statement->bindValue(":username", $username);
        $statement->execute();

        return $statement->fetchColumn() == 1;
    }

    public function createUser(string $firstname, string $lastname,  string $username, string $password, string $date): void
    {
        if (!$this->canCreateUser($firstname, $lastname, $username, $password)) {
            return;
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
        if (!password_verify($passwordSet, $passwordHashed)) {
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
    public function getIdByValue(string $table, string $column, string $value): int
    {
        $statement = $this->connexion->prepare("SELECT id FROM `$table` WHERE `$column` = :value");
        $statement->bindValue(':value', $value);
        $statement->execute();
        $result = $statement->fetchColumn();

        if ($result === false) {
            return 0;
        }
        return $result;
    }

    protected function movieExist(string $title): bool
    {
        $statement = $this->connexion->prepare("SELECT id FROM movies WHERE title = :title");
        $statement->bindValue(':title', $title);
        $statement->execute();
        if (empty($statement)) {
            throw new \Exception("Movie does not exist.");
        }

        return true;
    }

    protected function movieNotInWatchlist(string $title, string $username): bool
    {
        $movieId = $this->getIdByValue("movies", "title", $title);
        $userId = $this->getIdByValue("users", "username", $username);
        $statement = $this->connexion->prepare("
            SELECT COUNT(*)
            FROM watchlist 
            WHERE movie_id = :movieId 
            AND user_id = :userId
            ");
        $statement->bindValue(':movieId', $movieId);
        $statement->bindValue(':userId', $userId);
        $statement->execute();

        if ($statement->fetchColumn() > 0) {
            throw new \Exception("This movie is already in watchlist.");
        }

        return true;
    }

    protected function userCanAddMovieOnWatchlist(string $username, string $title): bool
    {
        if (!$this->movieExist($title)) {
            return false;
        }

        if (!$this->movieNotInWatchlist($title, $username)) {
            return false;
        }

        return true;
    }

    public function userAddMovieOnWatchlist(string $username, string $title): void
    {
        if ($this->userCanAddMovieOnWatchlist($username, $title)) {
            $userId = $this->getIdByValue("users", "username", $username);
            $titleId = $this->getIdByValue("movies", "title", $title);

            $statement = $this->connexion->prepare("INSERT INTO watchlist (movie_id, user_id) VALUES (:movieId, :userId)");
            $statement->bindValue(':movieId', $titleId);
            $statement->bindValue(':userId', $userId);
            $statement->execute();
        }
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

    public function showAllTypeOrTag(string $table): array
    {
        $statement = $this->connexion->query("SELECT description FROM `$table`");
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
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
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function displaySpecificRequest(string $title, array $tags, array $types): array
    {
        $movieId = $this->getIdByValue("movies", "title", $title);

        $tagIds = [];
        foreach ($tags as $tag) {
            $id = $this->getIdByValue("tag", "description", $tag);
            $tagIds[] = $id;
        }

        $typeIds = [];
        foreach ($types as $type) {
            $id = $this->getIdByValue("type", "description", $type);
            $typeIds[] = $id;
        }

        $sql = "
        SELECT DISTINCT movies.title, movies.picture_url 
        FROM movies 
        INNER JOIN movie_tag ON movies.id = movie_tag.movie_id
        INNER JOIN movie_type ON movies.id = movie_type.movie_id
        WHERE 1=1
        ";

        if ($movieId != 0) {

            $sql .= " AND movies.ID LIKE :movieid";
        }

        if (!empty($tagIds)) {
            $placeholders = [];
            foreach ($tagIds as $realId => $id) {
                $placeholders[] = ":tagid$realId";
            }
            $sql .= " AND movie_tag.tag_ID IN (" . implode(', ', $placeholders) . ")";
        }

        if (!empty($typeIds)) {
            $placeholders = [];
            foreach ($typeIds as $realId => $id) {
                $placeholders[] = ":typeid$realId";
            }
            $sql .= " AND movie_type.type_ID IN (" . implode(', ', $placeholders) . ")";
        }

        $statement = $this->connexion->prepare($sql);

        if ($movieId != 0) {
            $statement->bindValue(':movieid', $movieId);
        }

        foreach ($tagIds as $realId => $id) {
            $statement->bindValue(":tagid$realId", $id);
        }

        foreach ($typeIds as $realId => $id) {
            $statement->bindValue(":typeid$realId", $id);
        }

        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}