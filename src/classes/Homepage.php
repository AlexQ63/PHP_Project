<?php

namespace classes;

use AllowDynamicProperties;
use mysql_xdevapi\Exception;

require_once 'database.php';
require_once 'Page.php';
#[AllowDynamicProperties] class Homepage extends Page

{
    public function __construct()
    {
        $this->data = new Database();
        $this->page = new Page();
    }

    public function getUsername(): string
    {
        return $_SESSION['username'];
    }

    public function getMovieData(): array
    {
        if (!$this->page->getRequestGet()){
            throw new Exception("Page request get not found");
        }

        $title = $_GET["title"];
        $pictureUrl = $_GET["picture_url"];

        return [$title, $pictureUrl];
    }

    public function getTargetUsername(): string
    {
        return $this->page->getRequestPost() ? $_POST["target-username"] : "";
    }

    protected function stringToHtml(string $string): string
    {
        return htmlspecialchars($string);
    }

    public function redirectIfNoLogin(): void
    {
        $result = isset($_SESSION['username']);
        if (empty($result)) {
            header("location: main-website.php");
            exit;
        }
    }

    public function getSearchRequest(): array
    {
        if (!$this->page->getRequestGet()){
            throw new Exception("You can't use this form because it's not a get resquest");
        }

        $title = $_GET['title'] ?? '';
        $tags = isset($_GET['tag']) ? (array)$_GET['tag'] : [];
        $types = isset($_GET['type']) ? (array)$_GET['type'] : [];

        $tags = array_map('strval', array_filter($tags, 'is_scalar'));
        $types = array_map('strval', array_filter($types, 'is_scalar'));

        return [$title, $tags, $types];
    }

    public function displayMovies(array $movies): string
    {
        $html = '';
        foreach ($movies as $movie) {
            $title = htmlspecialchars($movie['title']);
            $pictureUrl = htmlspecialchars($movie['picture_url']);

            $html .= "<div class='movie'>";
            $html .= "<h3>{$title}</h3>";
            $html .= "<img src='assets/website-picture/{$pictureUrl}' alt='{$title}'>";
            $html .= "</div>";
        }
        return $html;
    }

    public function displayMoviesAllowedToWatchlist(string $title, string $pictureUrl, int $movieId): string
    {
        $htmlTitle = $this->stringToHtml($title);
        $htmlPicture = $this->stringToHtml($pictureUrl);
        $linkTitle = urlencode($title);
        $linkPicture = urlencode($pictureUrl);

        return "
        <div class='movie'>
            <h2>$htmlTitle</h2>
            <img src='assets/website-picture/$htmlPicture' alt='$htmlTitle'>
            <p><a href='?movie=$movieId&title=$linkTitle&picture_url=$linkPicture&action=add'>Add to watchlist</a></p>
            <p><a href='?movie=$movieId&title=$linkTitle&picture_url=$linkPicture&action=remove'>Remove from watchlist</a></p>
        </div>
    ";
    }

    public function htmlTypeOrTag(string $table): string
    {
        $data = $this->data->showAllTypeOrTag($table);
        $htmlOutput = "";

        $inputName = ($table === 'tag') ? 'tag' : 'type';

        foreach ($data as $displayData) {
            $htmlData = $this->stringToHtml($displayData['description']);
            $htmlOutput .= "
                <label>
                    <input type='checkbox' name='{$inputName}[]' value='{$htmlData}'> {$htmlData}
                </label><br>
                ";
        }

        return $htmlOutput;
    }
}
