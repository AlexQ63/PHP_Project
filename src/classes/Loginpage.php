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
        $remember = $_POST['remember'];

        return [$username, $password, $remember];
    }

    private function defineCookieTime(): int
    {
        $data = $this->getLogin();
        if ($data[2] === 'on') {
            $time = 86400 * 30;
            return $time;
        }
        return 86400;
    }

    private function setUserCookie(string $username): void
    {
        $cookieTime = $this->defineCookieTime();
        setcookie("username", $username, time() + $cookieTime);
    }

    public function login(): void
    {
        try {
            $dataConnexion = $this->getLogin();
            $username = $dataConnexion[0];
            $password = $dataConnexion[1];

            if ($this->data->userCanLogin($username, $password)){
                session_start();
                $this->setUserCookie($username);
                header("Location: /homepage-website.php");
                exit();
            }
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }
}
