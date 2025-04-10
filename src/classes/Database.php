<?php

namespace classes;

use PDO;
use PDOException;

class Database
{
    protected PDO $connexion;
    private const array ALLOWED_TABLE = ['movies', 'users', 'tags', 'type'];
    private const array ALLOWED_COLUMN = ['id', 'title', 'username', 'tag_name', 'type_name'];

    public function __construct(){
        $this->connexion = $this->getConnexion();
    }

    protected function getConnexion(): PDO
    {
        try{
            $pdo = new PDO("mysql:host=localhost;dbname=Watchlist", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
        return $pdo;
    }

/* ===================================================================== For Create user ===================================================================== */

    private function passwordIsValid(string $password): bool
    {
        $regex = '/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])\S{8,}$/';
        if (preg_match($regex, $password) !== 1){
            throw new \Exception("Invalid password : It must contain an uppercase letter, a digit, and a special character, and the length must be 8 characters.");
        }

        return true;
    }

    private function passwordMatches(string $passwordHashed, string $passwordSet): bool
    {
        return password_verify($passwordHashed, $passwordSet);
    }
    //Le hashage est unique, chaque hashage d'un même "password" va être différent.
    //Pour le formulaire de connexion, on va devoir vérifier le mdp hashé de la bdd avec le passwd_verify de l'utilisateur.
    //Il me faut simplement récupérer le mot de passe, le mettre dans la fonction et faire le match.

    private function usernameIsNotTaken(string $username): bool
    {
        $result = $this->connexion->query("SELECT '$username' FROM users"); //result is empty if the SQL request has no match.
        if (!empty($result)) {
            throw new \Exception("This username is already taken.");
        }

        return true;
    }

    private function usernameIsValid(string $username): bool
    {
        $regex = '/^[A-Za-z][A-Za-z0-9_-]{2,}$/';
        if (preg_match($regex, $username) !== 1){
            throw new \Exception("Invalid username : It must start with a letter and the length must be 3 characters.");
        }

        return true;
    }

    private function canCreateUser(string $username, string $password): bool
    {
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

    protected function createUser(string $username, string $password): void
    {
        if (!$this->canCreateUser($username, $password)) {
            die();
        }

        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        $statement = $this->connexion->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $hashPassword);
        $statement->execute();
    }

    /* ===================================================================== For add movies ===================================================================== */

    // This function will serve as a utility for the entire class.
    private function getIdByValue(string $table, string $column, string $value): int
    {
        $statement = $this->connexion->prepare("SELECT id FROM '$table' WHERE '$column' = :value");
        $statement->bindValue(':value', $value);
        $statement->execute();
        return $statement->fetchColumn();
    }

    private function movieExist(string $title): bool
    {
        $statement = $this->connexion->query("SELECT id FROM movies WHERE title = '$title'");
        if (empty($statement)) {
            throw new \Exception("Movie does not exist.");
        }

        return true;
    }

    private function movieNotInWatchlist(string $title, string $username): bool
    {
        $movieId = $this->getIdByValue("movies", "title", $title);
        $userId = $this->getIdByValue("users", "username", $username);
        $statement = $this->connexion->query("SELECT movie_id FROM watchlist WHERE movie_id = $movieId AND user_id = $userId");
        if (!empty($statement)) {
            throw new \Exception("This movie is already in watchlist.");
        }

        return true;
    }
    private function userCanAddMovie(string $username, string $title): bool
    {
        if (!$this->movieExist($title)){
            return false;
        }

        if (!$this->movieNotInWatchlist($username, $title)){
            return false;
        }

        return true;
    }

    protected function userAddMovie(string $username, string $title)
    {
        if (!$this->userCanAddMovie($username, $title)) {
            die();
        }

        $movieId = $this->getIdByValue("movies", "title", $title);
        $userId = $this->getIdByValue("users", "username", $username);

        $statement = $this->connexion->prepare("INSERT INTO watchlist (movie_id, user_id) VALUES (:title, :user)");
        $statement->bindValue(':title', $movieId);
        $statement->bindValue(':user', $userId);
        $statement->execute();
    }

    /* ===================================================================== For display Movies ===================================================================== */

    private function compareTableWithAllowedTable(string $table): bool
    {
        if (!in_array($table, self::ALLOWED_TABLE)){
            throw new \Exception("Invalid table.");
        }

        return true;
    }
    private function showAllFromTable(string $table)
    {
        $statement = $this->connexion->prepare("SELECT * FROM '$table'");
    }
    //User va demander les films dans bdd suivant tag ou categorie -> cad que depuis les tags on va afficher tous les films -> On va récupérer les tags . (Select * from table Where column = :column) ensuite on fetch all
}