<?php

namespace classes;

use mysql_xdevapi\Exception;

class Page
{
    private function generateCookieName(): string
    {
        $binary = ramdom_bytes(8);
        return bin2hex($binary);
    }

    protected function setCookie(): bool
    {
        $hex = $this->generateCookieName();
        if (!setcookie("visitor_id", $hex, time() + (3600))){
            throw new \Exception("Cookie visitor_id not set");
        }

        return true;
    }

    protected function sessionIsSet(): bool
    {
        return isset($_SESSION['"username']);
    }

    public function redirect(): void
    {
        if (!$this->sessionIsSet()) {
            die();
        }

        header('Location: homepage-website.php');
        exit();
    }

    protected function getRequestPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    protected function getRequestGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }
}