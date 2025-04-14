<?php

namespace classes;

require_once 'Page.php';
require_once 'Database.php';

class Loginpage extends Page
{

    public function __construct()
    {
        $this->data = new Database();
        parent::__construct();
    }

    private function getRequestPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    private function getLogin(): array
    {
        if (!$this->getRequestPost()) {
            throw new \Exception("Wrong request method.");
        }
        $username = $_POST['username'];
        $password = $_POST['password'];

        return [$username, $password];
    }

    private function getRememberMe(): array
    {
        if (!$this->getRequestPost()) {
            throw new \Exception("Wrong request method - Remember Me Error.");
        }

        return $_POST['remember'];
    }

    public function login(): void
    {
        try {
            $dataConnexion = $this->getLogin();
            $username = $dataConnexion[0];
            $password = $dataConnexion[1];

            if (!$this->data->userCanLogin($username, $password)){
                session_start();
                
            }

        } catch (\PDOException $e) {

        }



    }

        //Le login ici va récup les 2 données rentrés et regarder s'il peut se connecter

}
//      -Page de connexion :
//        -Récupère 2 datas
//        -Créer un comportement : Création d'une session et potentiellement d'un cookie permanent - Set un nouveau cookie par rapport à celui créer par défaut ?


//function isConnected(): bool
//{
//    return getCurrentUsername() !== null;
//}
//
//function getCurrentUsername(): ?string
//{
//    return $_SESSION['username'] ?? $_COOKIE['username'] ?? null;
//}
//
//function login($username, $password): void
//{
//    global $user;
//
//    if ($user['username'] !== $username) {
//        return;
//    }
//
//    if ($user['password'] !== $password) {
//        return;
//    }
//
//    $_SESSION['username'] = $username;
//}
//
//function rememberMe(string $username): void
//{
//    setcookie('username', $username, time() + 3600 * 24 * 30);
//}