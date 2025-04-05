<?php

namespace classes;

use PDO;
use PDOException;

class Database
{
    protected PDO $connexion;
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

    private function passwordIsValid(string $password): bool
    {

        // ^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])\S{8,}$   (regex a utilisé pour la force des mdp)
    }
    protected function usernameExist(string $username): bool
    {
        $result = $this->connexion->query("SELECT '$username' FROM users");
        if (empty($result)) {
            return false;
        } return true;
    }

    private function canCreateUser(string $username, string $password): bool
    {
        if (!$this->usernameExist($username)) {
            return false;
        }

        if
    }

}

/* Un utilisateur doit pouvoir ajouter un film à sa watchlist suivant
 */