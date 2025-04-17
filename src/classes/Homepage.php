<?php

namespace classes;

use mysql_xdevapi\Exception;

require_once 'database.php';
class Homepage extends Page
{
    public function __construct()
    {
        $this->data = new Database();
        $this->page = new Page();
    }

    protected function getUsername(): string
    {
        return $_SESSION['username'];
    }

    protected function getMovieData(): array
    {
        if (!$this->page->getRequestGet()){
            throw new Exception("Page request get not found");
        }

        $title = $_GET["title"];
        $pictureUrl = $_GET["picture_url"];

        return [$title, $pictureUrl];
    }

    public function addMoviesInWatchlist(): void
    {
        $data = $this->getMovieData();
        $title = $data[0];
        $username = $this->getUsername();
        $this->data->userAddMovieOnWatchlist($username, $title);
    }

    public function adminAddMovie(): void
    {
        $username = $this->getUsername();
        $movieData = $this->getMovieData();
        $title = $movieData[0];
        $pictureUrl = $movieData[1];

        if ($this->data->userIsAdmin($username)){
            $this->data->adminAddMovieOnDatabase($title, $pictureUrl);
        }
    }

    public function adminDeleteMovie(): void
    {
        $username = $this->getUsername();
        $movieData = $this->getMovieData();
        $title = $movieData[0];
        $pictureUrl = $movieData[1];

        if ($this->data->userIsAdmin($username)){
            $this->data->deleteMovie($title, $username);
        }
    }

/* ===================================================================== Search form ===================================================================== */

    protected function getSearchRequest(): array
    {
        if (!$this->page->getRequestGet()){
            throw new Exception("You can't use this form because it's not a get resquest");
        }

        $title = $_GET["title"];
        $tag = $_GET["tag"];
        $type = $_GET["type"];

        return [$title, $tag, $type];
    }



    //requête pour la recherche
    //Tu vas construire progressivement ta requête SQL en fonction des champs fournis, comme un puzzle :
    //    Ensuite, tu ajoutes des morceaux à cette requête selon les champs remplis
    //        Si le titre est rempli, tu ajoutes une condition AND titre LIKE
    //        Si le tag est rempli, tu ajoutes une condition AND tag = ...
    //        Idem pour le type
}

/*
-Page de l'user connecté :
    -Récupère les données du formulaire d'affichage de film suivant types ou tag
    -Créer un comportement avec le logout qui va détruire le comportement de la page de connexion.
    -Upload de media
    -Pagination à faire


Les formulaires à avoir :
    -1 qui regarde les titres de film et les affiche.
    -1 qui regarde
        Comment gérer la création de compte d'un admin ? Est-ce que j'ai besoin de créer ne condition pour qu'un user est admin ou non ? Je peux juste gérer le fait qu'un admin peut mettre un autre utilisateur comme admin. Je n'ai donc pas besoin de le gérer ici, mais depuis un formulaire de la homepage admin et faire une modification de la table.
    Il va y avoir une autre page de créer où son contenu va être gérer dynamiquement : Suivant l'user, s'il a ajouté des films ou non, il y avoir un affichage particulier en rapport avec les films qu'il a ajouté et s'il est admin. Cependant, je n'aurais pas besoin de gérer ça via une classe mais avec une vérification si le nom de l'user (numéro unique) match avec la BDD -> idem pour la partie Admin.

La page d'accueil de l'user (pas encore créer au moment de cet écris) va créer des instances de movies suivant les informations qu'il va remplir depuis sa page internet (export local d'image, donne le nom d'un film) et l'user va remplir sa watchlist. */