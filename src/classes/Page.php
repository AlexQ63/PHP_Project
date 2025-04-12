<?php

namespace classes;

use mysql_xdevapi\Exception;

class Page
{
    protected function __construct()
    {
        $this->setCookie();
    }

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
}

