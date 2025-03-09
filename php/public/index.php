<?php
session_start();
require '../config/config.php';

// Controllo se l'utente è già autenticato
if (isset($_SESSION['email'])) {
    // OK, reindirizzo alla home
    redirect(
        true,
        "Ti sei già autenticato correttamente.",
        "../public/home.php"
    );
} else {
    // NO, reindirizzo al login
    header("Location: ../public/login.php");
}
exit;