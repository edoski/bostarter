<?php
// === CONFIG ===
require '../config/config.php';

// === ACTION ===
// Server-side logout: destroy session
session_start();
session_unset();
session_destroy();

// Client-side logout: delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Success, redirect alla pagina di login
redirect(
    true,
    "Logout effettuato correttamente.",
    "../public/login.php"
);