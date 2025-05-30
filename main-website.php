<?php

use classes\Appmanager;

require_once "src/classes/Appmanager.php";

$app = new Appmanager();

$app->handleCookieNewUser();
$app->handleUserSession();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Web-Watchlist</title>
    <link rel="stylesheet" href="assets/css/main-style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a class="navbar-logo" href="#">
                    <img class="logo" src="assets/website-picture/Logo.webp">
                </a>
                <ul class="navbar-homepage">
                    <li>
                        <a class="nav-link" href="#">My Homepage</a>
                    </li>
                    <li>
                        <a class="nav-link" href="register-website.php">Create Account</a>
                    </li>
                    <li>
                        <a class="nav-link" href="login-website.php">Log In</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <section class="main-page">
        <div class="container">
            <div class="modify-pic">
                <img src="assets/website-picture/Main-picture.webp" class="main-picture">
            </div>
            <div class="inside">
                <span class="bigger-text">Welcome to the first website where you can watch all the movies you have already seen<img src="assets/website-picture/movie-icon.webp" class="movie-logo"></span>
                <div class="text-pic">
                    <div class="pic">
                        <div class="column1">
                            <img src="assets/website-picture/Novocaine.webp" class="resize">
                            <img src="assets/website-picture/Berlin Ete 42.webp" class="resize">
                            <img src="assets/website-picture/Aladin.webp" class="resize">
                            <img src="assets/website-picture/Flow.webp" class="resize">
                        </div>
                        <div class="column2">
                            <img src="assets/website-picture/Les Bodins.webp" class="resize">
                            <img src="assets/website-picture/Le Roi Lion.webp" class="resize">
                            <img src="assets/website-picture/Mercato.webp" class="resize">
                            <img src="assets/website-picture/Malcolm X.webp" class="resize">
                        </div>
                        <div class="column3">
                            <img src="assets/website-picture/Avatar.webp" class="resize">
                            <img src="assets/website-picture/Exodus.webp" class="resize">
                            <img src="assets/website-picture/Le Secret de Kheops.webp" class="resize">
                            <img src="assets/website-picture/Inestimable.webp" class="resize">
                        </div>
                    </div>
                    <div class="text-and-link">
                        <span class="regular-text">A watchlist website is an online platform that allows users to track movies and TV shows they want to watch or have already seen. It helps users organize their entertainment preferences, discover new content, and keep track of their progress. With this watchlist, users can save movies and series they plan to watch, mark titles as "watched" to keep track of their viewing history, receive recommendations based on their interests, and share their lists with friends or explore popular trends. A watchlist website enhances the viewing experience by making it easier to manage and discover great content! <br> </span>
                        <span>You want to join the community? Just create your <a href="register-website.php" class="text"> account.</a>  Already on board? <a href="login-website.php" class="text"> Log in!</a></span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
<footer>
    <section class="main-footer">
        <div class="content">
            <div class="copyright">
                <img src="assets/website-picture/copyright.webp">
                <p>© 2025 My Web-Watchlist. All rights reserved.
                    This site and its content are protected by copyright and intellectual property laws. Any reproduction, distribution, or use without written permission is prohibited.</p>
            </div>
            <div class="resume">
                <img src="assets/website-picture/about.webp">
                <p> My Web-Watchlist helps you keep track of your favorite movies and TV shows. Easily add, organize, and manage your personal entertainment collection in one place. </p>
            </div>
            <div class="list">
                <nav class="end-navbar">
                    <ul>
                        <li>
                            <img src="assets/website-picture/homepage.webp">
                            <a href="#">Homepage</a>
                        </li>
                        <li>
                            <img src="assets/website-picture/add.webp">
                            <a href="register-website.php">Create Account</a>
                        </li>
                        <li>
                            <img src="assets/website-picture/log-in.webp">
                            <a href="login-website.php">Log In</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </section>
</footer>
</html>