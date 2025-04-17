<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log In</title>
    <link rel="stylesheet" href="assets/css/login-style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a class="navbar-logo" href="main-website.php">
                    <img class="logo" src="assets/website-picture/Logo.webp">
                </a>
                <ul class="navbar-homepage">
                    <li>
                        <a class="nav-link" href="main-website.php">My Homepage</a>
                    </li>
                    <li>
                        <a class="nav-link" href="register-website.php">Sign Up</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <section class="main-page">
        <div class="modify-pic">
            <img src="assets/website-picture/login-main-pic.webp" class="main-picture">
        </div>
    </section>
    <section class="register-form">
        <form method="post">
            <input type="text" name="username" placeholder="Username" class="user">
            <input type="password" name="password" placeholder="Password" class="user">
            <label for="remember_me" class="remember">Remember Me<input type="checkbox" name="remember" id="remember_me" class="checkbox"></label>
            <input type="submit" value="Submit" class="submit">
        </form>
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
            <div class="list">
                <nav class="end-navbar">
                    <ul>
                        <li>
                            <img src="assets/website-picture/homepage.webp">
                            <a href="main-website.php">Homepage</a>
                        </li>
                        <li>
                            <img src="assets/website-picture/add.webp">
                            <a href="register-website.php">Create Account</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </section>
</footer>
</html>