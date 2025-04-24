<?php

namespace classes;

use AllowDynamicProperties;

require_once 'Database.php';
require_once 'Loginpage.php';

#[AllowDynamicProperties] class Registerpage extends Page
{
    public function __construct()
    {
        $this->data = new Database();
        $this->page = new Page();
        $this->loginpage = new Loginpage();
    }

    protected function getRegister(): array
    {
        if (!$this->page->getRequestPost()) {
            throw new \Exception("Wrong request method.");
        }

        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $birthDate = $_POST['birthdate'];

        return [$firstname, $lastname, $username, $password, $birthDate];
    }

    public function createUser(): void
    {
        $dataCreation = $this->getRegister();
        $this->data->createUser($dataCreation[0], $dataCreation[1], $dataCreation[2], $dataCreation[3], $dataCreation[4]);
    }

    public function userConnectAfterCreation(): void
    {
        $dataCreation = $this->getRegister();
        if ($this->data->userCanLogin($dataCreation[2],$dataCreation[3])){
            session_start();
            $_SESSION['username'] = $dataCreation[2];
            $this->loginpage->setUserCookie($dataCreation[2]);
            header("Location: homepage-website.php");
            exit;
        }
    }
}
