<?php

namespace classes;

use AllowDynamicProperties;

require_once 'Page.php';
require_once 'Database.php';

#[AllowDynamicProperties] class Loginpage extends Page
{

    public function __construct()
    {
        $this->data = new Database();
        $this->page = new Page();
    }

    protected function getLogin(): array
    {
        if (!$this->page->getRequestPost()) {
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

    public function setUserCookie(string $username): void
    {
        $cookieTime = $this->defineCookieTime();
        setcookie("username", $username, time() + $cookieTime);
    }

    public function login(): void
    {
        $dataConnexion = $this->getLogin();

        if ($this->data->userCanLogin($dataConnexion[0], $dataConnexion[1])){
            session_start();
            $_SESSION['username'] = $dataConnexion[0];
            $this->setUserCookie($dataConnexion[0]);
            header("Location: homepage-website.php");
            exit;
        }
    }
}
