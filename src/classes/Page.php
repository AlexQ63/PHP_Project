<?php

namespace classes;

require_once 'Database.php';
class Page
{
    protected $username;
    protected $data;

    public function __construct($username){
        $this->username = $username;
        $data = new Database();
    }

    public function getUsername()
    {
        return $this->username;
    }


    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    protected function usernameExist($username): bool
    {
        if ($this->data.$this->usernameExist($username)) {
            return true;
        } return false;
    }

}
