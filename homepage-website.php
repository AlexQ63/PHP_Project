<?php

use classes\Appmanager;
use classes\Database;
use classes\Homepage;
use classes\Page;

require_once "src/classes/Appmanager.php";
require_once "src/classes/Page.php";
require_once "src/classes/Homepage.php";
require_once "src/classes/Database.php";

$home = new Homepage();
$app = new Appmanager();
$page = new Page();
$data = new Database();

session_start();
$app->handleUserWatchlistForEdit();

$home->redirectIfNoLogin();

$_SESSION['has_right'] = $data->userIsAdmin($home->getUsername());

$home->setUserToAdmin();
?>

<head>
    <meta charset="UTF-8">
    <title>My Web-Watchlist</title>
    <link rel="stylesheet" href="assets/css/homepage-style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a class="navbar-logo" href="homepage-website.php">
                    <img class="logo" src="assets/website-picture/Logo.webp">
                </a>
                <ul class="navbar-homepage">
                    <li>
                        <a class="nav-link" href="#">My Homepage</a>
                    </li>
                    <form action="logout.php" method="POST" name="logout">
                        <button type="submit" class="link-style">Disconnect </button>
                    </form>
                </ul>
            </div>
        </nav>
    </header>
    <section class="main-page">
        <p class="welcome"> Welcome - <span><?= $home->getUsername();?></span></p>
        <div class="container">
            <div class="display-watch">
                <?= $app->displayWatchlist(); ?>
            </div>
            <form method="get" class="search-form">
                <label for="title">Title : <br><input type="text" id="title" name="title" placeholder="Inception"></label>
                <div class="column">
                    <fieldset>
                        <legend>Types</legend>
                        <?= $app->displayTag(); ?>
                    </fieldset>

                    <fieldset>
                        <legend>Tags</legend>
                        <?= $app->displayType(); ?>
                    </fieldset>
                </div>
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="search">
            <?php
            if (!empty($_GET['title']) || !empty($_GET['type']) || !empty($_GET['tag'])) {
                $result = $app->displaySearchMovie();
                echo $result !== "" ? $result : "<p>No movies found</p>";
            }
            ?>
        </div>
    </section>
    <?php if ($_SESSION['has_right']){ ?>
    <section class="form-admin">
        <div class="admin-user">
            <form method="post">
                <table>
                    <thead>
                    <tr>
                        <th>Username</th>
                        <th>IsAdmin</th>
                        <th>Modify content</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data->adminGetUser() as $user) : ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= $user['has_right'] ? 'Admin' : 'Utilisateur' ?></td>
                            <td>
                                <select name="admin-user[<?= $user['username'] ?>]">
                                    <option value="0" <?= $user['has_right'] ? '' : 'selected' ?>>Lambda user</option>
                                    <option value="1" <?= $user['has_right'] ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <button type="submit" name="target-username" value="<?= $user['username']; ?>">Applicate</button>
            </form>
        </div>
    </section>
    <?php } ?>
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
                            <a href="homepage-website.php">Homepage</a>
                        </li>
                        <li>
                            <img src="assets/website-picture/disconnect.webp">
                            <form action="logout.php" method="POST" name="logout">
                                <button type="submit" class="link-style">Disconnect </button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </section>
</footer>
</html>