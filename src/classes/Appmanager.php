<?php

namespace classes;

use AllowDynamicProperties;
use PDOException;

require_once "Database.php";
require_once "Homepage.php";
require_once "Loginpage.php";
require_once "Registerpage.php";
require_once "Page.php";

#[AllowDynamicProperties] class Appmanager

{
    public function __construct(){
        $this->data = new Database();
        $this->home = new Homepage();
        $this->login = new Loginpage();
        $this->register = new Registerpage();
        $this->page = new Page();
    }

/* ===================================================================== User Interaction ===================================================================== */

    public function logout(): void
    {
        echo "yes";
        session_start();
        session_destroy();
        setcookie("username", "", time() - 3600);
        header("Location: main-website.php");
        exit;
    }

    public function adminAddMovie(): void
    {
        $username = $this->home->getUsername();
        $movieData = $this->home->getMovieData();
        $title = $movieData[0];
        $pictureUrl = $movieData[1];

        if ($this->data->userIsAdmin($username)){
            $this->data->adminAddMovieOnDatabase($title, $pictureUrl);
        }
    }

    public function adminDeleteMovie(): void
    {
        $username = $this->home->getUsername();
        $movieData = $this->home->getMovieData();
        $title = $movieData[0];
        $pictureUrl = $movieData[1];

        if ($this->data->userIsAdmin($username)){
            $this->data->deleteMovie($title, $username);
        }
    }

    public function adminSetUserToAdmin(): void
    {
        try{
            $selfUsername = $this->home->getUsername();
            if (!$this->data->userIsAdmin($selfUsername)){
                return;
            }

            $targetUsername = $this->home->getTargetUsername();

            if (empty($targetUsername)){
                return;
            }

            $this->data->changeUserToAdmin($targetUsername);
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }


    public function handleCreateAccount(): void
    {
        try {
            $this->register->createUser();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function handleUserConnectAfterCreate(): void
    {
        try{
            $this->register->userConnectAfterCreation();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function handleLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
            try {
                $this->login->login();

            } catch (PDOException $e) {
                echo $e->getMessage();

            }
        }
    }

    public function handleUserSession(): void
    {
        try{
            $this->page->redirect();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function handleCookieNewUser(): void
    {
        try{
            if ($this->page->sessionIsSet() == false){
                $this->page->setCookie();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function handleUserWatchlistForEdit(): void
    {
        if (isset($_GET['movie']) && isset($_GET['action'])) {
            $title = $_GET['title'];
            $action = $_GET['action'];
            $username = $this->home->getUsername();

            try {
                if ($action == 'add') {
                    $this->data->userAddMovieOnWatchlist($username, $title);
                } elseif ($action == 'remove') {
                    $this->data->userDeleteMovieOnWatchlist($username, $title);
                }

                header('Location: homepage-website.php');
                exit;
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

/* ===================================================================== display Task ===================================================================== */

    public function displayType(): string
    {
        try{
            return $this->home->htmlTypeOrTag("type");
        }catch (PDOException $e){
            echo $e->getMessage();
            return "";
        }
    }

    public function displayTag(): string
    {
        try{
            return $this->home->htmlTypeOrTag("tag");
        }catch (PDOException $e){
            echo $e->getMessage();
            return "";
        }
    }

    public function displaySearchMovie() : string
    {
        try{
            $getMethod = $this->home->getSearchRequest();
            $sqlData = $this->data->displaySpecificRequest($getMethod[0], $getMethod[1],$getMethod[2]);
            $htmlOutput = "";

            foreach ($sqlData as $movie) {
                $movieId = $this->data->getIdByValue("movies", "title", $movie["title"]);
                $htmlOutput .= $this->home->displayMoviesAllowedToWatchlist($movie["title"], $movie["picture_url"], $movieId);
            }

            return $htmlOutput;

        } catch (PDOException $e) {
            echo $e->getMessage();
            return "";
        }
    }

    public function displayWatchlist(): string
    {
        try{
            $username = $this->home->getUsername();
            $watchlist = $this->data->showUserWatchlist($username);
            if (!empty($watchlist)){
                return $this->home->displayMovies($watchlist);
            }

            return "";
        } catch (PDOException $e) {
            echo $e->getMessage();
            return "";
        }
    }
}