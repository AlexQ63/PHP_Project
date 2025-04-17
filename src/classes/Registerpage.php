<?php

namespace classes;

require_once 'Database.php';
require_once 'Loginpage.php';

class Registerpage extends Page
{
    public function __construct()
    {
        $this->data = new Database();
        $this->page = new Page();
        $this->loginpage = new Loginpage();
    }

    private function getRegister(): array
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

    public function userGetConnected(): void
    {
        $dataCreation = $this->getRegister();
        if ($this->data->userCanLogin($dataCreation[2],$dataCreation[3])){
            session_start();
            $this->loginpage->setUserCookie($dataCreation[2]);
            header("Location: /homepage-website.php");
            exit();
        }
    }


}
