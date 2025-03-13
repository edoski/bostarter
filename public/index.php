<?php
session_start();
require '../config/config.php';

// Controlla se l'utente è già autenticato
if (isset($_SESSION['email'])) {
    // OK, reindirizzo alla home
    header("Location: home.php");
    exit;
} else {
    // NO, reindirizzo al login
    header("Location: login.php");
    exit;
}